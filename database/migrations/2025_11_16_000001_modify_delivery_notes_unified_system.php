<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration converts delivery_notes table to unified system
     * supporting both incoming and outgoing delivery notes.
     */
    public function up(): void
    {
        // Check if columns don't exist before adding them
        if (Schema::hasTable('delivery_notes')) {
            Schema::table('delivery_notes', function (Blueprint $table) {
                // Add type field if it doesn't exist
                if (!Schema::hasColumn('delivery_notes', 'type')) {
                    $table->enum('type', ['incoming', 'outgoing'])->default('incoming')->after('note_number')->comment('نوع الأذن: استقبال أو تسليم');
                }

                // Add weight fields if they don't exist
                if (!Schema::hasColumn('delivery_notes', 'actual_weight')) {
                    $table->decimal('actual_weight', 10, 3)->nullable()->after('delivered_weight')->comment('الوزن الفعلي المسجل من الميزان');
                }

                if (!Schema::hasColumn('delivery_notes', 'invoice_weight')) {
                    $table->decimal('invoice_weight', 10, 3)->nullable()->after('actual_weight')->comment('وزن الفاتورة من الموردين');
                }

                if (!Schema::hasColumn('delivery_notes', 'weight_discrepancy')) {
                    $table->decimal('weight_discrepancy', 10, 3)->nullable()->after('invoice_weight')->comment('الفرق بين الفعلي والفاتورة');
                }

                // Add supplier and destination fields if they don't exist
                if (!Schema::hasColumn('delivery_notes', 'supplier_id')) {
                    $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null')->after('material_id')->comment('المورد (للأذن الواردة)');
                }

                if (!Schema::hasColumn('delivery_notes', 'destination_id')) {
                    $table->foreignId('destination_id')->nullable()->constrained('warehouses')->onDelete('set null')->after('supplier_id')->comment('الوجهة/المستودع (للأذن الصادرة)');
                }

                // Add invoice details if they don't exist
                if (!Schema::hasColumn('delivery_notes', 'invoice_number')) {
                    $table->string('invoice_number', 100)->nullable()->after('weight_discrepancy')->comment('رقم الفاتورة');
                }

                if (!Schema::hasColumn('delivery_notes', 'invoice_reference_number')) {
                    $table->string('invoice_reference_number', 100)->nullable()->after('invoice_number')->comment('رقم مرجع الفاتورة من الموردين');
                }

                // Add recorded_by and approved_by if they don't exist
                if (!Schema::hasColumn('delivery_notes', 'recorded_by')) {
                    $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null')->after('received_by')->comment('المستخدم الذي سجل الأذن');
                }

                if (!Schema::hasColumn('delivery_notes', 'approved_by')) {
                    $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('recorded_by')->comment('المستخدم الذي وافق على الأذن');
                }

                if (!Schema::hasColumn('delivery_notes', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('updated_at')->comment('وقت الموافقة على الأذن');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasColumn('delivery_notes', 'supplier_id')) {
                $table->dropForeign(['supplier_id']);
            }
            if (Schema::hasColumn('delivery_notes', 'destination_id')) {
                $table->dropForeign(['destination_id']);
            }
            if (Schema::hasColumn('delivery_notes', 'recorded_by')) {
                $table->dropForeign(['recorded_by']);
            }
            if (Schema::hasColumn('delivery_notes', 'approved_by')) {
                $table->dropForeign(['approved_by']);
            }

            // Drop columns
            $columnsToDropIfExists = [
                'type',
                'actual_weight',
                'invoice_weight',
                'weight_discrepancy',
                'supplier_id',
                'destination_id',
                'invoice_number',
                'invoice_reference_number',
                'recorded_by',
                'approved_by',
                'approved_at'
            ];

            foreach ($columnsToDropIfExists as $column) {
                if (Schema::hasColumn('delivery_notes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
