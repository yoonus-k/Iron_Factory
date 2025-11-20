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
        Schema::create('material_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade')->comment('المادة');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade')->comment('الوحدة');
            $table->string('batch_code', 100)->unique()->comment('الباركود الخاص بالدفعة');
            $table->decimal('initial_quantity', 10, 3)->comment('كمية الدفعة الأصلية');
            $table->decimal('available_quantity', 10, 3)->comment('الكمية المتبقية');
            $table->date('batch_date')->comment('تاريخ استلام الدفعة');
            $table->date('expire_date')->nullable()->comment('تاريخ انتهاء الصلاحية إن وجد');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null')->comment('المستودع');
            $table->decimal('unit_price', 10, 2)->nullable()->comment('سعر الوحدة');
            $table->decimal('total_value', 15, 2)->nullable()->comment('القيمة الإجمالية للدفعة');
            $table->enum('status', ['available', 'in_production', 'consumed'])->default('available')->comment('حالة الدفعة');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->timestamps();

            // Indexes
            $table->index('batch_code');
            $table->index('material_id');
            $table->index('warehouse_id');
            $table->index('status');
            $table->index('batch_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_batches');
    }
};
