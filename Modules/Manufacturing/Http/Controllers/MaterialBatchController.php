<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Manufacturing\Entities\MaterialBatch;

class MaterialBatchController extends Controller
{
    /**
     * API: جلب بيانات الدفعة (المادة الخام) حسب الباركود
     * GET /manufacturing/material-batches/get-by-barcode/{barcode}
     */
    public function getByBarcode($barcode)
    {
        $batch = MaterialBatch::where('batch_code', $barcode)->with(['material', 'unit', 'warehouse'])->first();
        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على دفعة بهذا الباركود',
            ], 404);
        }
        // جلب اسم المادة من name_ar إذا كان موجوداً
        $material_name = null;
        if ($batch->material) {
            $material_name = $batch->material->name_ar ?? $batch->material->name ?? null;
        }
        return response()->json([
            'success' => true,
            'batch' => [
                'id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'material_id' => $batch->material_id,
                'material_name' => $material_name,
                'unit_id' => $batch->unit_id,
                'unit_name' => $batch->unit ? $batch->unit->name : null,
                'initial_quantity' => $batch->initial_quantity,
                'available_quantity' => $batch->available_quantity,
                'batch_date' => $batch->batch_date,
                'expire_date' => $batch->expire_date,
                'warehouse_id' => $batch->warehouse_id,
                'warehouse_name' => $batch->warehouse ? $batch->warehouse->name : null,
                'notes' => $batch->notes,
            ]
        ]);
    }
}
