<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdditiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manufacturing::warehouses.additives.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::warehouses.additives.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'additive_name' => 'required|string',
            'type' => 'required|in:dye,plastic,other',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|in:liter,kg,gram,unit',
            'supplier_id' => 'required|exists:suppliers,id',
            'color' => 'nullable|string',
            'status' => 'nullable|in:available,low_stock,out_of_stock',
        ]);

        // حفظ البيانات في قاعدة البيانات
        // Additive::create($validated);

        return redirect()->route('manufacturing.additives.index')
            ->with('success', 'تم إضافة المادة بنجاح');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('manufacturing::warehouses.additives.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('manufacturing::warehouses.additives.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'additive_name' => 'required|string',
            'type' => 'required|in:dye,plastic,other',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|in:liter,kg,gram,unit',
            'supplier_id' => 'required|exists:suppliers,id',
            'color' => 'nullable|string',
            'status' => 'nullable|in:available,low_stock,out_of_stock',
        ]);

        // تحديث البيانات
        // $additive = Additive::find($id);
        // $additive->update($validated);

        return redirect()->route('manufacturing.additives.index')
            ->with('success', 'تم تحديث بيانات المادة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // حذف البيانات
        // Additive::find($id)->delete();

        return redirect()->route('manufacturing.additives.index')
            ->with('success', 'تم حذف المادة بنجاح');
    }
}
