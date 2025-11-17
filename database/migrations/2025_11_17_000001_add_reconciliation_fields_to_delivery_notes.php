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
        Schema::table('delivery_notes', function (Blueprint $table) {
            // ========== معلومات التسجيل ==========
            // Check if registration_status column exists before adding
            if (!Schema::hasColumn('delivery_notes', 'registration_status')) {
                $table->enum('registration_status', [
                    'not_registered',
                    'registered',
                    'in_production',
                    'completed'
                ])->default('not_registered')->nullable();
            }

            if (!Schema::hasColumn('delivery_notes', 'registered_by')) {
                $table->unsignedBigInteger('registered_by')->nullable();
            }

            if (!Schema::hasColumn('delivery_notes', 'registered_at')) {
                $table->timestamp('registered_at')->nullable();
            }

            // ========== معلومات الفاتورة ==========
            if (!Schema::hasColumn('delivery_notes', 'purchase_invoice_id')) {
                $table->unsignedBigInteger('purchase_invoice_id')->nullable();
            }

            if (!Schema::hasColumn('delivery_notes', 'invoice_weight')) {
                $table->decimal('invoice_weight', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('delivery_notes', 'invoice_date')) {
                $table->date('invoice_date')->nullable();
            }

            // ========== حساب الفروقات (Generated) ==========
            if (!Schema::hasColumn('delivery_notes', 'weight_discrepancy')) {
                $table->decimal('weight_discrepancy', 10, 2)
                    ->virtualAs('actual_weight - COALESCE(invoice_weight, 0)')
                    ->comment('الفرق بالكيلو')
                    ->nullable();
            }

            if (!Schema::hasColumn('delivery_notes', 'discrepancy_percentage')) {
                $table->decimal('discrepancy_percentage', 5, 2)
                    ->virtualAs(
                        'CASE WHEN COALESCE(invoice_weight, 0) = 0 THEN 0
                        ELSE ((actual_weight - COALESCE(invoice_weight, 0)) / COALESCE(invoice_weight, 1) * 100) END'
                    )
                    ->comment('الفرق بالنسبة المئوية')
                    ->nullable();
            }

            // ========== معلومات التسوية ==========
            if (!Schema::hasColumn('delivery_notes', 'reconciliation_status')) {
                $table->enum('reconciliation_status', [
                    'pending',
                    'matched',
                    'discrepancy',
                    'adjusted',
                    'rejected'
                ])->default('pending');
            }

            if (!Schema::hasColumn('delivery_notes', 'reconciliation_notes')) {
                $table->text('reconciliation_notes')->nullable();
            }

            if (!Schema::hasColumn('delivery_notes', 'reconciled_by')) {
                $table->unsignedBigInteger('reconciled_by')->nullable();
            }

            if (!Schema::hasColumn('delivery_notes', 'reconciled_at')) {
                $table->timestamp('reconciled_at')->nullable();
            }

            // ========== معلومات إضافية ==========
            if (!Schema::hasColumn('delivery_notes', 'is_locked')) {
                $table->boolean('is_locked')->default(false);
            }

            if (!Schema::hasColumn('delivery_notes', 'lock_reason')) {
                $table->string('lock_reason')->nullable();
            }

            // ========== Foreign Keys ==========
            if (Schema::hasColumn('delivery_notes', 'purchase_invoice_id')) {
                try {
                    $table->foreign('purchase_invoice_id')
                        ->references('id')
                        ->on('purchase_invoices')
                        ->onDelete('set null');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }

            if (Schema::hasColumn('delivery_notes', 'registered_by')) {
                try {
                    $table->foreign('registered_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }

            if (Schema::hasColumn('delivery_notes', 'reconciled_by')) {
                try {
                    $table->foreign('reconciled_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }

            // ========== Indexes ==========
            if (Schema::hasColumn('delivery_notes', 'registration_status')) {
                try {
                    $table->index('registration_status');
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }

            if (Schema::hasColumn('delivery_notes', 'reconciliation_status')) {
                try {
                    $table->index('reconciliation_status');
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }

            if (Schema::hasColumn('delivery_notes', 'purchase_invoice_id')) {
                try {
                    $table->index('purchase_invoice_id');
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }

            if (Schema::hasColumn('delivery_notes', 'is_locked')) {
                try {
                    $table->index('is_locked');
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            // حذف الـ foreign keys بحذر
            $columns = Schema::getColumns('delivery_notes');
            $columnNames = array_column($columns, 'name');

            // حذف foreign keys فقط إذا كانت الأعمدة موجودة
            try {
                if (in_array('purchase_invoice_id', $columnNames)) {
                    $table->dropForeign(['purchase_invoice_id']);
                }
            } catch (\Exception $e) {
                // Foreign key might not exist
            }

            try {
                if (in_array('registered_by', $columnNames)) {
                    $table->dropForeign(['registered_by']);
                }
            } catch (\Exception $e) {
                // Foreign key might not exist
            }

            try {
                if (in_array('reconciled_by', $columnNames)) {
                    $table->dropForeign(['reconciled_by']);
                }
            } catch (\Exception $e) {
                // Foreign key might not exist
            }

            // حذف الأعمدة
            $columnsToDropIfExist = [
                'registration_status',
                'registered_by',
                'registered_at',
                'purchase_invoice_id',
                'invoice_weight',
                'invoice_date',
                'weight_discrepancy',
                'discrepancy_percentage',
                'reconciliation_status',
                'reconciliation_notes',
                'reconciled_by',
                'reconciled_at',
                'is_locked',
                'lock_reason'
            ];

            $columns = Schema::getColumns('delivery_notes');
            $existingColumns = array_column($columns, 'name');

            $columnsToActuallyDrop = array_intersect($columnsToDropIfExist, $existingColumns);

            if (!empty($columnsToActuallyDrop)) {
                $table->dropColumn($columnsToActuallyDrop);
            }
        });
    }
};
