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
        Schema::create('delivery_note_coils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_note_id')->constrained('delivery_notes')->onDelete('cascade');
            $table->string('coil_number')->nullable()->comment('رقم الكويل - اختياري أو مولد تلقائياً');
            $table->decimal('coil_weight', 10, 3)->comment('وزن الكويل الأصلي (كجم)');
            $table->decimal('remaining_weight', 10, 3)->comment('الوزن المتبقي (كجم)');
            $table->string('coil_barcode')->unique()->comment('باركود الكويل الفريد');
            $table->enum('status', ['available', 'partially_used', 'fully_used'])->default('available')->comment('حالة الكويل');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->timestamps();

            // Indexes
            $table->index('delivery_note_id');
            $table->index('coil_barcode');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_note_coils');
    }
};
