<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MaterialType;
use App\Models\Unit;
use Illuminate\Http\Request;

class MaterialTypeController extends Controller
{
    /**
     * Display a listing of the material types with filtering and search.
     */
    public function index(Request $request)
    {
        $query = MaterialType::with(['creator']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('type_name', 'like', "%{$search}%")
                  ->orWhere('type_name_en', 'like', "%{$search}%")
                  ->orWhere('type_code', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $materialTypes = $query->paginate(15);

        return view('manufacturing::warehouses.settings.material-types.index', compact('materialTypes'));
    }

    /**
     * Show the form for creating a new material type.
     */
    public function create()
    {
        $categories = [
            'raw_material' => 'خام',
            'finished_product' => 'منتج نهائي',
            'semi_finished' => 'منتج نصف نهائي',
            'additive' => 'إضافة',
            'packing_material' => 'مادة تغليف',
            'component' => 'مكون',
        ];

        $units = Unit::where('is_active', true)->get();

        return view('manufacturing::warehouses.settings.material-types.create', compact('categories', 'units'));
    }

    /**
     * Store a newly created material type in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_code' => 'required|string|unique:material_types,type_code',
            'type_name' => 'required|string|min:2|max:255',
            'type_name_en' => 'nullable|string|min:2|max:255',

            'description' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'specifications' => 'nullable|json',
            'default_unit' => 'nullable|exists:units,id',
            'standard_cost' => 'nullable|numeric|min:0',
            'storage_conditions' => 'nullable|string|max:500',
            'storage_conditions_en' => 'nullable|string|max:500',
            'shelf_life_days' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);

        MaterialType::create($validated);

        return redirect()->route('manufacturing.warehouse-settings.material-types.index')
                       ->with('success', 'تم إضافة نوع المادة بنجاح');
    }

    /**
     * Display the specified material type.
     */
    public function show($id)
    {
        $materialType = MaterialType::with(['creator', 'materials'])->findOrFail($id);

        return view('manufacturing::warehouses.settings.material-types.show', compact('materialType'));
    }

    /**
     * Show the form for editing the specified material type.
     */
    public function edit($id)
    {
        $materialType = MaterialType::findOrFail($id);

        $categories = [
            'raw_material' => 'خام',
            'finished_product' => 'منتج نهائي',
            'semi_finished' => 'منتج نصف نهائي',
            'additive' => 'إضافة',
            'packing_material' => 'مادة تغليف',
            'component' => 'مكون',
        ];

        $units = Unit::where('is_active', true)->get();

        return view('manufacturing::warehouses.settings.material-types.edit', compact('materialType', 'categories', 'units'));
    }

    /**
     * Update the specified material type in storage.
     */
    public function update(Request $request, $id)
    {
        $materialType = MaterialType::findOrFail($id);

        $validated = $request->validate([
            'type_code' => 'required|string|unique:material_types,type_code,' . $id,
            'type_name' => 'required|string|min:2|max:255',
            'type_name_en' => 'nullable|string|min:2|max:255',

            'description' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'specifications' => 'nullable|json',
            'default_unit' => 'nullable|exists:units,id',
            'standard_cost' => 'nullable|numeric|min:0',
            'storage_conditions' => 'nullable|string|max:500',
            'storage_conditions_en' => 'nullable|string|max:500',
            'shelf_life_days' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $materialType->update($validated);

        return redirect()->route('manufacturing.warehouse-settings.material-types.index')
                       ->with('success', 'تم تحديث نوع المادة بنجاح');
    }

    /**
     * Remove the specified material type from storage.
     */
    public function destroy($id)
    {
        $materialType = MaterialType::findOrFail($id);

        // Check if material type is used in materials
        if ($materialType->materials()->count() > 0) {
            return redirect()->route('manufacturing.warehouse-settings.material-types.index')
                           ->with('error', 'لا يمكن حذف هذا النوع لأنه مستخدم في مواد');
        }

        $materialType->delete();

        return redirect()->route('manufacturing.warehouse-settings.material-types.index')
                       ->with('success', 'تم حذف نوع المادة بنجاح');
    }

    /**
     * Bulk delete material types.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'الرجاء اختيار نوع مادة واحد على الأقل');
        }

        MaterialType::whereIn('id', $ids)->delete();

        return redirect()->route('manufacturing.warehouse-settings.material-types.index')
                       ->with('success', 'تم حذف أنواع المواد المختارة بنجاح');
    }
}
