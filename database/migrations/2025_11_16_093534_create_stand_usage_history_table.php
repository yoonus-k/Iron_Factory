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
        Schema::create('stand_usage_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stand_id')->constrained('stands')->onDelete('cascade')->comment('رقم الاستاند');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('المستخدم');
            $table->foreignId('material_id')->nullable()->comment('المادة الخام المستخدمة');
            $table->string('material_barcode')->nullable()->comment('باركود المادة');
            $table->string('material_type')->nullable()->comment('نوع المادة');
            $table->decimal('wire_size', 8, 2)->nullable()->comment('مقاس السلك مم');
            $table->decimal('total_weight', 10, 2)->comment('الوزن الإجمالي كجم');
            $table->decimal('net_weight', 10, 2)->comment('الوزن الصافي كجم');
            $table->decimal('stand_weight', 10, 2)->comment('وزن الاستاند الفارغ كجم');
            $table->decimal('waste_percentage', 5, 2)->default(0)->comment('نسبة الهدر %');
            $table->decimal('cost', 10, 2)->nullable()->comment('التكلفة');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->enum('status', ['in_use', 'completed', 'returned'])->default('in_use')->comment('حالة الاستخدام');
            $table->timestamp('started_at')->useCurrent()->comment('وقت البدء');
            $table->timestamp('completed_at')->nullable()->comment('وقت الانتهاء');
            $table->timestamps();
            
            $table->index('stand_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stand_usage_history');
    }
};
