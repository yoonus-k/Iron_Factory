<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WarehouseSettingsController extends Controller
{
    /**
     * Display the main warehouse settings page (with options).
     */
    public function index()
    {
        return view('manufacturing::warehouses.settings.index');
    }

    // ==================== CATEGORIES ====================

    /**
     * Display a listing of categories.
     */
    public function categoriesIndex()
    {
        $categories = [
            ['id' => 1, 'name' => 'خامات معدنية', 'description' => 'جميع المعادن والسبائك'],
            ['id' => 2, 'name' => 'خامات بلاستيكية', 'description' => 'البلاستيك والراتنجات'],
            ['id' => 3, 'name' => 'خامات خشبية', 'description' => 'الأخشاب المختلفة'],
            ['id' => 4, 'name' => 'خامات أخرى', 'description' => 'مواد خام أخرى'],
        ];

        return view('manufacturing::warehouses.settings.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function categoriesCreate()
    {
        return view('manufacturing::warehouses.settings.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function categoriesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // حفظ الفئة في قاعدة البيانات
        // Category::create($validated);

        return redirect()->route('manufacturing.warehouse-settings.categories.index')
            ->with('success', 'تم إضافة الفئة بنجاح');
    }

    /**
     * Display the specified category.
     */
    public function categoriesShow($id)
    {
        return view('manufacturing::warehouses.settings.categories.show');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function categoriesEdit($id)
    {
        return view('manufacturing::warehouses.settings.categories.edit');
    }

    /**
     * Update the specified category.
     */
    public function categoriesUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // تحديث الفئة
        // $category = Category::find($id);
        // $category->update($validated);

        return redirect()->route('manufacturing.warehouse-settings.categories.index')
            ->with('success', 'تم تحديث الفئة بنجاح');
    }

    /**
     * Delete the specified category.
     */
    public function categoriesDestroy($id)
    {
        // حذف الفئة
        // Category::find($id)->delete();

        return redirect()->route('manufacturing.warehouse-settings.categories.index')
            ->with('success', 'تم حذف الفئة بنجاح');
    }

    // ==================== UNITS ====================

    /**
     * Display a listing of units.
     */
    public function unitsIndex()
    {
        $units = [
            ['id' => 1, 'name' => 'كيلوجرام', 'abbreviation' => 'كجم'],
            ['id' => 2, 'name' => 'جرام', 'abbreviation' => 'جم'],
            ['id' => 3, 'name' => 'لتر', 'abbreviation' => 'لتر'],
            ['id' => 4, 'name' => 'متر مكعب', 'abbreviation' => 'م³'],
            ['id' => 5, 'name' => 'متر', 'abbreviation' => 'م'],
            ['id' => 6, 'name' => 'وحدة', 'abbreviation' => 'وحدة'],
        ];

        return view('manufacturing::warehouses.settings.units.index', compact('units'));
    }

    /**
     * Show the form for creating a new unit.
     */
    public function unitsCreate()
    {
        return view('manufacturing::warehouses.settings.units.create');
    }

    /**
     * Store a newly created unit.
     */
    public function unitsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:50',
        ]);

        // حفظ الوحدة في قاعدة البيانات
        // Unit::create($validated);

        return redirect()->route('manufacturing.warehouse-settings.units.index')
            ->with('success', 'تم إضافة الوحدة بنجاح');
    }

    /**
     * Display the specified unit.
     */
    public function unitsShow($id)
    {
        return view('manufacturing::warehouses.settings.units.show');
    }

    /**
     * Show the form for editing the specified unit.
     */
    public function unitsEdit($id)
    {
        return view('manufacturing::warehouses.settings.units.edit');
    }

    /**
     * Update the specified unit.
     */
    public function unitsUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:50',
        ]);

        // تحديث الوحدة
        // $unit = Unit::find($id);
        // $unit->update($validated);

        return redirect()->route('manufacturing.warehouse-settings.units.index')
            ->with('success', 'تم تحديث الوحدة بنجاح');
    }

    /**
     * Delete the specified unit.
     */
    public function unitsDestroy($id)
    {
        // حذف الوحدة
        // Unit::find($id)->delete();

        return redirect()->route('manufacturing.warehouse-settings.units.index')
            ->with('success', 'تم حذف الوحدة بنجاح');
    }
}
