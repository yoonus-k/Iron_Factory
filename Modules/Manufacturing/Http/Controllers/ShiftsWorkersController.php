<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class ShiftsWorkersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manufacturing::shifts-workers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::shifts-workers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'shift_code' => 'required|string|max:255',
            'shift_date' => 'required|date',
            'shift_type' => 'required|in:morning,evening,night',
            'supervisor_id' => 'required|integer',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'workers' => 'nullable|array',
            'workers.*' => 'integer'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Store the shift data (in a real application, this would save to database)
        // For now, we'll just redirect back to the index with a success message
        return redirect()->route('manufacturing.shifts-workers.index')->with('success', 'تم إنشاء الوردية بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return view('manufacturing::shifts-workers.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('manufacturing::shifts-workers.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'shift_code' => 'required|string|max:255',
            'shift_date' => 'required|date',
            'shift_type' => 'required|in:morning,evening,night',
            'supervisor_id' => 'required|integer',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'workers' => 'nullable|array',
            'workers.*' => 'integer'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update the shift data (in a real application, this would update the database)
        // For now, we'll just redirect back to the index with a success message
        return redirect()->route('manufacturing.shifts-workers.index')->with('success', 'تم تحديث الوردية بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Delete the shift (in a real application, this would delete from database)
        return redirect()->route('manufacturing.shifts-workers.index')->with('success', 'تم حذف الوردية بنجاح');
    }

    /**
     * Display current active shifts.
     */
    public function current()
    {
        return view('manufacturing::shifts-workers.current');
    }

    /**
     * Display attendance records.
     */
    public function attendance()
    {
        return view('manufacturing::shifts-workers.attendance');
    }
}