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
        Schema::create('delivery_note_items', function (Blueprint $table) {
            $table->id();
            
            // الإذن
            $table->foreignId('delivery_note_id')->constrained('delivery_notes')->onDelete('cascade')->comment('رقم الإذن');
            
            // الكرتونة من المرحلة 4
            $table->foreignId('stage4_box_id')->constrained('stage4_boxes')->onDelete('cascade')->comment('رقم الكرتونة');
            
            // البيانات (نسخة للسرعة)
            $table->string('barcode', 50)->comment('باركود الكرتونة');
            $table->string('packaging_type', 100)->comment('نوع التغليف');
            $table->decimal('weight', 10, 3)->comment('الوزن');
            
            $table->timestamps();
            
            // الفهارس
            $table->index('delivery_note_id');
            $table->index('stage4_box_id');
            $table->index('barcode');
            $table->unique(['delivery_note_id', 'stage4_box_id'], 'unique_note_box');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_note_items');
    }
};
