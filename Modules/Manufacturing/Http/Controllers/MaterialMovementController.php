<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\MaterialMovement;
use App\Models\Warehouse;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MaterialMovementController extends Controller
{
    /**
     * Display all material movements
     */
    public function index(Request $request)
    {
        $query = MaterialMovement::with([
            'material',
            'unit',
            'fromWarehouse',
            'toWarehouse',
            'supplier',
            'createdBy',
            'deliveryNote',
            'reconciliationLog'
        ])->latest('movement_date');

        // فلترة حسب نوع الحركة
        if ($request->movement_type) {
            $query->where('movement_type', $request->movement_type);
        }

        // فلترة حسب المصدر
        if ($request->source) {
            $query->where('source', $request->source);
        }

        // فلترة حسب المستودع
        if ($request->warehouse_id) {
            $query->byWarehouse($request->warehouse_id);
        }

        // فلترة حسب المادة
        if ($request->material_id) {
            $query->where('material_id', $request->material_id);
        }

        // فلترة حسب التاريخ
        if ($request->from_date) {
            $query->whereDate('movement_date', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('movement_date', '<=', $request->to_date);
        }

        // ترتيب البيانات حسب الأحدث أولاً مع الباجنيشن
        $movements = $query->orderBy('movement_date', 'desc')
            ->paginate(15)
            ->appends($request->query());

        // احصائيات
        $stats = [
            'total' => MaterialMovement::count(),
            'incoming' => MaterialMovement::where('movement_type', 'incoming')->count(),
            'to_production' => MaterialMovement::where('movement_type', 'to_production')->count(),
            'reconciliation' => MaterialMovement::where('source', 'reconciliation')->count(),
        ];

        // بيانات الفلترة
        $warehouses = Warehouse::where('is_active', true)->orderBy('warehouse_name')->get();
        $materials = Material::where('name_ar', true)->orderBy('name_ar')->get();

        return view('manufacturing::warehouses.movements.index', compact(
            'movements',
            'stats',
            'warehouses',
            'materials'
        ));
    }

    /**
     * Show single movement details
     */
    public function show(MaterialMovement $movement)
    {
        $movement->load([
            'material',
            'unit',
            'fromWarehouse',
            'toWarehouse',
            'supplier',
            'createdBy',
            'approvedBy',
            'deliveryNote',
            'deliveryNote.supplier',
            'deliveryNote.warehouse',
            'reconciliationLog',
            'materialDetail'
        ]);

        return view('manufacturing::warehouses.movements.show', compact('movement'));
    }

    /**
     * Get movement details as JSON (API endpoint)
     */
    public function getDetails($id)
    {
        $movement = MaterialMovement::with([
            'material',
            'unit',
            'fromWarehouse',
            'toWarehouse',
            'supplier',
            'createdBy',
            'materialBatch.material',
            'materialBatch.unit'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'movement' => [
                'id' => $movement->id,
                'movement_number' => $movement->movement_number,
                'movement_type' => $movement->movement_type,
                'movement_type_name' => $movement->movement_type_name,
                'source' => $movement->source,
                'source_name' => $movement->source_name,
                'material_name' => $movement->material->name_ar ?? null,
                'material_name_en' => $movement->material->name_en ?? null,
                'quantity' => number_format((float)$movement->quantity, 2),
                'unit_symbol' => $movement->unit?->unit_symbol,
                'unit_price' => $movement->unit_price ? number_format((float)$movement->unit_price, 2) : null,
                'total_value' => $movement->total_value ? number_format((float)$movement->total_value, 2) : null,
                'from_location' => $movement->fromWarehouse?->warehouse_name ?? $movement->supplier?->name ?? '-',
                'to_location' => $movement->toWarehouse?->warehouse_name ?? $movement->destination ?? '-',
                'description' => $movement->description,
                'notes' => $movement->notes,
                'reference_number' => $movement->reference_number,
                'movement_date' => $movement->movement_date ? $movement->movement_date->format('Y-m-d H:i') : null,
                'created_at' => $movement->created_at->format('Y-m-d H:i'),
                'created_by_name' => $movement->createdBy?->name,
                'status' => $movement->status,
                'status_name' => $movement->status_name,
                // Determine which barcode to show
                'batch_code' => $movement->movement_type === 'to_production' && $movement->materialBatch?->latest_production_barcode
                    ? $movement->materialBatch->latest_production_barcode
                    : $movement->materialBatch?->batch_code,
                'warehouse_batch_code' => $movement->materialBatch?->batch_code, // Always show original
                'production_barcode' => $movement->materialBatch?->latest_production_barcode, // Always show production if exists
                'coil_number' => $movement->materialBatch?->coil_number, // ✅ رقم الكويل
                'batch_initial_quantity' => $movement->materialBatch ? number_format((float)$movement->materialBatch->initial_quantity, 2) : null,
                'batch_available_quantity' => $movement->materialBatch ? number_format((float)$movement->materialBatch->available_quantity, 2) : null,
                'is_production_barcode' => $movement->movement_type === 'to_production' && $movement->materialBatch?->latest_production_barcode !== null,
            ]
        ]);
    }
}
