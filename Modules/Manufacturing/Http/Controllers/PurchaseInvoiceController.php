<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Models\User;
use Modules\Manufacturing\Traits\LogsOperations;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PurchaseInvoiceController extends Controller
{
    use LogsOperations;

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

        return view('manufacturing::warehouses.purchase-invoices.create', compact('suppliers', 'users'));
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
                'total_amount' => 'required|numeric|min:0|gt:0',
                'currency' => 'required|string|max:3',
                'payment_terms' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
                'is_active' => 'nullable|boolean',
            ]);

            $validated['recorded_by'] = Auth::id() ?? 1;
            $validated['status'] = 'draft';
            $validated['is_active'] = $request->boolean('is_active', true);

            $invoice = PurchaseInvoice::create($validated);

            // Log the operation
            try {
                $this->logOperation(
                    'create',
                    'Create Purchase Invoice',
                    'تم إنشاء فاتورة شراء جديدة: ' . $invoice->invoice_number,
                    'purchase_invoices',
                    $invoice->id,
                    null,
                    $invoice->toArray()
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log invoice creation: ' . $logError->getMessage());
            }

            return redirect()->route('manufacturing.purchase-invoices.show', $invoice->id)
                           ->with('success', 'تم إضافة الفاتورة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error creating purchase invoice: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->withErrors(['error' => 'فشل في حفظ الفاتورة: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the specified invoice
     */
    public function show($id)
    {
        try {
            $invoice = PurchaseInvoice::with(['supplier', 'recordedBy', 'approvedBy', 'operationLogs'])->findOrFail($id);
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
            $invoice = PurchaseInvoice::findOrFail($id);
            $suppliers = Supplier::all();
            $users = User::all();

            return view('manufacturing::warehouses.purchase-invoices.edit', compact('invoice', 'suppliers', 'users'));
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
            $invoice = PurchaseInvoice::findOrFail($id);
            $oldValues = $invoice->toArray();

            $validated = $request->validate([
                'invoice_number' => 'required|string|unique:purchase_invoices,invoice_number,' . $id,
                'invoice_reference_number' => 'nullable|string|max:100',
                'supplier_id' => 'required|exists:suppliers,id',
                'invoice_date' => 'required|date',
                'due_date' => 'nullable|date|after_or_equal:invoice_date',
                'total_amount' => 'required|numeric|min:0|gt:0',
                'currency' => 'required|string|max:3',
                'payment_terms' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
                'is_active' => 'nullable|boolean',
            ]);

            $validated['is_active'] = $request->boolean('is_active', true);

            $invoice->update($validated);
            $newValues = $invoice->fresh()->toArray();

            // Log the operation
            try {
                $this->logOperation(
                    'update',
                    'Update Purchase Invoice',
                    'تم تحديث فاتورة الشراء: ' . $invoice->invoice_number,
                    'purchase_invoices',
                    $invoice->id,
                    $oldValues,
                    $newValues
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log invoice update: ' . $logError->getMessage());
            }

            return redirect()->route('manufacturing.purchase-invoices.show', $invoice->id)
                           ->with('success', 'تم تحديث الفاتورة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating purchase invoice: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->withErrors(['error' => 'فشل في تحديث الفاتورة: ' . $e->getMessage()]);
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

            $invoice->update($validated);

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

            // Log the operation
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

            return redirect()->back()
                           ->with('success', 'تم تحديث حالة الفاتورة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating invoice status: ' . $e->getMessage());
            return redirect()->back()
                           ->withErrors(['error' => 'فشل في تحديث الحالة: ' . $e->getMessage()]);
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

            return redirect()->route('manufacturing.purchase-invoices.index')
                           ->with('success', 'تم حذف الفاتورة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting purchase invoice: ' . $e->getMessage());
            return redirect()->back()
                           ->withErrors(['error' => 'فشل في حذف الفاتورة: ' . $e->getMessage()]);
        }
    }
}

