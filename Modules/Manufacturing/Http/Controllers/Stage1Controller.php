<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Stage1Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manufacturing::stages.stage1.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::stages.stage1.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'stand_number' => 'required|string',
            'wire_size' => 'required|numeric',
            'weight' => 'required|numeric',
            'waste_percentage' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        // Stage1Stand::create($validated);

        return redirect()->route('manufacturing.stage1.index')
            ->with('success', 'تم إنشاء الاستاند بنجاح');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('manufacturing::stages.stage1.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('manufacturing::stages.stage1.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'stand_number' => 'required|string',
            'wire_size' => 'required|numeric',
            'weight' => 'required|numeric',
            'waste_percentage' => 'nullable|numeric',
            'status' => 'nullable|in:created,in_process,completed,consumed',
            'notes' => 'nullable|string',
        ]);

        // $stand = Stage1Stand::find($id);
        // $stand->update($validated);

        return redirect()->route('manufacturing.stage1.index')
            ->with('success', 'تم تحديث الاستاند بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Stage1Stand::find($id)->delete();

        return redirect()->route('manufacturing.stage1.index')
            ->with('success', 'تم حذف الاستاند بنجاح');
    }

    /**
     * Show barcode scan page
     */
    public function barcodeScan()
    {
        return view('manufacturing::stages.stage1.barcode-scan');
    }

    /**
     * Process barcode scan
     */
    public function processBarcodeAction(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string',
        ]);

        // Process barcode logic here
        return response()->json([
            'success' => true,
            'message' => 'تم معالجة الباركود بنجاح',
            'barcode' => $validated['barcode']
        ]);
    }

    /**
     * Show waste tracking page
     */
    public function wasteTracking()
    {
        return view('manufacturing::stages.stage1.waste-tracking');
    }
}
