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
        Schema::create('reconciliation_logs', function (Blueprint $table) {
            $table->id();

            // ========== الربط ==========
            $table->unsignedBigInteger('delivery_note_id');
            $table->unsignedBigInteger('purchase_invoice_id')->nullable();

            // ========== البيانات الفعلية ==========
            $table->decimal('actual_weight', 10, 2)->comment('من الميزان');
            $table->decimal('invoice_weight', 10, 2)->comment('من الفاتورة');

            // ========== الحسابات ==========
            $table->decimal('discrepancy_kg', 10, 2)
                ->virtualAs('actual_weight - invoice_weight');

            $table->decimal('discrepancy_percentage', 5, 2)
                ->virtualAs(
                    'CASE WHEN invoice_weight = 0 THEN 0
                    ELSE ((actual_weight - invoice_weight) / invoice_weight * 100) END'
                );

            $table->decimal('financial_impact', 12, 2)->nullable()
                ->comment('التأثير المالي');

            // ========== القرار المتخذ ==========
            $table->enum('action', [
                'accepted',
                'rejected',
                'adjusted',
                'negotiated',
                'pending'
            ])->default('pending');

            $table->string('reason')->nullable()->comment('سبب القرار');
            $table->text('comments')->nullable()->comment('تفاصيل إضافية');

            // ========== من اتخذ القرار ==========
            $table->unsignedBigInteger('decided_by');
            $table->timestamp('decided_at')->nullable();

            // ========== Metadata ==========
            $table->timestamps();

            // ========== Foreign Keys ==========
            $table->foreign('delivery_note_id')
                ->references('id')
                ->on('delivery_notes')
                ->onDelete('cascade');

            $table->foreign('purchase_invoice_id')
                ->references('id')
                ->on('purchase_invoices')
                ->onDelete('set null');

            $table->foreign('decided_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            // ========== Indexes ==========
            $table->index('delivery_note_id');
            $table->index('purchase_invoice_id');
            $table->index('action');
            $table->index('created_at');
            $table->index('decided_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliation_logs');
    }
};
