<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\MaterialType;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class DefaultMaterialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // البحث عن الوحدات أو إنشاؤها
        $unitCount = Unit::firstOrCreate(
            ['unit_code' => 'COUNT'],
            [
                'unit_name' => 'عدد',
                'unit_symbol' => 'عدد',
                'unit_name_en' => 'Count',
                'unit_type' => 'quantity',
                'conversion_factor' => 1,
                'base_unit' => 'عدد',
                'is_active' => true,
            ]
        );

        $unitKg = Unit::firstOrCreate(
            ['unit_code' => 'KG'],
            [
                'unit_name' => 'كيلو',
                'unit_symbol' => 'كجم',
                'unit_name_en' => 'Kilogram',
                'unit_type' => 'weight',
                'conversion_factor' => 1,
                'base_unit' => 'كيلو',
                'is_active' => true,
            ]
        );

        // إنشاء أنواع المواد
        $typeCarton = MaterialType::firstOrCreate(
            ['type_code' => 'CARTON'],
            [
                'type_name' => 'كرتون',
                'type_name_en' => 'Carton',
                'description' => 'مواد التعبئة والتغليف - كرتون',
                'description_en' => 'Packaging Materials - Carton',
                'default_unit' => 'عدد',
                'is_active' => true,
            ]
        );

        $typePlastic = MaterialType::firstOrCreate(
            ['type_code' => 'PLASTIC'],
            [
                'type_name' => 'بلاستيك',
                'type_name_en' => 'Plastic',
                'description' => 'مواد التعبئة والتغليف - بلاستيك',
                'description_en' => 'Packaging Materials - Plastic',
                'default_unit' => 'كيلو',
                'is_active' => true,
            ]
        );

        $typeDye = MaterialType::firstOrCreate(
            ['type_code' => 'DYE'],
            [
                'type_name' => 'صبغة',
                'type_name_en' => 'Dye',
                'description' => 'مواد كيميائية - صبغات وألوان',
                'description_en' => 'Chemical Materials - Dyes and Colors',
                'default_unit' => 'كيلو',
                'is_active' => true,
            ]
        );

        // إضافة المواد الثابتة

        // 1. كرتون
        $this->createMaterialIfNotExists([
            'name_ar' => 'كرتون',
            'name_en' => 'Carton',
            'material_type_id' => $typeCarton->id,
            'unit_id' => $unitCount->id,
            'type' => 'raw',
            'barcode' => 'MAT-CARTON-001',
            'status' => 'available',
            'notes' => 'مادة ثابتة - كرتون للتعبئة والتغليف',
            'created_by' => 1, // المستخدم الأول (Admin)
        ]);

        // 2. بلاستيك
        $this->createMaterialIfNotExists([
            'name_ar' => 'بلاستيك',
            'name_en' => 'Plastic',
            'material_type_id' => $typePlastic->id,
            'unit_id' => $unitKg->id,
            'type' => 'raw',
            'barcode' => 'MAT-PLASTIC-001',
            'status' => 'available',
            'notes' => 'مادة ثابتة - بلاستيك للتعبئة والتغليف',
            'created_by' => 1,
        ]);

        // 3. الألوان (صبغات)
        $colors = [
            ['ar' => 'أحمر', 'en' => 'Red', 'code' => 'RED'],
            ['ar' => 'أزرق', 'en' => 'Blue', 'code' => 'BLUE'],
            ['ar' => 'أخضر', 'en' => 'Green', 'code' => 'GREEN'],
            ['ar' => 'أصفر', 'en' => 'Yellow', 'code' => 'YELLOW'],
            ['ar' => 'برتقالي', 'en' => 'Orange', 'code' => 'ORANGE'],
            ['ar' => 'أسود', 'en' => 'Black', 'code' => 'BLACK'],
            ['ar' => 'أبيض', 'en' => 'White', 'code' => 'WHITE'],
            ['ar' => 'رمادي', 'en' => 'Gray', 'code' => 'GRAY'],
            ['ar' => 'بني', 'en' => 'Brown', 'code' => 'BROWN'],
            ['ar' => 'بنفسجي', 'en' => 'Purple', 'code' => 'PURPLE'],
            ['ar' => 'وردي', 'en' => 'Pink', 'code' => 'PINK'],
            ['ar' => 'ذهبي', 'en' => 'Gold', 'code' => 'GOLD'],
            ['ar' => 'فضي', 'en' => 'Silver', 'code' => 'SILVER'],
        ];

        foreach ($colors as $color) {
            $this->createMaterialIfNotExists([
                'name_ar' => 'صبغة ' . $color['ar'],
                'name_en' => $color['en'] . ' Dye',
                'material_type_id' => $typeDye->id,
                'unit_id' => $unitKg->id,
                'type' => 'raw',
                'barcode' => 'MAT-DYE-' . $color['code'],
                'status' => 'available',
                'notes' => 'صبغة لون ' . $color['ar'] . ' - مادة ثابتة',
                'created_by' => 1,
            ]);
        }

        $this->command->info('✅ تم إضافة المواد الثابتة بنجاح!');
        $this->command->info('   - كرتون (عدد)');
        $this->command->info('   - بلاستيك (كيلو)');
        $this->command->info('   - ' . count($colors) . ' صبغة ألوان (كيلو)');
    }

    /**
     * إنشاء مادة إذا لم تكن موجودة
     */
    private function createMaterialIfNotExists(array $data): void
    {
        $existing = Material::where('barcode', $data['barcode'])->first();
        
        if (!$existing) {
            Material::create($data);
            $this->command->info("   ✓ تم إضافة: {$data['name_ar']}");
        } else {
            $this->command->warn("   ⚠ موجود مسبقاً: {$data['name_ar']}");
        }
    }
}
