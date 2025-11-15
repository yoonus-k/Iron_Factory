<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Modules\Manufacturing\Http\Requests\StoreMaterialRequest;
use Modules\Manufacturing\Http\Requests\UpdateMaterialRequest;
use Illuminate\Http\Request;

class WarehouseProductController extends Controller
{
    /**
     * Display a listing of the warehouse products with filtering.
     */
    public function index(Request $request)
    {
        $query = Material::with(['supplier', 'unit', 'creator']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('material_type', 'like', "%{$search}%")
                  ->orWhere('material_type_en', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('batch_number', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('material_category', $request->get('category'));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Supplier filter
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->get('supplier_id'));
        }

        $materials = $query->paginate(10);
        $suppliers = Supplier::all();

        return view('manufacturing::warehouses.material.index', compact('materials', 'suppliers'));
    }

    /**
     * Show the form for creating a new warehouse product.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $units = Unit::all();
        $users = User::all();

        return view('manufacturing::warehouses.material.create', compact('suppliers', 'units', 'users'));
    }

    /**
     * Store a newly created warehouse product in storage.
     */
    public function store(StoreMaterialRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();
        $validated['remaining_weight'] = $validated['remaining_weight'] ?? $validated['original_weight'];

        Material::create($validated);

        return redirect()->route('manufacturing.warehouse-products.index')
                       ->with('success', 'تم إضافة المادة بنجاح');
    }

    /**
     * Display the specified warehouse product.
     */
    public function show($id)
    {
        $material = Material::with(['supplier', 'unit', 'creator', 'purchaseInvoice'])->findOrFail($id);

        return view('manufacturing::warehouses.material.show', compact('material'));
    }

    /**
     * Show the form for editing the specified warehouse product.
     */
    public function edit($id)
    {
        $material = Material::findOrFail($id);
        $suppliers = Supplier::all();
        $units = Unit::all();
        $users = User::all();

        return view('manufacturing::warehouses.material.edit', compact('material', 'suppliers', 'units', 'users'));
    }

    /**
     * Update the specified warehouse product in storage.
     */
    public function update(UpdateMaterialRequest $request, $id)
    {
        $material = Material::findOrFail($id);
        $validated = $request->validated();

        $material->update($validated);

        return redirect()->route('manufacturing.warehouse-products.index')
                       ->with('success', 'تم تحديث المادة بنجاح');
    }

    /**
     * Remove the specified warehouse product from storage.
     */
    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        $material->delete();

        return redirect()->route('manufacturing.warehouse-products.index')
                       ->with('success', 'تم حذف المادة بنجاح');
    }
}
