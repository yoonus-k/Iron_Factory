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
        Schema::create('barcodes', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique()->comment('الباركود الفريد');
            $table->string('type')->comment('raw_material, stage1, stage2, stage3, stage4');
            $table->unsignedBigInteger('reference_id')->comment('ID المنتج في جدوله');
            $table->string('reference_table')->comment('اسم جدول المنتج');
            $table->enum('status', ['active', 'scanned', 'used', 'expired'])->default('active');
            $table->integer('scan_count')->default(0)->comment('عدد مرات المسح');
            $table->timestamp('last_scanned_at')->nullable();
            $table->json('metadata')->nullable()->comment('بيانات إضافية');
            $table->timestamps();

            // فهارس للبحث السريع
            $table->index('barcode');
            $table->index('type');
            $table->index(['reference_table', 'reference_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcodes');
    }
};
