<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Supplier;
use Modules\Manufacturing\Traits\LogsOperations;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    use LogsOperations;
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

        $suppliers = $query->paginate(10);

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

            // إرسال الإشعارات بإضافة المورد
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'إضافة مورد جديد',
                        'تم إضافة مورد جديد: ' . $supplier->name,
                        'create_supplier',
                        'success',
                        'feather icon-plus-circle',
                        route('manufacturing.suppliers.show', $supplier->id)
                    );
                }
            } catch (\Exception $notifError) {
                Log::warning('Failed to send supplier creation notifications: ' . $notifError->getMessage());
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
     */
    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('manufacturing::warehouses.suppliers.show', compact('supplier'));
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

            // إرسال الإشعارات بتحديث المورد
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'تحديث مورد',
                        'تم تحديث معلومات المورد: ' . $supplier->name,
                        'update_supplier',
                        'info',
                        'feather icon-edit',
                        route('manufacturing.suppliers.show', $supplier->id)
                    );
                }
            } catch (\Exception $notifError) {
                Log::warning('Failed to send supplier update notifications: ' . $notifError->getMessage());
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

            // إرسال الإشعارات بحذف المورد
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'حذف مورد',
                        'تم حذف المورد: ' . $supplier->name,
                        'delete_supplier',
                        'danger',
                        'feather icon-trash-2',
                        route('manufacturing.suppliers.index')
                    );
                }
            } catch (\Exception $notifError) {
                Log::warning('Failed to send supplier delete notifications: ' . $notifError->getMessage());
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

            // إرسال الإشعارات بتغيير حالة المورد
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'تغيير حالة مورد',
                        'تم تغيير حالة المورد ' . $supplier->name . ' إلى ' . $statusText,
                        'toggle_supplier_status',
                        'info',
                        'feather icon-toggle-' . ($newStatus ? 'right' : 'left'),
                        route('manufacturing.suppliers.show', $supplier->id)
                    );
                }
            } catch (\Exception $notifError) {
                Log::warning('Failed to send supplier status toggle notifications: ' . $notifError->getMessage());
            }

            return redirect()->back()
                           ->with('success', 'تم تغيير حالة المورد إلى ' . $statusText);
        } catch (\Exception $e) {
            Log::error('Error toggling supplier status: ' . $e->getMessage());
            return redirect()->back()
                           ->withErrors(['error' => 'فشل في تغيير حالة المورد: ' . $e->getMessage()]);
        }
    }
}
