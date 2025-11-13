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
            $table->string('warehouse_name')->comment('اسم المستودع');
            $table->enum('warehouse_type', ['raw_materials', 'additives', 'finished_goods', 'general'])->default('raw_materials')
                ->comment('نوع المستودع');
            $table->string('location')->nullable()->comment('موقع المستودع');
            $table->text('description')->nullable()->comment('وصف المستودع');
            $table->decimal('capacity', 12, 3)->nullable()->comment('السعة التخزينية');
            $table->string('capacity_unit', 20)->default('kg')->comment('وحدة السعة');
            $table->string('manager_name')->nullable()->comment('مسؤول المستودع');
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
