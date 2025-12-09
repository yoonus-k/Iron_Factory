<?php

namespace Database\Seeders;

use App\Helpers\TranslationHelper;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class UnitTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $userId = $user?->id ?? 1;

        $units = [
            [
                'unit_code' => 'KG',
                'ar' => ['name' => 'كيلوجرام', 'symbol' => 'كج', 'description' => 'وحدة قياس الكتلة'],
                'en' => ['name' => 'Kilogram', 'symbol' => 'kg', 'description' => 'Unit of mass'],
                'hi' => ['name' => 'किलोग्राम', 'symbol' => 'kg', 'description' => 'द्रव्यमान की इकाई'],
                'ur' => ['name' => 'کلوگرام', 'symbol' => 'کج', 'description' => 'بڑے پن کی پیمائش'],
            ],

            [
                'unit_code' => 'M',
                'ar' => ['name' => 'متر', 'symbol' => 'م', 'description' => 'وحدة قياس الطول'],
                'en' => ['name' => 'Meter', 'symbol' => 'm', 'description' => 'Unit of length'],
                'hi' => ['name' => 'मीटर', 'symbol' => 'm', 'description' => 'लंबाई की इकाई'],
                'ur' => ['name' => 'میٹر', 'symbol' => 'م', 'description' => 'لمبائی کی پیمائش'],
            ],
            [
                'unit_code' => 'PIECE',
                'ar' => ['name' => 'قطعة', 'symbol' => 'قط', 'description' => 'وحدة عد'],
                'en' => ['name' => 'Piece', 'symbol' => 'pcs', 'description' => 'Unit count'],
                'hi' => ['name' => 'टुकड़ा', 'symbol' => 'टुक', 'description' => 'गणना की इकाई'],
                'ur' => ['name' => 'ٹکڑا', 'symbol' => 'ٹک', 'description' => 'شمار کی پیمائش'],
            ],
            [
                'unit_code' => 'BOX',
                'ar' => ['name' => 'صندوق', 'symbol' => 'صن', 'description' => 'وحدة التعبئة'],
                'en' => ['name' => 'Box', 'symbol' => 'box', 'description' => 'Packaging unit'],
                'hi' => ['name' => 'डिब्बा', 'symbol' => 'डिब', 'description' => 'पैकेजिंग यूनिट'],
                'ur' => ['name' => 'ڈبہ', 'symbol' => 'ڈب', 'description' => 'پیکنج کی پیمائش'],
            ],
        ];

        foreach ($units as $unitData) {
            $code = $unitData['unit_code'];
            $ar = $unitData['ar'];
            $en = $unitData['en'];
            $hi = $unitData['hi'];
            $ur = $unitData['ur'];

            // البحث عن الوحدة أو إنشاء جديدة
            $unit = Unit::firstOrCreate(
                ['unit_code' => $code],
                [
                    'unit_name' => $ar['name'],
                    'unit_name_en' => $en['name'],
                    'unit_symbol' => $ar['symbol'],
                    'unit_type' => in_array($code, ['KG', 'G', 'T']) ? 'weight' : 'quantity',
                    'conversion_factor' => 1,
                    'base_unit' => $code,
                    'description' => $ar['description'],
                    'is_active' => true,
                    'created_by' => $userId,
                ]
            );

            // حفظ الترجمات
            TranslationHelper::saveForAllLanguages('App\Models\Unit', $unit->id, [
                'ar' => $ar,
                'en' => $en,
                'hi' => $hi,
                'ur' => $ur,
            ]);
        }

        $this->command->info('✅ Units with translations seeded successfully!');
    }
}
