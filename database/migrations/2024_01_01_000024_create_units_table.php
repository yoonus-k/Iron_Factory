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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code', 20)->unique()->comment('رمز الوحدة');
            $table->string('unit_name')->comment('اسم الوحدة');
            $table->string('unit_name_en')->comment('اسم الوحدة بالإنجليزية');
            $table->string('unit_symbol', 10)->comment('رمز الوحدة المختصر');
            $table->enum('unit_type', ['weight', 'length', 'volume', 'count', 'area'])->comment('نوع الوحدة');
            $table->decimal('conversion_factor', 12, 6)->default(1)->nullable()->comment('معامل التحويل للوحدة الأساسية');
            $table->string('base_unit', 20)->nullable()->comment('الوحدة الأساسية للتحويل');
            $table->text('description')->nullable()->comment('وصف الوحدة');
            $table->boolean('is_active')->default(true)->comment('حالة الوحدة');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('unit_code');
            $table->index('unit_type');
            $table->index('is_active');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
