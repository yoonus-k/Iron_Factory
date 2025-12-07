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
        Schema::create('stage_suspensions', function (Blueprint $table) {
            $table->id();
            $table->integer('stage_number')->comment('رقم المرحلة (1, 2, 3, 4)');
            $table->string('batch_barcode')->comment('باركود الدفعة');
            $table->foreignId('batch_id')->nullable()->comment('معرف الدفعة');
            $table->decimal('input_weight', 10, 2)->comment('الوزن المدخل');
            $table->decimal('output_weight', 10, 2)->comment('الوزن الناتج');
            $table->decimal('waste_weight', 10, 2)->comment('وزن الهدر');
            $table->decimal('waste_percentage', 5, 2)->comment('نسبة الهدر المئوية');
            $table->decimal('allowed_percentage', 5, 2)->comment('النسبة المسموح بها');
            $table->enum('status', ['suspended', 'approved', 'rejected'])->default('suspended')->comment('حالة الإيقاف');
            $table->text('suspension_reason')->nullable()->comment('سبب الإيقاف');
            $table->foreignId('suspended_by')->nullable()->constrained('users')->comment('من قام بالإيقاف (النظام أو مستخدم)');
            $table->timestamp('suspended_at')->comment('وقت الإيقاف');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->comment('من راجع الإيقاف');
            $table->timestamp('reviewed_at')->nullable()->comment('وقت المراجعة');
            $table->text('review_notes')->nullable()->comment('ملاحظات المراجعة');
            $table->json('additional_data')->nullable()->comment('بيانات إضافية (JSON)');
            $table->timestamps();

            $table->index(['stage_number', 'status']);
            $table->index('batch_barcode');
            $table->index('suspended_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage_suspensions');
    }
};
