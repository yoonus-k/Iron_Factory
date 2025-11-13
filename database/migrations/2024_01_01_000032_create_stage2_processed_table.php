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
        Schema::create('stage2_processed', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 50)->unique()->comment('ST2-XXX-2025');
            $table->string('parent_barcode', 50)->comment('ST1-XXX-2025');
            $table->foreignId('stage1_id')->constrained('stage1_stands')->onDelete('cascade');
            $table->text('process_details')->nullable()->comment('تفاصيل المعالجة بالعربية');
            $table->text('process_details_en')->nullable()->comment('تفاصيل المعالجة بالإنجليزية');
            $table->decimal('input_weight', 10, 3);
            $table->decimal('output_weight', 10, 3);
            $table->decimal('waste', 10, 3)->default(0);
            $table->decimal('remaining_weight', 10, 3);
            $table->enum('status', ['started', 'in_progress', 'completed', 'consumed'])->default('started');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('barcode');
            $table->index('parent_barcode');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage2_processed');
    }
};
