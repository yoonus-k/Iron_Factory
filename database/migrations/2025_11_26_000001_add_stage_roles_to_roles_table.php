<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة أدوار المراحل الإنتاجية
        DB::table('roles')->insert([
            [
                'role_name' => 'عامل المرحلة الأولى',
                'role_name_en' => 'Stage 1 Worker',
                'role_code' => 'STAGE1_WORKER',
                'description' => 'عامل متخصص في المرحلة الأولى - الاستاندات والتقطيع',
                'level' => 25,
                'is_system' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'عامل المرحلة الثانية',
                'role_name_en' => 'Stage 2 Worker',
                'role_code' => 'STAGE2_WORKER',
                'description' => 'عامل متخصص في المرحلة الثانية - المعالجة والتشكيل',
                'level' => 25,
                'is_system' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'عامل المرحلة الثالثة',
                'role_name_en' => 'Stage 3 Worker',
                'role_code' => 'STAGE3_WORKER',
                'description' => 'عامل متخصص في المرحلة الثالثة - اللفائف والتجميع',
                'level' => 25,
                'is_system' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'عامل المرحلة الرابعة',
                'role_name_en' => 'Stage 4 Worker',
                'role_code' => 'STAGE4_WORKER',
                'description' => 'عامل متخصص في المرحلة الرابعة - التعبئة والتغليف',
                'level' => 25,
                'is_system' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->whereIn('role_code', [
            'STAGE1_WORKER',
            'STAGE2_WORKER',
            'STAGE3_WORKER',
            'STAGE4_WORKER'
        ])->delete();
    }
};
