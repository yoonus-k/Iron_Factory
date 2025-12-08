<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Translation;
use Illuminate\Http\Request;

class MaterialTranslationExampleController extends Controller
{
    public function createMaterialWithTranslations()
    {
        $material = Material::create([
            'warehouse_id' => 1,
            'material_type_id' => 1,
            'barcode' => 'WH-' . date('YmdHis'),
            'batch_number' => 'BATCH-001',
            'status' => Material::STATUS_AVAILABLE,
            'created_by' => auth()->id(),
        ]);

        $material->setTranslation('name', 'حديد خام', 'ar');
        $material->setTranslation('name', 'Raw Iron', 'en');

        return response()->json([
            'message' => 'Done',
            'material' => $material,
        ]);
    }

    public function showMaterial(Material $material)
    {
        return response()->json([
            'id' => $material->id,
            'barcode' => $material->barcode,
            'name_ar' => $material->getName('ar'),
            'name_en' => $material->getName('en'),
        ]);
    }
}

