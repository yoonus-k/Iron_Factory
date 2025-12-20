<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaterialType;
use App\Models\Unit;

class MaterialTypesAndUnitsSeeder extends Seeder
{
    public function run(): void
    {
        // أنواع المواد
        $materialTypes = [
            ['type_code' => 'MT-001', 'type_name' => 'حديد خام', 'type_name_en' => 'Raw Iron', 'default_unit' => 'kg', 'is_active' => true],
            ['type_code' => 'MT-002', 'type_name' => 'حديد مشغول', 'type_name_en' => 'Processed Iron', 'default_unit' => 'kg', 'is_active' => true],
            ['type_code' => 'MT-003', 'type_name' => 'أسلاك', 'type_name_en' => 'Wires', 'default_unit' => 'kg', 'is_active' => true],
            ['type_code' => 'MT-004', 'type_name' => 'لفائف', 'type_name_en' => 'Coils', 'default_unit' => 'roll', 'is_active' => true],
            ['type_code' => 'MT-005', 'type_name' => 'إضافات كيميائية', 'type_name_en' => 'Chemical Additives', 'default_unit' => 'L', 'is_active' => true],
        ];

        foreach ($materialTypes as $type) {
            MaterialType::firstOrCreate(
                ['type_code' => $type['type_code']],
                $type
            );
        }

        // الوحدات
        $units = [
            ['unit_code' => 'KG', 'unit_name' => 'كيلوجرام', 'unit_name_en' => 'Kilogram', 'unit_symbol' => 'kg', 'unit_type' => 'weight', 'conversion_factor' => 1, 'is_active' => true],
            ['unit_code' => 'TON', 'unit_name' => 'طن', 'unit_name_en' => 'Ton', 'unit_symbol' => 't', 'unit_type' => 'weight', 'conversion_factor' => 1000, 'base_unit' => 'kg', 'is_active' => true],
            ['unit_code' => 'PCS', 'unit_name' => 'قطعة', 'unit_name_en' => 'Piece', 'unit_symbol' => 'pcs', 'unit_type' => 'quantity', 'conversion_factor' => 1, 'is_active' => true],
            ['unit_code' => 'ROLL', 'unit_name' => 'لفة', 'unit_name_en' => 'Roll', 'unit_symbol' => 'roll', 'unit_type' => 'quantity', 'conversion_factor' => 1, 'is_active' => true],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['unit_code' => $unit['unit_code']],
                $unit
            );
        }

        $this->command->info('✅ تم إنشاء أنواع المواد والوحدات بنجاح');
    }
}
