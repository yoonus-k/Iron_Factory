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
        Schema::create('product_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->comment('الباركود المتتبع');
            $table->string('stage')->comment('warehouse, stage1, stage2, stage3, stage4');
            $table->string('action')->comment('received, created, processed, moved, quality_check, packed, shipped');
            $table->string('input_barcode')->nullable()->comment('الباركود المستخدم كمدخل');
            $table->string('output_barcode')->nullable()->comment('الباركود الناتج');
            
            // الأوزان والهدر
            $table->decimal('input_weight', 10, 2)->nullable();
            $table->decimal('output_weight', 10, 2)->nullable();
            $table->decimal('waste_amount', 10, 2)->nullable();
            $table->decimal('waste_percentage', 5, 2)->nullable();
            
            // التكلفة
            $table->decimal('cost', 10, 2)->nullable();
            
            // الموظف والوردية
            $table->unsignedBigInteger('worker_id')->nullable();
            $table->unsignedBigInteger('shift_id')->nullable();
            
            // ملاحظات وبيانات إضافية
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable()->comment('تفاصيل إضافية خاصة بكل مرحلة');
            
            $table->timestamps();

            // فهارس
            $table->index('barcode');
            $table->index('stage');
            $table->index('action');
            $table->index('input_barcode');
            $table->index('output_barcode');
            $table->index('worker_id');
            $table->index('shift_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_tracking');
    }
};
