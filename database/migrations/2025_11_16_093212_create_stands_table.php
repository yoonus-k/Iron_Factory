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
        Schema::create('stands', function (Blueprint $table) {
            $table->id();
            $table->string('stand_number')->unique()->comment('رقم الاستاند');
            $table->decimal('weight', 10, 2)->comment('وزن الاستاند بالكيلوغرام');
            $table->enum('status', ['unused', 'stage1', 'stage2', 'stage3', 'stage4', 'completed'])->default('unused')->comment('الحالة الحالية للاستاند');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->boolean('is_active')->default(true)->comment('حالة التفعيل');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('stand_number');
            $table->index('status');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stands');
    }
};
