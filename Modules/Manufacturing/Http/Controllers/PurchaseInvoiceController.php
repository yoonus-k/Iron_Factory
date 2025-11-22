<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Material;
use App\Models\Unit;
use App\Services\NotificationService;
use Modules\Manufacturing\Traits\LogsOperations;
use App\Traits\StoresNotifications;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    use LogsOperations, StoresNotifications;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the purchase invoices
     */
    public function index(Request $request)
    {
        $query = PurchaseInvoice::with(['supplier', 'recordedBy', 'approvedBy']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('invoice_reference_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->get('supplier_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->get('date_to'));
        }

        $invoices = $query->paginate(15);
        $suppliers = Supplier::all();

        return view('manufacturing::warehouses.purchase-invoices.index', compact('invoices', 'suppliers'));
    }

    /**
     * Show the form for creating a new invoice
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $users = User::all();
        $materials = Material::all();
        $units = Unit::all();

        // Generate automatic invoice number
        $lastInvoice = PurchaseInvoice::orderBy('id', 'desc')->first();
        $nextNumber = $lastInvoice ? $lastInvoice->id + 1 : 1;
        $invoiceNumber = 'PI-' . date('Y') . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return view('manufacturing::warehouses.purchase-invoices.create', compact('suppliers', 'users', 'materials', 'units', 'invoiceNumber'));
    }

    /**
     * Store a newly created invoice in storage
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'invoice_number' => 'required|string|unique:purchase_invoices,invoice_number',
                'invoice_reference_number' => 'nullable|string|max:100',
                'supplier_id' => 'required|exists:suppliers,id',
                'invoice_date' => 'required|date',
                'due_date' => 'nullable|date|after_or_equal:invoice_date',
                'currency' => 'required|string|max:3',
                'payment_terms' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
                'is_active' => 'nullable|boolean',
                // Items validation
                'items' => 'required|array|min:1',
                'items.*.material_id' => 'nullable|exists:materials,id',
                'items.*.item_name' => 'nullable|string|max:255',
                'items.*.description' => 'nullable|string|max:500',
                'items.*.quantity' => 'required|numeric|min:0.001',
                'items.*.unit' => 'required|string|max:50',
                'items.*.weight' => 'nullable|numeric|min:0',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
                'items.*.discount_rate' => 'nullable|numeric|min:0|max:100',
                'items.*.notes' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $validated['recorded_by'] = Auth::id() ?? 1;
            $validated['created_by'] = Auth::id() ?? 1;
            $validated['status'] = 'draft';
            $validated['is_active'] = $request->boolean('is_active', true);

            // Calculate total from items
            $totalAmount = 0;
            $totalQuantity = 0;
            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $taxAmount = $subtotal * (($item['tax_rate'] ?? 0) / 100);
                $discountAmount = $subtotal * (($item['discount_rate'] ?? 0) / 100);
                $total = $subtotal + $taxAmount - $discountAmount;
                $totalAmount += $total;

                // Calculate total quantity as weight for invoice
                $totalQuantity += (float)($item['quantity'] ?? 0);
            }

            $validated['total_amount'] = round($totalAmount, 2);
            $validated['weight'] = round($totalQuantity, 3);
            $validated['weight_unit'] = 'وحدة';

            // Ensure all required fields are present
            if (empty($validated['created_by'])) {
                throw new \Exception('فشل في تحديد المستخدم الحالي. الرجاء تسجيل الدخول مرة أخرى.');
            }

            // Remove items from validated data before creating invoice
            $itemsData = $validated['items'];
            unset($validated['items']);

            $invoice = PurchaseInvoice::create($validated);

            // Create invoice items
            foreach ($itemsData as $itemData) {
                $itemData['purchase_invoice_id'] = $invoice->id;
                PurchaseInvoiceItem::create($itemData);
            }

            DB::commit();

            // ✅ تخزين الإشعار
            $this->storeNotification(
                'purchase_invoice_created',
                'تم إنشاء فاتورة شراء جديدة',
                'تم إنشاء فاتورة شراء رقم ' . $invoice->invoice_number . ' مع ' . count($itemsData) . ' منتج - المبلغ: ' . number_format($invoice->total_amount, 2),
                'success',
                'fas fa-file-invoice',
                route('manufacturing.purchase-invoices.show', $invoice->id)
            );

            // Log the operation
            try {
                $this->logOperation(
                    'create',
                    'Create Purchase Invoice',
                    'تم إنشاء فاتورة شراء جديدة: ' . $invoice->invoice_number . ' مع ' . count($itemsData) . ' منتج',
                    'purchase_invoices',
                    $invoice->id,
                    null,
                    $invoice->toArray()
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log invoice creation: ' . $logError->getMessage());
            }

            // إرسال الإشعارات لجميع المستخدمين الآخرين
            try {
                $users = User::where('id', '!=', Auth::id())->get();
                foreach ($users as $user) {
                    $this->notificationService->notifyPurchaseInvoiceCreated(
                        $user,
                        $invoice,
                        Auth::user()
                    );
                }
            } catch (\Exception $notifError) {
                Log::warning('Failed to send invoice creation notifications: ' . $notifError->getMessage());
            }

            return redirect()->route('manufacturing.purchase-invoices.show', $invoice->id)
                           ->with('success', 'تم إضافة الفاتورة بنجاح مع ' . count($itemsData) . ' منتج');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating purchase invoice: ' . $e->getMessage());

            // Provide user-friendly error message
            $errorMessage = 'فشل في حفظ الفاتورة. ';
            if (str_contains($e->getMessage(), 'created_by')) {
                $errorMessage .= 'الرجاء التحقق من بيانات المستخدم.';
            } else {
                $errorMessage .= 'الرجاء التحقق من المعلومات المدخلة.';
            }

            return redirect()->back()
                           ->withInput()
                           ->withErrors(['error' => $errorMessage]);
        }
    }

    /**
     * Show the specified invoice
     */
    public function show($id)
    {
        try {
            $invoice = PurchaseInvoice::with(['supplier', 'recordedBy', 'approvedBy', 'operationLogs', 'items.material'])->findOrFail($id);
            return view('manufacturing::warehouses.purchase-invoices.show', compact('invoice'));
        } catch (\Exception $e) {
            return redirect()->route('manufacturing.purchase-invoices.index')
                           ->withErrors(['error' => 'الفاتورة غير موجودة']);
        }
    }

    /**
     * Show the form for editing the invoice
     */
    public function edit($id)
    {
        try {
            $invoice = PurchaseInvoice::with('items')->findOrFail($id);
            $suppliers = Supplier::all();
            $users = User::all();
            $materials = Material::where('is_active', true)->get();
            $units = Unit::where('is_active', true)->get();

            return view('manufacturing::warehouses.purchase-invoices.edit', compact('invoice', 'suppliers', 'users', 'materials', 'units'));
        } catch (\Exception $e) {
            return redirect()->route('manufacturing.purchase-invoices.index')
                           ->withErrors(['error' => 'الفاتورة غير موجودة']);
        }
    }

    /**
     * Update the specified invoice in storage
     */
    public function update(Request $request, $id)
    {
        try {
            $invoice = PurchaseInvoice::with('items')->findOrFail($id);
            $oldValues = $invoice->toArray();

            $validated = $request->validate([
                'invoice_number' => 'required|string|unique:purchase_invoices,invoice_number,' . $id,
                'invoice_reference_number' => 'nullable|string|max:100',
                'supplier_id' => 'required|exists:suppliers,id',
                'invoice_date' => 'required|date',
                'due_date' => 'nullable|date|after_or_equal:invoice_date',
                'currency' => 'required|string|max:3',
                'payment_terms' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
                'is_active' => 'nullable|boolean',
                // Items validation
                'items' => 'required|array|min:1',
                'items.*.material_id' => 'nullable|exists:materials,id',
                'items.*.item_name' => 'nullable|string|max:255',
                'items.*.description' => 'nullable|string|max:500',
                'items.*.quantity' => 'required|numeric|min:0.001',
                'items.*.unit' => 'required|string|max:50',
                'items.*.weight' => 'nullable|numeric|min:0',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
                'items.*.discount_rate' => 'nullable|numeric|min:0|max:100',
                'items.*.notes' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $validated['is_active'] = $request->boolean('is_active', true);

            // Calculate total from items
            $totalAmount = 0;
            $totalQuantity = 0;
            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $taxAmount = $subtotal * (($item['tax_rate'] ?? 0) / 100);
                $discountAmount = $subtotal * (($item['discount_rate'] ?? 0) / 100);
                $total = $subtotal + $taxAmount - $discountAmount;
                $totalAmount += $total;

                // Calculate total quantity as weight for invoice
                $totalQuantity += (float)($item['quantity'] ?? 0);
            }

            $validated['total_amount'] = round($totalAmount, 2);
            $validated['weight'] = round($totalQuantity, 3);
            $validated['weight_unit'] = 'وحدة';

            // Ensure all required fields are present
            if (empty($validated['created_by'])) {
                $validated['created_by'] = Auth::id() ?? 1;
            }

            // Remove items from validated data before updating invoice
            $itemsData = $validated['items'];
            unset($validated['items']);

            $invoice->update($validated);

            // Delete old items and create new ones
            $invoice->items()->delete();
            foreach ($itemsData as $itemData) {
                $itemData['purchase_invoice_id'] = $invoice->id;
                PurchaseInvoiceItem::create($itemData);
            }

            DB::commit();

            $newValues = $invoice->fresh()->toArray();

            // ✅ تخزين الإشعار
            $this->storeNotification(
                'purchase_invoice_updated',
                'تم تحديث فاتورة شراء',
                'تم تحديث فاتورة الشراء رقم ' . $invoice->invoice_number . ' - المبلغ: ' . number_format($invoice->total_amount, 2),
                'warning',
                'fas fa-edit',
                route('manufacturing.purchase-invoices.show', $invoice->id)
            );

            // Log the operation
            try {
                $this->logOperation(
                    'update',
                    'Update Purchase Invoice',
                    'تم تحديث فاتورة الشراء: ' . $invoice->invoice_number . ' مع ' . count($itemsData) . ' منتج',
                    'purchase_invoices',
                    $invoice->id,
                    $oldValues,
                    $newValues
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log invoice update: ' . $logError->getMessage());
            }

            // إرسال الإشعارات بتحديث الفاتورة
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'تحديث فاتورة شراء',
                        'تم تحديث فاتورة الشراء: ' . $invoice->invoice_number . ' مع ' . count($itemsData) . ' منتج',
                        'update_purchase_invoice',
                        'info',
                        'feather icon-edit',
                        route('manufacturing.purchase-invoices.show', $invoice->id)
                    );
                }
            } catch (\Exception $notifError) {
                Log::warning('Failed to send invoice update notifications: ' . $notifError->getMessage());
            }

            return redirect()->route('manufacturing.purchase-invoices.show', $invoice->id)
                           ->with('success', 'تم تحديث الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating purchase invoice: ' . $e->getMessage());

            // Provide user-friendly error message
            $errorMessage = 'فشل في تحديث الفاتورة. ';
            if (str_contains($e->getMessage(), 'created_by')) {
                $errorMessage .= 'الرجاء التحقق من بيانات المستخدم.';
            } else {
                $errorMessage .= 'الرجاء التحقق من المعلومات المدخلة.';
            }

            return redirect()->back()
                           ->withInput()
                           ->withErrors(['error' => $errorMessage]);
        }
    }

    /**
     * Update invoice status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $invoice = PurchaseInvoice::findOrFail($id);
            $oldValues = $invoice->toArray();

            $validated = $request->validate([
                'status' => 'required|in:draft,pending,approved,rejected,paid',
            ]);

            // Ensure created_by is not null
            $updateData = $validated;
            if (empty($invoice->created_by)) {
                $updateData['created_by'] = Auth::id() ?? 1;
            }

            $invoice->update($updateData);

            // Update approval info if approving
            if ($validated['status'] === 'approved') {
                $invoice->update([
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            }

            // Update paid info if marking as paid
            if ($validated['status'] === 'paid') {
                $invoice->update([
                    'paid_at' => now(),
                ]);
            }

            $newValues = $invoice->fresh()->toArray();

            // Get the status label from the enum
            $statuses = [
                'draft' => 'مسودة',
                'pending' => 'قيد الانتظار',
                'approved' => 'موافق عليه',
                'rejected' => 'مرفوض',
                'paid' => 'مدفوع',
            ];
            $statusLabel = $statuses[$validated['status']] ?? $validated['status'];

            // ✅ تخزين الإشعار
            $this->storeNotification(
                'purchase_invoice_status_changed',
                'تم تغيير حالة فاتورة شراء',
                'تم تغيير حالة الفاتورة رقم ' . $invoice->invoice_number . ' إلى ' . $statusLabel,
                'info',
                'fas fa-sync-alt',
                route('manufacturing.purchase-invoices.show', $invoice->id)
            );

            // Log the operation
            try {
                $this->logOperation(
                    'update',
                    'Update Status',
                    'تم تغيير حالة الفاتورة إلى ' . $statusLabel . ': ' . $invoice->invoice_number,
                    'purchase_invoices',
                    $invoice->id,
                    $oldValues,
                    $newValues
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log status update: ' . $logError->getMessage());
            }

            // إرسال الإشعارات بتحديث حالة الفاتورة
            try {
                // Get the status label from the enum
                $statuses = [
                    'draft' => 'مسودة',
                    'pending' => 'قيد الانتظار',
                    'approved' => 'موافق عليه',
                    'rejected' => 'مرفوض',
                    'paid' => 'مدفوع',
                ];
                $statusLabel = $statuses[$validated['status']] ?? $validated['status'];
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'تحديث حالة فاتورة شراء',
                        'تم تغيير حالة الفاتورة ' . $invoice->invoice_number . ' إلى ' . $statusLabel,
                        'update_invoice_status',
                        'info',
                        'feather icon-edit-2',
                        route('manufacturing.purchase-invoices.show', $invoice->id)
                    );
                }
            } catch (\Exception $notifError) {
                Log::warning('Failed to send invoice status update notifications: ' . $notifError->getMessage());
            }

            return redirect()->back()
                           ->with('success', 'تم تحديث حالة الفاتورة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating invoice status: ' . $e->getMessage());

            // Provide user-friendly error message
            $errorMessage = 'فشل في تحديث الحالة. ';
            if (str_contains($e->getMessage(), 'created_by')) {
                $errorMessage .= 'الرجاء التحقق من بيانات المستخدم.';
            } else {
                $errorMessage .= 'الرجاء التحقق من المعلومات المدخلة.';
            }

            return redirect()->back()
                           ->withErrors(['error' => $errorMessage]);
        }
    }

    /**
     * Delete the specified invoice
     */
    public function destroy($id)
    {
        try {
            $invoice = PurchaseInvoice::findOrFail($id);
            $invoiceData = $invoice->toArray();

            // ✅ تخزين الإشعار
            $this->storeNotification(
                'purchase_invoice_deleted',
                'تم حذف فاتورة شراء',
                'تم حذف فاتورة الشراء رقم ' . $invoice->invoice_number,
                'danger',
                'fas fa-trash',
                route('manufacturing.purchase-invoices.index')
            );

            $invoice->delete();

            // Log the operation
            try {
                $this->logOperation(
                    'delete',
                    'Delete Purchase Invoice',
                    'تم حذف فاتورة الشراء: ' . $invoice->invoice_number,
                    'purchase_invoices',
                    $id,
                    $invoiceData,
                    null
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log invoice deletion: ' . $logError->getMessage());
            }

            // إرسال الإشعارات بحذف الفاتورة
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'حذف فاتورة شراء',
                        'تم حذف فاتورة الشراء: ' . $invoice->invoice_number,
                        'delete_purchase_invoice',
                        'danger',
                        'feather icon-trash-2',
                        route('manufacturing.purchase-invoices.index')
                    );
                }
            } catch (\Exception $notifError) {
                Log::warning('Failed to send invoice delete notifications: ' . $notifError->getMessage());
            }

            return redirect()->route('manufacturing.purchase-invoices.index')
                           ->with('success', 'تم حذف الفاتورة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting purchase invoice: ' . $e->getMessage());
            return redirect()->back()
                           ->withErrors(['error' => 'فشل في حذف الفاتورة: ' . $e->getMessage()]);
        }
    }
}

