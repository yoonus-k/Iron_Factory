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
            $table->string('coil_number', 50)->nullable()->comment('رقم الملف');
            $table->string('coil_number_en', 50)->nullable()->comment('Coil number');
            $table->string('wire_size', 20)->nullable()->comment('حجم السلك');
            $table->string('wire_size_en', 20)->nullable()->comment('Wire size');
            $table->decimal('base_weight', 10, 3)->comment('الوزن الأساسي');
            $table->decimal('dye_weight', 10, 3)->default(0)->comment('وزن الصبغة');
            $table->decimal('plastic_weight', 10, 3)->default(0)->comment('وزن البلاستيك');
            $table->decimal('total_weight', 10, 3)->comment('الوزن الكلي');
            $table->string('color', 50)->nullable()->comment('اللون');
            $table->string('color_en', 50)->nullable()->comment('Color');
            $table->decimal('waste', 10, 3)->default(0);
            $table->string('dye_type', 100)->nullable()->comment('نوع الصبغة المستخدمة');
            $table->string('dye_type_en', 100)->nullable()->comment('نوع الصبغة المستخدمة بالإنجليزية');
            $table->string('plastic_type', 100)->nullable()->comment('نوع البلاستيك المستخدم');
            $table->string('plastic_type_en', 100)->nullable()->comment('نوع البلاستيك المستخدم بالإنجليزية');
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
