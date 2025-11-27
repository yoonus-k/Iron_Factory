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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name')->unique()->comment('اسم الدور');
            $table->string('role_name_en')->unique()->comment('اسم الدور بالإنجليزية');
            $table->string('role_code', 50)->unique()->comment('رمز الدور');
            $table->text('description')->nullable()->comment('وصف الدور');
            $table->integer('level')->default(0)->comment('مستوى الصلاحية (0-100)');
            $table->boolean('is_system')->default(false)->comment('دور نظام (لا يمكن حذفه)');
            $table->boolean('is_active')->default(true)->comment('حالة الدور');
            $table->bigInteger('created_by')->unsigned()->nullable()->comment('من أنشأ الدور');
            $table->timestamps();

            $table->index('role_code');
            $table->index('is_active');
            $table->index('level');
        });

        // إدراج الأدوار الافتراضية
        DB::table('roles')->insert([
            [
                'role_name' => 'مدير عام',
                'role_name_en' => 'Admin',
                'role_code' => 'ADMIN',
                'description' => 'صلاحيات كاملة على جميع أقسام النظام',
                'level' => 100,
                'is_system' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'مدير إنتاج',
                'role_name_en' => 'Production Manager',
                'role_code' => 'MANAGER',
                'description' => 'إدارة الإنتاج والتقارير والموافقات',
                'level' => 80,
                'is_system' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'مشرف',
                'role_name_en' => 'Supervisor',
                'role_code' => 'SUPERVISOR',
                'description' => 'مراقبة المراحل الإنتاجية والموافقة على العمليات',
                'level' => 60,
                'is_system' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'عامل',
                'role_name_en' => 'Worker',
                'role_code' => 'WORKER',
                'description' => 'تنفيذ العمليات وإدخال البيانات',
                'level' => 20,
                'is_system' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'أمين مستودع',
                'role_name_en' => 'Warehouse Keeper',
                'role_code' => 'WAREHOUSE_KEEPER',
                'description' => 'إدارة المستودعات والمواد',
                'level' => 40,
                'is_system' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'محاسب',
                'role_name_en' => 'Accountant',
                'role_code' => 'ACCOUNTANT',
                'description' => 'إدارة الفواتير والتكاليف',
                'level' => 50,
                'is_system' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_name' => 'عامل المرحلة الأولى',
                'role_name_en' => 'Stage 1 Worker',
                'role_code' => 'STAGE1_WORKER',
                'description' => 'عامل متخصص في المرحلة الأولى من الإنتاج',
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
                'description' => 'عامل متخصص في المرحلة الثانية من الإنتاج',
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
                'description' => 'عامل متخصص في المرحلة الثالثة من الإنتاج',
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
                'description' => 'عامل متخصص في المرحلة الرابعة من الإنتاج',
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
        Schema::dropIfExists('roles');
    }
};
