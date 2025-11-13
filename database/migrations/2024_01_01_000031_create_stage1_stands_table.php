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
        Schema::create('stage1_stands', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 50)->unique()->comment('ST1-XXX-2025');
            $table->string('parent_barcode', 50)->comment('WH-XXX-2025');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->string('stand_number', 50)->nullable();
            $table->string('wire_size', 20)->nullable();
            $table->decimal('weight', 10, 3);
            $table->decimal('waste', 10, 3)->default(0);
            $table->decimal('remaining_weight', 10, 3);
            $table->enum('status', ['created', 'in_process', 'completed', 'consumed'])->default('created');
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
        Schema::dropIfExists('stage1_stands');
    }
};
