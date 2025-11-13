<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the units with filtering and search.
     */
    public function index(Request $request)
    {
        $query = Unit::with(['creator']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('unit_name', 'like', "%{$search}%")
                  ->orWhere('unit_name_en', 'like', "%{$search}%")
                  ->orWhere('unit_code', 'like', "%{$search}%")
                  ->orWhere('unit_symbol', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->get('unit_type'));
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $units = $query->paginate(15);

        return view('manufacturing::warehouses.settings.units.index', compact('units'));
    }

    /**
     * Show the form for creating a new unit.
     */
    public function create()
    {
        $unitTypes = [
            'weight' => 'الوزن',
            'length' => 'الطول',
            'volume' => 'الحجم',
            'area' => 'المساحة',
            'quantity' => 'الكمية',
            'time' => 'الوقت',
            'temperature' => 'درجة الحرارة',
            'other' => 'أخرى',
        ];

        return view('manufacturing::warehouses.settings.units.create', compact('unitTypes'));
    }

    /**
     * Store a newly created unit in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_code' => 'required|string|unique:units,unit_code',
            'unit_name' => 'required|string|min:2|max:255',
            'unit_name_en' => 'nullable|string|min:2|max:255',
            'unit_symbol' => 'required|string|max:10',
            'unit_type' => 'required|in:weight,length,volume,area,quantity,time,temperature,other',
            'conversion_factor' => 'nullable|numeric|min:0',
            'base_unit' => 'nullable|exists:units,id',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);

        Unit::create($validated);

        return redirect()->route('manufacturing.warehouse-settings.units.index')
                       ->with('success', 'تم إضافة الوحدة بنجاح');
    }

    /**
     * Display the specified unit.
     */
    public function show($id)
    {
        $unit = Unit::with(['creator', 'materials'])->findOrFail($id);

        return view('manufacturing::warehouses.settings.units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified unit.
     */
    public function edit($id)
    {
        $unit = Unit::findOrFail($id);

        $unitTypes = [
            'weight' => 'الوزن',
            'length' => 'الطول',
            'volume' => 'الحجم',
            'area' => 'المساحة',
            'quantity' => 'الكمية',
            'time' => 'الوقت',
            'temperature' => 'درجة الحرارة',
            'other' => 'أخرى',
        ];

        $baseUnits = Unit::where('id', '!=', $id)->get();

        return view('manufacturing::warehouses.settings.units.edit', compact('unit', 'unitTypes', 'baseUnits'));
    }

    /**
     * Update the specified unit in storage.
     */
    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $validated = $request->validate([
            'unit_code' => 'required|string|unique:units,unit_code,' . $id,
            'unit_name' => 'required|string|min:2|max:255',
            'unit_name_en' => 'nullable|string|min:2|max:255',
            'unit_symbol' => 'required|string|max:10',
            'unit_type' => 'required|in:weight,length,volume,area,quantity,time,temperature,other',
            'conversion_factor' => 'nullable|numeric|min:0',
            'base_unit' => 'nullable|exists:units,id',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $unit->update($validated);

        return redirect()->route('manufacturing.warehouse-settings.units.index')
                       ->with('success', 'تم تحديث الوحدة بنجاح');
    }

    /**
     * Remove the specified unit from storage.
     */
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);

        // Check if unit is used in materials
        if ($unit->materials()->count() > 0) {
            return redirect()->route('manufacturing.warehouse-settings.units.index')
                           ->with('error', 'لا يمكن حذف هذه الوحدة لأنها مستخدمة في مواد');
        }

        $unit->delete();

        return redirect()->route('manufacturing.warehouse-settings.units.index')
                       ->with('success', 'تم حذف الوحدة بنجاح');
    }

    /**
     * Bulk delete units.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'الرجاء اختيار وحدة واحدة على الأقل');
        }

        Unit::whereIn('id', $ids)->delete();

        return redirect()->route('manufacturing.warehouse-settings.units.index')
                       ->with('success', 'تم حذف الوحدات المختارة بنجاح');
    }
}
