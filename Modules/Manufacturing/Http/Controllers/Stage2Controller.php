<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Stage2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manufacturing::stages.stage2.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::stages.stage2.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stage1_id' => 'required|exists:stage1_stands,id',
            'process_details' => 'required|string',
            'input_weight' => 'required|numeric',
            'output_weight' => 'required|numeric',
            'waste' => 'nullable|numeric',
            'status' => 'nullable|in:started,in_progress,completed,consumed',
        ]);

        // Stage2Processed::create($validated);

        return redirect()->route('manufacturing.stage2.index')
            ->with('success', 'تم بدء المعالجة بنجاح');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('manufacturing::stages.stage2.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('manufacturing::stages.stage2.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'process_details' => 'required|string',
            'output_weight' => 'required|numeric',
            'waste' => 'nullable|numeric',
            'status' => 'nullable|in:started,in_progress,completed,consumed',
        ]);

        // $processed = Stage2Processed::find($id);
        // $processed->update($validated);

        return redirect()->route('manufacturing.stage2.index')
            ->with('success', 'تم تحديث المعالجة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Stage2Processed::find($id)->delete();

        return redirect()->route('manufacturing.stage2.index')
            ->with('success', 'تم حذف المعالجة بنجاح');
    }

    /**
     * Show complete processing page
     */
    public function completeProcessing()
    {
        return view('manufacturing::stages.stage2.complete-processing');
    }

    /**
     * Complete processing action
     */
    public function completeAction(Request $request)
    {
        $validated = $request->validate([
            'output_weight' => 'required|numeric|min:0',
            'waste_weight' => 'required|numeric|min:0',
            'quality_status' => 'required|in:excellent,good,acceptable,rejected',
            'notes' => 'nullable|string',
        ]);

        // Complete processing logic here
        return redirect()->route('manufacturing.stage2.index')
            ->with('success', 'تم إنهاء المعالجة بنجاح');
    }

    /**
     * Show waste statistics page
     */
    public function wasteStatistics()
    {
        return view('manufacturing::stages.stage2.waste-statistics');
    }
}
