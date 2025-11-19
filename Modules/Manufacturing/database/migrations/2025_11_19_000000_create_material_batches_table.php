<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('material_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->string('batch_code')->unique()->comment('الباركود الخاص بالدفعة');
            $table->decimal('initial_quantity', 15, 3)->comment('كمية الدفعة الأصلية');
            $table->decimal('available_quantity', 15, 3)->comment('الكمية المتبقية');
            $table->date('batch_date')->nullable()->comment('تاريخ استلام الدفعة');
            $table->date('expire_date')->nullable()->comment('تاريخ انتهاء الصلاحية إن وجد');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null')->comment('المستودع الحالي');
            $table->decimal('unit_price', 15, 2)->nullable()->comment('سعر الوحدة');
            $table->decimal('total_value', 15, 2)->nullable()->comment('القيمة الإجمالية للدفعة');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('batch_code');
            $table->index('material_id');
            $table->index('warehouse_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_batches');
    }
};
