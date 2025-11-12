<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Stage3Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manufacturing::stages.stage3.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::stages.stage3.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stage2_id' => 'required|exists:stage2_processed,id',
            'coil_number' => 'required|string',
            'base_weight' => 'required|numeric',
            'dye_weight' => 'required|numeric',
            'dye_additive_id' => 'required|exists:additives_inventory,id',
            'plastic_weight' => 'required|numeric',
            'plastic_additive_id' => 'required|exists:additives_inventory,id',
            'total_weight' => 'required|numeric',
            'color' => 'required|string',
            'status' => 'nullable|in:created,in_process,completed,packed',
        ]);

        // Stage3Coil::create($validated);

        return redirect()->route('manufacturing.stage3.index')
            ->with('success', 'تم إنشاء الكويل بنجاح');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('manufacturing::stages.stage3.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('manufacturing::stages.stage3.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'coil_number' => 'required|string',
            'dye_weight' => 'required|numeric',
            'plastic_weight' => 'required|numeric',
            'total_weight' => 'required|numeric',
            'color' => 'required|string',
            'status' => 'nullable|in:created,in_process,completed,packed',
        ]);

        // $coil = Stage3Coil::find($id);
        // $coil->update($validated);

        return redirect()->route('manufacturing.stage3.index')
            ->with('success', 'تم تحديث الكويل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Stage3Coil::find($id)->delete();

        return redirect()->route('manufacturing.stage3.index')
            ->with('success', 'تم حذف الكويل بنجاح');
    }

    /**
     * Show add dye and plastic page
     */
    public function addDyePlastic()
    {
        return view('manufacturing::stages.stage3.add-dye-plastic');
    }

    /**
     * Process adding dye and plastic
     */
    public function addDyeAction(Request $request)
    {
        $validated = $request->validate([
            'coil_id' => 'required|string',
            'dye_type' => 'required|string',
            'dye_weight' => 'required|numeric|min:0',
            'plastic_type' => 'required|string',
            'plastic_weight' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Logic to add dye and plastic to coil
        return redirect()->route('manufacturing.stage3.index')
            ->with('success', 'تم إضافة الصبغة والبلاستيك بنجاح');
    }

    /**
     * Show completed coils page
     */
    public function completedCoils()
    {
        return view('manufacturing::stages.stage3.completed-coils');
    }
}
