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
        Schema::create('production_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_note_id')->constrained('delivery_notes')->onDelete('cascade')->comment('معرف أذن التسليم');
            $table->foreignId('batch_id')->nullable()->constrained('material_batches')->onDelete('set null')->comment('معرف الدفعة');
            $table->string('stage_code', 50)->comment('كود المرحلة');
            $table->foreignId('assigned_to')->constrained('users')->comment('الموظف المكلف بالاستلام');
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending')->comment('حالة التأكيد');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->comment('الموظف الذي أكد الاستلام');
            $table->timestamp('confirmed_at')->nullable()->comment('وقت التأكيد');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->comment('الموظف الذي رفض');
            $table->timestamp('rejected_at')->nullable()->comment('وقت الرفض');
            $table->text('rejection_reason')->nullable()->comment('سبب الرفض');
            $table->decimal('actual_received_quantity', 10, 2)->nullable()->comment('الكمية المستلمة فعلياً');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index('delivery_note_id');
            $table->index('batch_id');
            $table->index('stage_code');
            $table->index('assigned_to');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_confirmations');
    }
};
