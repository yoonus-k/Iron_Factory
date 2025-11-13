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
        Schema::create('stage3_coils', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 50)->unique()->comment('CO3-XXX-2025');
            $table->string('parent_barcode', 50)->comment('ST2-XXX-2025');
            $table->foreignId('stage2_id')->constrained('stage2_processed')->onDelete('cascade');
            $table->string('coil_number', 50)->nullable();
            $table->string('wire_size', 20)->nullable();
            $table->decimal('base_weight', 10, 3);
            $table->decimal('dye_weight', 10, 3)->default(0);
            $table->decimal('plastic_weight', 10, 3)->default(0);
            $table->decimal('total_weight', 10, 3);
            $table->string('color', 50)->nullable();
            $table->decimal('waste', 10, 3)->default(0);
            $table->string('dye_type', 100)->nullable()->comment('نوع الصبغة المستخدمة');
            $table->string('plastic_type', 100)->nullable()->comment('نوع البلاستيك المستخدم');
            $table->enum('status', ['created', 'in_process', 'completed', 'packed'])->default('created');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('barcode');
            $table->index('parent_barcode');
            $table->index('status');
            $table->index('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage3_coils');
    }
};
