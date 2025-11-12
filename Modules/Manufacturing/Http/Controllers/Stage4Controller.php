<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Stage4Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manufacturing::stages.stage4.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::stages.stage4.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'packaging_type' => 'required|string',
            'coils' => 'required|array',
            'coils_count' => 'required|integer',
            'total_weight' => 'required|numeric',
            'customer_info' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'status' => 'nullable|in:packing,packed,shipped,delivered',
        ]);

        // Stage4Box::create($validated);

        return redirect()->route('manufacturing.stage4.index')
            ->with('success', 'تم إنشاء الكرتون بنجاح');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('manufacturing::stages.stage4.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('manufacturing::stages.stage4.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'packaging_type' => 'required|string',
            'total_weight' => 'required|numeric',
            'customer_info' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'status' => 'nullable|in:packing,packed,shipped,delivered',
        ]);

        // $box = Stage4Box::find($id);
        // $box->update($validated);

        return redirect()->route('manufacturing.stage4.index')
            ->with('success', 'تم تحديث الكرتون بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Stage4Box::find($id)->delete();

        return redirect()->route('manufacturing.stage4.index')
            ->with('success', 'تم حذف الكرتون بنجاح');
    }
}
