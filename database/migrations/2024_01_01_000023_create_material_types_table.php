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
        Schema::create('material_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_code', 50)->unique()->comment('رمز نوع المادة');
            $table->string('type_name')->comment('اسم نوع المادة بالعربية');
            $table->string('type_name_en')->nullable()->comment('اسم نوع المادة بالإنجليزية');

            $table->text('description')->nullable()->comment('وصف المادة بالعربية');
            $table->text('description_en')->nullable()->comment('وصف المادة بالإنجليزية');
            $table->json('specifications')->nullable()->comment('المواصفات التقنية');
            $table->string('default_unit', 20)->default('kg')->comment('الوحدة الافتراضية');
            $table->decimal('standard_cost', 10, 2)->nullable()->comment('التكلفة القياسية');
            $table->string('storage_conditions')->nullable()->comment('شروط التخزين');
            $table->string('storage_conditions_en')->nullable()->comment('شروط التخزين بالإنجليزية');
            $table->integer('shelf_life_days')->nullable()->comment('مدة الصلاحية بالأيام');
            $table->boolean('is_active')->default(true)->comment('حالة المادة');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('type_code');

            $table->index('is_active');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_types');
    }
};
