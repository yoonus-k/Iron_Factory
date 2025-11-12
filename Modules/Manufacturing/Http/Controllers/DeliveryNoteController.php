<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DeliveryNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manufacturing::warehouses.delivery-notes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::warehouses.delivery-notes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'delivery_number' => 'required|string|unique:delivery_notes',
            'delivery_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'received_by' => 'nullable|string',
            'total_weight' => 'nullable|numeric',
            'quantity' => 'nullable|integer',
            'status' => 'nullable|in:pending,received,rejected',
        ]);

        // حفظ البيانات في قاعدة البيانات
        // DeliveryNote::create($validated);

        return redirect()->route('manufacturing.delivery-notes.index')
            ->with('success', 'تم إضافة أذن التسليم بنجاح');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('manufacturing::warehouses.delivery-notes.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('manufacturing::warehouses.delivery-notes.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'delivery_number' => 'required|string',
            'delivery_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'received_by' => 'nullable|string',
            'total_weight' => 'nullable|numeric',
            'quantity' => 'nullable|integer',
            'status' => 'nullable|in:pending,received,rejected',
        ]);

        // تحديث البيانات
        // $deliveryNote = DeliveryNote::find($id);
        // $deliveryNote->update($validated);

        return redirect()->route('manufacturing.delivery-notes.index')
            ->with('success', 'تم تحديث أذن التسليم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // حذف البيانات
        // DeliveryNote::find($id)->delete();

        return redirect()->route('manufacturing.delivery-notes.index')
            ->with('success', 'تم حذف أذن التسليم بنجاح');
    }
}
