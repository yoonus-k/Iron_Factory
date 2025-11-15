<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Modules\Manufacturing\Http\Requests\StoreMaterialRequest;
use Modules\Manufacturing\Http\Requests\UpdateMaterialRequest;
use Modules\Manufacturing\Services\MaterialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WarehouseProductController extends Controller
{
    private MaterialService $materialService;

    public function __construct(MaterialService $materialService)
    {
        $this->materialService = $materialService;
    }

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
        try {
            $validated = $request->validated();

            // إنشاء المادة عبر الـ Service
            $material = $this->materialService->createMaterial($validated);

            // إضافة الكمية الأولية إلى MaterialDetail
            if (isset($validated['warehouse_id']) && isset($validated['original_weight'])) {
                \App\Models\MaterialDetail::create([
                    'material_id' => $material->id,
                    'warehouse_id' => $validated['warehouse_id'],
                    'quantity' => $validated['original_weight'],
                    'original_weight' => $validated['original_weight'],    // ✅ جديد
                    'remaining_weight' => $validated['original_weight'],   // ✅ جديد
                    'unit_id' => $validated['unit_id'] ?? null,           // ✅ جديد
                    'min_quantity' => $validated['min_quantity'] ?? 0,
                    'max_quantity' => $validated['max_quantity'] ?? 999999,
                    'created_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                ]);

                // إنشاء أذن مخزنية (Delivery Note) عند الإنشاء
                $deliveryNote = \App\Models\DeliveryNote::create([
                    'note_number' => $this->generateDeliveryNoteNumber(),
                    'material_id' => $material->id,
                    'delivered_weight' => $validated['original_weight'],
                    'delivery_date' => now()->toDateString(),
                    'driver_name' => 'مادة جديدة',
                    'driver_name_en' => 'New Material',
                    'vehicle_number' => 'N/A',
                    'received_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                ]);
            }

            return redirect()->route('manufacturing.warehouse-products.index')
                           ->with('success', 'تم إضافة المادة بنجاح وتسجيل حركة المستودع');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error creating material: ' . $e->getMessage(), [
                'exception' => $e,
                'input' => $request->all()
            ]);

            // Return back with error message
            return redirect()->back()
                           ->withInput()
                           ->withErrors(['error' => 'فشل في حفظ المادة: ' . $e->getMessage()]);
        }
    }    /**
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
        try {
            $material = Material::findOrFail($id);
            $validated = $request->validated();

            $this->materialService->updateMaterial($material, $validated);

            return redirect()->route('manufacturing.warehouse-products.index')
                           ->with('success', 'تم تحديث المادة بنجاح وتسجيل حركة المستودع');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error updating material: ' . $e->getMessage(), [
                'exception' => $e,
                'material_id' => $id,
                'input' => $request->all()
            ]);

            // Return back with error message
            return redirect()->back()
                           ->withInput()
                           ->withErrors(['error' => 'فشل في تحديث المادة: ' . $e->getMessage()]);
        }
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

    /**
     * Display warehouse transactions for a material.
     */
    public function transactions($id)
    {
        $material = Material::with(['warehouseTransactions.unit'])->findOrFail($id);
        $transactions = $material->warehouseTransactions()->orderBy('created_at', 'desc')->get();

        return view('manufacturing::warehouses.material.transactions', compact('material', 'transactions'));
    }

    /**
     * إضافة كمية جديدة للمادة - تسجيل في MaterialDetail و Delivery Note
     * Add quantity to material - record in MaterialDetail and create Delivery Note
     */
    public function addQuantity(\Illuminate\Http\Request $request, $id)
    {
        try {
            $material = Material::findOrFail($id);

            // التحقق من صحة البيانات
            $validated = $request->validate([
                'warehouse_id' => 'required|exists:warehouses,id',
                'quantity' => 'required|numeric|min:0.01',
                'notes' => 'nullable|string|max:500',
            ], [
                'warehouse_id.required' => 'المستودع مطلوب',
                'warehouse_id.exists' => 'المستودع غير موجود',
                'quantity.required' => 'الكمية مطلوبة',
                'quantity.numeric' => 'الكمية يجب أن تكون رقم',
                'quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
            ]);

            // الحصول على أول MaterialDetail لنأخذ unit_id منه
            $firstDetail = \App\Models\MaterialDetail::where('material_id', $material->id)->first();
            $unitId = $firstDetail?->unit_id;

            // البحث أو إنشاء سجل في MaterialDetail
            $materialDetail = \App\Models\MaterialDetail::where('material_id', $material->id)
                                                       ->where('warehouse_id', $validated['warehouse_id'])
                                                       ->first();

            if ($materialDetail) {
                // تحديث الكمية في سجل موجود
                $materialDetail->quantity += $validated['quantity'];
                $materialDetail->original_weight += $validated['quantity'];        // ✅ تحديث في MaterialDetail
                $materialDetail->remaining_weight += $validated['quantity'];      // ✅ تحديث في MaterialDetail
                $materialDetail->save();
            } else {
                // إنشاء سجل جديد
                $materialDetail = \App\Models\MaterialDetail::create([
                    'material_id' => $material->id,
                    'warehouse_id' => $validated['warehouse_id'],
                    'quantity' => $validated['quantity'],
                    'original_weight' => $validated['quantity'],                 // ✅ جديد
                    'remaining_weight' => $validated['quantity'],                // ✅ جديد
                    'unit_id' => $unitId,                                        // ✅ جديد
                    'min_quantity' => 0,
                    'max_quantity' => 999999,
                    'created_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                ]);
            }

            // إنشاء أذن مخزنية (Delivery Note) تلقائياً
            $deliveryNote = \App\Models\DeliveryNote::create([
                'note_number' => $this->generateDeliveryNoteNumber(),
                'material_id' => $material->id,
                'delivered_weight' => $validated['quantity'],
                'delivery_date' => now()->toDateString(),
                'driver_name' => 'إضافة مستودع', // قيمة افتراضية
                'driver_name_en' => 'Warehouse Addition',
                'vehicle_number' => 'N/A',
                'received_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            ]);

            // تسجيل الحركة في المستودع
            \App\Models\WarehouseTransaction::create([
                'transaction_number' => 'TRX-' . date('Y-m-d') . '-' . uniqid(),
                'material_id' => $material->id,
                'warehouse_id' => $validated['warehouse_id'],
                'transaction_type' => 'receive', // استقبال
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ?? 'أذن مخزنية رقم: ' . $deliveryNote->note_number,
                'created_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            ]);

            return redirect()->back()
                           ->with('success', 'تم إضافة الكمية بنجاح وإنشاء أذن مخزنية رقم: ' . $deliveryNote->note_number);
        } catch (\Exception $e) {
            Log::error('Error adding quantity: ' . $e->getMessage(), [
                'exception' => $e,
                'material_id' => $id,
                'input' => $request->all()
            ]);

            return redirect()->back()
                           ->withInput()
                           ->withErrors(['error' => 'فشل في إضافة الكمية: ' . $e->getMessage()]);
        }
    }

    /**
     * توليد رقم أذن مخزنية فريد
     * Generate unique delivery note number
     */
    private function generateDeliveryNoteNumber(): string
    {
        $prefix = 'DN-' . date('Y-m-d') . '-';
        $sequence = \App\Models\DeliveryNote::where('note_number', 'like', $prefix . '%')
            ->count() + 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
