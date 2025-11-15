<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Supplier; // Import the Supplier model

class SupplierController extends Controller
{
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

        try {
            // Create the supplier
            $supplier = new Supplier();
            $supplier->name = $validated['supplier_name'];
            $supplier->name_en = $validated['supplier_name']; // For now, we'll use the same name
            $supplier->contact_person = $validated['contact_person'];
            $supplier->contact_person_en = $validated['contact_person']; // For now, we'll use the same name
            $supplier->phone = $validated['phone'];
            $supplier->email = $validated['email'];
            $supplier->address = $validated['address'] ?? null;
            $supplier->address_en = $validated['address'] ?? null; // For now, we'll use the same address
            $supplier->is_active = ($validated['status'] ?? 'active') === 'active' ? 1 : 0;
            $supplier->created_by = auth()->id(); // Set the creator
            $supplier->save();

            return redirect()->route('manufacturing.suppliers.index')
                ->with('success', 'تم إضافة المورد بنجاح');
        } catch (\Exception $e) {
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
        $supplier = Supplier::findOrFail($id);

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

        try {
            // Update the supplier
            $supplier->name = $validated['supplier_name'];
            $supplier->name_en = $validated['supplier_name']; // For now, we'll use the same name
            $supplier->contact_person = $validated['contact_person'];
            $supplier->contact_person_en = $validated['contact_person']; // For now, we'll use the same name
            $supplier->phone = $validated['phone'];
            $supplier->email = $validated['email'];
            $supplier->address = $validated['address'] ?? null;
            $supplier->address_en = $validated['address'] ?? null; // For now, we'll use the same address
            $supplier->is_active = ($validated['status'] ?? 'active') === 'active' ? 1 : 0;
            $supplier->save();

            return redirect()->route('manufacturing.suppliers.index')
                ->with('success', 'تم تحديث بيانات المورد بنجاح');
        } catch (\Exception $e) {
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
            $supplier->delete();

            return redirect()->route('manufacturing.suppliers.index')
                ->with('success', 'تم حذف المورد بنجاح');
        } catch (\Exception $e) {
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
            $supplier->is_active = !$supplier->is_active;
            $supplier->save();

            $statusText = $supplier->is_active ? 'نشط' : 'غير نشط';

            return response()->json([
                'success' => true,
                'message' => 'تم تغيير حالة المورد إلى ' . $statusText
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير الحالة: ' . $e->getMessage()
            ], 500);
        }
    }
}