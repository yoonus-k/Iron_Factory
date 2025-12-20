<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductionStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            [
                'stage_code' => 'stage_1',
                'stage_name' => 'المرحلة الأولى',
                'stage_name_en' => 'Stage 1',
                'stage_order' => 1,
                'description' => 'مرحلة التحضير الأولية',
                'estimated_duration' => 60, // بالدقائق
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'stage_code' => 'stage_2',
                'stage_name' => 'المرحلة الثانية',
                'stage_name_en' => 'Stage 2',
                'stage_order' => 2,
                'description' => 'مرحلة المعالجة المتوسطة',
                'estimated_duration' => 90,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'stage_code' => 'stage_3',
                'stage_name' => 'المرحلة الثالثة',
                'stage_name_en' => 'Stage 3',
                'stage_order' => 3,
                'description' => 'مرحلة المعالجة المتقدمة',
                'estimated_duration' => 120,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'stage_code' => 'stage_4',
                'stage_name' => 'المرحلة الرابعة',
                'stage_name_en' => 'Stage 4',
                'stage_order' => 4,
                'description' => 'مرحلة التشطيب النهائي',
                'estimated_duration' => 60,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($stages as $stage) {
            DB::table('production_stages')->updateOrInsert(
                ['stage_code' => $stage['stage_code']],
                $stage
            );
        }

        $this->command->info('✅ تم إضافة ' . count($stages) . ' مراحل إنتاجية بنجاح!');
    }
}
