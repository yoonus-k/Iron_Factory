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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            
            // الرمز التلقائي
            $table->string('customer_code', 20)->unique()->comment('رمز العميل - مثال: CUST-2025-0001');
            
            // البيانات الأساسية
            $table->string('name', 200)->comment('اسم العميل');
            $table->string('company_name', 200)->nullable()->comment('اسم الشركة');
            $table->string('phone', 20)->comment('رقم الهاتف');
            $table->string('email', 100)->nullable()->comment('البريد الإلكتروني');
            
            // العنوان
            $table->text('address')->nullable()->comment('العنوان');
            $table->string('city', 100)->nullable()->comment('المدينة');
            $table->string('country', 100)->default('السعودية')->comment('الدولة');
            
            // بيانات ضريبية
            $table->string('tax_number', 50)->nullable()->comment('الرقم الضريبي');
            
            // الحالة
            $table->boolean('is_active')->default(true)->comment('نشط / غير نشط');
            
            // ملاحظات
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            // من أنشأه
            $table->foreignId('created_by')->constrained('users')->comment('المستخدم الذي أنشأ العميل');
            
            $table->timestamps();
            $table->softDeletes();
            
            // الفهارس
            $table->index('customer_code');
            $table->index('name');
            $table->index('phone');
            $table->index('is_active');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
