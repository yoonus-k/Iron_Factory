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
        Schema::create('iron_journey_logs', function (Blueprint $table) {
            $table->id();
            
            // معلومات المنتج
            $table->string('barcode')->index()->comment('الباركود المرتبط (من أي مرحلة)');
            $table->enum('stage_type', ['warehouse', 'stage1', 'stage2', 'stage3', 'stage4'])->comment('نوع المرحلة');
            $table->string('stage_name')->comment('اسم المرحلة');
            $table->string('product_type')->nullable()->comment('نوع المنتج');
            
            // بيانات الوزن
            $table->decimal('input_weight', 10, 2)->nullable()->comment('وزن المدخل (كجم)');
            $table->decimal('output_weight', 10, 2)->nullable()->comment('وزن المخرج (كجم)');
            $table->decimal('waste_amount', 10, 2)->default(0)->comment('كمية الهدر (كجم)');
            $table->decimal('waste_percentage', 5, 2)->default(0)->comment('نسبة الهدر (%)');
            
            // معلومات العملية
            $table->foreignId('worker_id')->nullable()->constrained('users')->nullOnDelete()->comment('معرّف العامل');
            $table->string('worker_name')->nullable()->comment('اسم العامل');
            $table->enum('status', ['pending', 'in-progress', 'completed', 'issue'])->default('pending')->comment('حالة المرحلة');
            
            // التوقيت
            $table->timestamp('started_at')->nullable()->comment('وقت البدء');
            $table->timestamp('completed_at')->nullable()->comment('وقت الإكمال');
            $table->integer('duration_minutes')->nullable()->comment('المدة بالدقائق');
            
            // معلومات إضافية
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->json('metadata')->nullable()->comment('بيانات إضافية (JSON)');
            $table->decimal('cost', 10, 2)->nullable()->comment('التكلفة');
            $table->integer('quality_score')->nullable()->comment('درجة الجودة (0-100)');
            
            // علاقات الربط
            $table->string('parent_barcode')->nullable()->index()->comment('باركود المرحلة السابقة');
            $table->string('child_barcode')->nullable()->index()->comment('باركود المرحلة التالية');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes للأداء
            $table->index(['barcode', 'stage_type']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iron_journey_logs');
    }
};
