<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarcodeSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // حذف البيانات القديمة
        DB::table('barcode_settings')->truncate();

        // إدراج الإعدادات الافتراضية
        DB::table('barcode_settings')->insert([
            [
                'type' => 'raw_material',
                'prefix' => 'RW',
                'current_number' => 0,
                'year' => date('Y'),
                'format' => '{prefix}-{year}-{number}',
                'auto_increment' => true,
                'padding' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'stage1',
                'prefix' => 'ST1',
                'current_number' => 0,
                'year' => date('Y'),
                'format' => '{prefix}-{year}-{number}',
                'auto_increment' => true,
                'padding' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'stage2',
                'prefix' => 'ST2',
                'current_number' => 0,
                'year' => date('Y'),
                'format' => '{prefix}-{year}-{number}',
                'auto_increment' => true,
                'padding' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'stage3',
                'prefix' => 'LF3',
                'current_number' => 0,
                'year' => date('Y'),
                'format' => '{prefix}-{year}-{number}',
                'auto_increment' => true,
                'padding' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'stage4',
                'prefix' => 'BOX4',
                'current_number' => 0,
                'year' => date('Y'),
                'format' => '{prefix}-{year}-{number}',
                'auto_increment' => true,
                'padding' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('✅ تم إضافة إعدادات الباركود الافتراضية بنجاح!');
    }
}
