<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            
            // معلومات العامل الأساسية
            $table->string('worker_code')->unique()->comment('كود العامل');
            $table->string('name')->comment('اسم العامل');
            $table->string('national_id')->unique()->nullable()->comment('رقم الهوية');
            $table->string('phone')->nullable()->comment('رقم الهاتف');
            $table->string('email')->unique()->nullable()->comment('البريد الإلكتروني');
            
            // معلومات العمل
            $table->enum('position', ['worker', 'supervisor', 'technician', 'quality_inspector'])->default('worker')->comment('المنصب');
            $table->json('allowed_stages')->nullable()->comment('المراحل المسموح بها');
            $table->decimal('hourly_rate', 8, 2)->default(0)->comment('الأجر بالساعة');
            $table->enum('shift_preference', ['morning', 'evening', 'night', 'any'])->default('any')->comment('تفضيل الوردية');
            
            // حالة العامل
            $table->boolean('is_active')->default(true)->comment('هل العامل نشط');
            $table->date('hire_date')->nullable()->comment('تاريخ التعيين');
            $table->date('termination_date')->nullable()->comment('تاريخ إنهاء العمل');
            
            // معلومات إضافية
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->string('emergency_contact')->nullable()->comment('جهة الاتصال للطوارئ');
            $table->string('emergency_phone')->nullable()->comment('هاتف الطوارئ');
            
            // ربط بجدول المستخدمين (اختياري)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment('حساب المستخدم');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes للأداء
            $table->index('worker_code');
            $table->index('position');
            $table->index('is_active');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
