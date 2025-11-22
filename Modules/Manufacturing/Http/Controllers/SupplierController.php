<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Supplier;
use App\Models\User;
use App\Models\DeliveryNote;
use App\Models\PurchaseInvoice;
use Modules\Manufacturing\Traits\LogsOperations;
use App\Traits\StoresNotifications;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    use LogsOperations, StoresNotifications;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Apply filters if provided
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('name_en', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('phone') && $request->phone) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        if ($request->has('status') && $request->status) {
            $isActive = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        // ترتيب البيانات حسب الأحدث أولاً مع الباجنيشن
        $suppliers = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('manufacturing::warehouses.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::warehouses.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'supplier_name' => 'required|string|max:255',
                'contact_person' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|unique:suppliers,email',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'status' => 'nullable|in:active,inactive',
                'notes' => 'nullable|string',
            ], [
                'supplier_name.required' => 'اسم المورد مطلوب',
                'supplier_name.max' => 'اسم المورد يجب ألا يتجاوز 255 حرفًا',
                'contact_person.required' => 'اسم الشخص المسؤول مطلوب',
                'contact_person.max' => 'اسم الشخص المسؤول يجب ألا يتجاوز 255 حرفًا',
                'phone.required' => 'رقم الهاتف مطلوب',
                'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 رقمًا',
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.email' => 'البريد الإلكتروني غير صحيح',
                'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
            ]);

            // Create the supplier
            $supplier = new Supplier();
            $supplier->name = $validated['supplier_name'];
            $supplier->name_en = $validated['supplier_name'];
            $supplier->contact_person = $validated['contact_person'];
            $supplier->contact_person_en = $validated['contact_person'];
            $supplier->phone = $validated['phone'];
            $supplier->email = $validated['email'];
            $supplier->address = $validated['address'] ?? null;
            $supplier->address_en = $validated['address'] ?? null;
            $supplier->is_active = ($validated['status'] ?? 'active') === 'active' ? 1 : 0;
            $supplier->created_by = Auth::id() ?? 1;
            $supplier->save();

            // ✅ تخزين الإشعار
            $this->storeNotification(
                'supplier_created',
                'تم إنشاء مورد جديد',
                'تم إنشاء مورد جديد: ' . $supplier->name . ' - ' . $supplier->phone,
                'success',
                'fas fa-building',
                route('manufacturing.suppliers.show', $supplier->id)
            );

            // تسجيل العملية
            try {
                $this->logOperation(
                    'create',
                    'Create Supplier',
                    'تم إنشاء مورد جديد: ' . $supplier->name,
                    'suppliers',
                    $supplier->id,
                    null,
                    $supplier->toArray()
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log supplier creation: ' . $logError->getMessage());
            }

            return redirect()->route('manufacturing.suppliers.index')
                ->with('success', 'تم إضافة المورد بنجاح');
        } catch (\Exception $e) {
            Log::error('Error creating supplier: ' . $e->getMessage(), [
                'exception' => $e,
                'input' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة المورد: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     * عرض تفاصيل المورد مع فواتيره وأذون التسليم
     */
    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);

        // الحصول على فواتير المورد مع الترتيب الأحدث أولاً
        $invoices = $supplier->purchaseInvoices()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // الحصول على أذون التسليم (الواردة من المورد)
        $deliveryNotes = DeliveryNote::where('supplier_id', $supplier->id)
            ->where('type', 'incoming')
            ->orderBy('delivery_date', 'desc')
            ->paginate(10);

        return view('manufacturing::warehouses.suppliers.show', compact('supplier', 'invoices', 'deliveryNotes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('manufacturing::warehouses.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $oldValues = $supplier->toArray();

            // Validate the request data
            $validated = $request->validate([
                'supplier_name' => 'required|string|max:255',
                'contact_person' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|unique:suppliers,email,' . $id,
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'status' => 'nullable|in:active,inactive',
                'notes' => 'nullable|string',
            ], [
                'supplier_name.required' => 'اسم المورد مطلوب',
                'supplier_name.max' => 'اسم المورد يجب ألا يتجاوز 255 حرفًا',
                'contact_person.required' => 'اسم الشخص المسؤول مطلوب',
                'contact_person.max' => 'اسم الشخص المسؤول يجب ألا يتجاوز 255 حرفًا',
                'phone.required' => 'رقم الهاتف مطلوب',
                'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 رقمًا',
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.email' => 'البريد الإلكتروني غير صحيح',
                'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
            ]);

            // Update the supplier
            $supplier->name = $validated['supplier_name'];
            $supplier->name_en = $validated['supplier_name'];
            $supplier->contact_person = $validated['contact_person'];
            $supplier->contact_person_en = $validated['contact_person'];
            $supplier->phone = $validated['phone'];
            $supplier->email = $validated['email'];
            $supplier->address = $validated['address'] ?? null;
            $supplier->address_en = $validated['address'] ?? null;
            $supplier->is_active = ($validated['status'] ?? 'active') === 'active' ? 1 : 0;
            $supplier->save();

            $newValues = $supplier->fresh()->toArray();

            // ✅ تخزين الإشعار
            $this->storeNotification(
                'supplier_updated',
                'تم تحديث بيانات مورد',
                'تم تحديث معلومات المورد: ' . $supplier->name,
                'warning',
                'fas fa-edit',
                route('manufacturing.suppliers.show', $supplier->id)
            );

            // تسجيل العملية
            try {
                $this->logOperation(
                    'update',
                    'Update Supplier',
                    'تم تحديث مورد: ' . $supplier->name,
                    'suppliers',
                    $supplier->id,
                    $oldValues,
                    $newValues
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log supplier update: ' . $logError->getMessage());
            }

            return redirect()->route('manufacturing.suppliers.index')
                ->with('success', 'تم تحديث بيانات المورد بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating supplier: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث بيانات المورد: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $oldValues = $supplier->toArray();

            // ✅ تخزين الإشعار
            $this->storeNotification(
                'supplier_deleted',
                'تم حذف مورد',
                'تم حذف المورد: ' . $supplier->name . ' (' . $supplier->phone . ')',
                'danger',
                'fas fa-trash',
                route('manufacturing.suppliers.index')
            );

            // تسجيل العملية قبل الحذف
            try {
                $this->logOperation(
                    'delete',
                    'Delete Supplier',
                    'تم حذف مورد: ' . $supplier->name,
                    'suppliers',
                    $supplier->id,
                    $oldValues,
                    null
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log supplier deletion: ' . $logError->getMessage());
            }

            $supplier->delete();

            return redirect()->route('manufacturing.suppliers.index')
                ->with('success', 'تم حذف المورد بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting supplier: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف المورد: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the status of the specified supplier.
     */
    public function toggleStatus($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);

            $oldStatus = $supplier->is_active;
            $newStatus = !$oldStatus;

            $supplier->is_active = $newStatus;
            $supplier->save();

            $statusText = $newStatus ? 'نشط' : 'غير نشط';

            // ✅ تخزين الإشعار
            $this->storeNotification(
                'supplier_status_changed',
                'تم تغيير حالة مورد',
                'تم تغيير حالة المورد ' . $supplier->name . ' إلى ' . $statusText,
                'info',
                'fas fa-toggle-' . ($newStatus ? 'on' : 'off'),
                route('manufacturing.suppliers.show', $supplier->id)
            );

            // Log the status change
            try {
                $this->logOperation(
                    'update',
                    'Toggle Status',
                    'تم تغيير حالة المورد من ' . ($oldStatus ? 'نشط' : 'غير نشط') . ' إلى ' . ($newStatus ? 'نشط' : 'غير نشط'),
                    'suppliers',
                    $supplier->id,
                    ['is_active' => $oldStatus],
                    ['is_active' => $newStatus]
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log supplier status change: ' . $logError->getMessage());
            }

            return redirect()->back()
                           ->with('success', 'تم تغيير حالة المورد إلى ' . $statusText);
        } catch (\Exception $e) {
            Log::error('Error toggling supplier status: ' . $e->getMessage());
            return redirect()->back()
                           ->withErrors(['error' => 'فشل في تغيير حالة المورد: ' . $e->getMessage()]);
        }
    }

    /**
     * Get supplier invoices via AJAX
     */
    public function getInvoices($id, Request $request)
    {
        $supplier = Supplier::findOrFail($id);
        $page = $request->get('page', 1);

        $invoices = $supplier->purchaseInvoices()
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'invoice_page', $page);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('manufacturing::warehouses.suppliers.partials.invoices-table', compact('invoices'))->render(),
                'pagination' => (string) $invoices->links()
            ]);
        }

        return view('manufacturing::warehouses.suppliers.partials.invoices-table', compact('invoices'));
    }

    /**
     * Get supplier delivery notes via AJAX
     */
    public function getDeliveryNotes($id, Request $request)
    {
        $supplier = Supplier::findOrFail($id);
        $page = $request->get('page', 1);

        $deliveryNotes = DeliveryNote::where('supplier_id', $supplier->id)
            ->where('type', 'incoming')
            ->orderBy('delivery_date', 'desc')
            ->paginate(10, ['*'], 'delivery_page', $page);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('manufacturing::warehouses.suppliers.partials.delivery-notes-table', compact('deliveryNotes'))->render(),
                'pagination' => (string) $deliveryNotes->links()
            ]);
        }

        return view('manufacturing::warehouses.suppliers.partials.delivery-notes-table', compact('deliveryNotes'));
    }
}
