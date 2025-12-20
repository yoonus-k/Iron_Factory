<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\MaterialType;
use App\Models\Unit;
use App\Models\Warehouse;

class MaterialsSeeder extends Seeder
{
    public function run(): void
    {
        $ironType = MaterialType::where('type_code', 'MT-001')->first();
        $processedType = MaterialType::where('type_code', 'MT-002')->first();
        $coilType = MaterialType::where('type_code', 'MT-004')->first();
        
        $kgUnit = Unit::where('unit_symbol', 'kg')->first();
        $tonUnit = Unit::where('unit_symbol', 't')->first();
        $rollUnit = Unit::where('unit_symbol', 'roll')->first();
        
        $mainWarehouse = Warehouse::where('warehouse_code', 'WH-001')->first();

        $materials = [
            [
                'barcode' => 'WH-001-2025-001',
                'batch_number' => 'BATCH-001',
                'name_ar' => 'حديد خام 10 ملم',
                'name_en' => 'Raw Iron 10mm',
                'material_type_id' => $ironType?->id,
                'unit_id' => $kgUnit?->id,
                'warehouse_id' => $mainWarehouse?->id,
                'type' => 'raw',
                'shelf_location' => 'A-01-01',
                'shelf_location_en' => 'A-01-01',
                'status' => 'available',
                'notes' => 'مادة خام عالية الجودة',
                'notes_en' => 'High quality raw material',
                'created_by' => 1,
            ],
            [
                'barcode' => 'WH-001-2025-002',
                'batch_number' => 'BATCH-002',
                'name_ar' => 'حديد مشغول 12 ملم',
                'name_en' => 'Processed Iron 12mm',
                'material_type_id' => $processedType?->id,
                'unit_id' => $kgUnit?->id,
                'warehouse_id' => $mainWarehouse?->id,
                'type' => 'manufactured',
                'shelf_location' => 'A-01-02',
                'shelf_location_en' => 'A-01-02',
                'status' => 'available',
                'notes' => 'جاهز للاستخدام',
                'notes_en' => 'Ready for use',
                'created_by' => 1,
            ],
            [
                'barcode' => 'WH-001-2025-003',
                'batch_number' => 'BATCH-003',
                'name_ar' => 'لفائف حديد 8 ملم',
                'name_en' => 'Iron Coils 8mm',
                'material_type_id' => $coilType?->id,
                'unit_id' => $rollUnit?->id,
                'warehouse_id' => $mainWarehouse?->id,
                'type' => 'manufactured',
                'shelf_location' => 'B-01-01',
                'shelf_location_en' => 'B-01-01',
                'status' => 'available',
                'notes' => 'لفائف جاهزة للتعبئة',
                'notes_en' => 'Coils ready for packaging',
                'created_by' => 1,
            ],
        ];

        foreach ($materials as $material) {
            Material::firstOrCreate(
                ['barcode' => $material['barcode']],
                $material
            );
        }

        $this->command->info('✅ تم إنشاء المواد بنجاح');
    }
}
