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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('warehouse_code', 50)->unique()->comment('رمز المستودع');
            $table->string('warehouse_name')->comment('اسم المستودع بالعربية');
            $table->string('warehouse_name_en')->nullable()->comment('اسم المستودع بالإنجليزية');
            $table->enum('warehouse_type', ['raw_materials', 'additives', 'finished_goods', 'general'])->default('raw_materials')
                ->comment('نوع المستودع');
            $table->string('location')->nullable()->comment('موقع المستودع بالعربية');
            $table->string('location_en')->nullable()->comment('موقع المستودع بالإنجليزية');
            $table->text('description')->nullable()->comment('وصف المستودع بالعربية');
            $table->text('description_en')->nullable()->comment('وصف المستودع بالإنجليزية');
            $table->decimal('capacity', 12, 3)->nullable()->comment('السعة التخزينية');
            $table->string('capacity_unit', 20)->default('kg')->comment('وحدة السعة');
            $table->string('manager_name')->nullable()->comment('مسؤول المستودع');
            $table->string('manager_name_en')->nullable()->comment('مسؤول المستودع بالإنجليزية');
            $table->string('contact_number', 50)->nullable()->comment('رقم التواصل');
            $table->boolean('is_active')->default(true)->comment('حالة المستودع');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('warehouse_code');
            $table->index('warehouse_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
