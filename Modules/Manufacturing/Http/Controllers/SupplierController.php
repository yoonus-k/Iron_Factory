<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manufacturing::warehouses.suppliers.index');
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
        // التحقق من البيانات
        $validated = $request->validate([
            'supplier_name' => 'required|string',
            'contact_person' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        // حفظ البيانات في قاعدة البيانات
        // Supplier::create($validated);

        return redirect()->route('manufacturing.suppliers.index')
            ->with('success', 'تم إضافة المورد بنجاح');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('manufacturing::warehouses.suppliers.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('manufacturing::warehouses.suppliers.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'supplier_name' => 'required|string',
            'contact_person' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        // تحديث البيانات
        // $supplier = Supplier::find($id);
        // $supplier->update($validated);

        return redirect()->route('manufacturing.suppliers.index')
            ->with('success', 'تم تحديث بيانات المورد بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // حذف البيانات
        // Supplier::find($id)->delete();

        return redirect()->route('manufacturing.suppliers.index')
            ->with('success', 'تم حذف المورد بنجاح');
    }
}
