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
        // إضافة حقول جديدة لتحسين نظام المستودع
        Schema::table('delivery_notes', function (Blueprint $table) {
            // تمييز بين النوع (داخل/خارج)
            if (!Schema::hasColumn('delivery_notes', 'delivery_type_category')) {
                $table->enum('delivery_type_category', ['incoming', 'outgoing'])->default('incoming')->comment('نوع الأذن: داخلة أو طالعة');
            }

            // الكمية المدخلة للمستودع
            if (!Schema::hasColumn('delivery_notes', 'warehouse_quantity')) {
                $table->decimal('warehouse_quantity', 10, 2)->nullable()->comment('الكمية المسجلة في المستودع');
            }

            // تاريخ الدخول للمستودع
            if (!Schema::hasColumn('delivery_notes', 'warehouse_entry_date')) {
                $table->timestamp('warehouse_entry_date')->nullable()->comment('تاريخ دخول البضاعة للمستودع');
            }

            // الكمية المنقولة للإنتاج
            if (!Schema::hasColumn('delivery_notes', 'production_transfer_quantity')) {
                $table->decimal('production_transfer_quantity', 10, 2)->default(0)->comment('الكمية المنقولة للإنتاج');
            }

            // تاريخ النقل للإنتاج
            if (!Schema::hasColumn('delivery_notes', 'production_transfer_date')) {
                $table->timestamp('production_transfer_date')->nullable()->comment('تاريخ النقل للإنتاج');
            }

            // الكمية المتبقية في المستودع
            if (!Schema::hasColumn('delivery_notes', 'warehouse_remaining_quantity')) {
                $table->decimal('warehouse_remaining_quantity', 10, 2)->nullable()->comment('الكمية المتبقية في المستودع');
            }

            // حالة المستودع (نشطة أو منتهية)
            if (!Schema::hasColumn('delivery_notes', 'warehouse_status')) {
                $table->enum('warehouse_status', ['pending', 'in_warehouse', 'partially_transferred', 'fully_transferred'])->default('pending')->comment('حالة البضاعة في المستودع');
            }

            // من نقل للإنتاج
            if (!Schema::hasColumn('delivery_notes', 'transferred_by')) {
                $table->foreignId('transferred_by')->nullable()->constrained('users')->comment('معرف المستخدم الذي نقل البضاعة');
            }

            // ملاحظات النقل للإنتاج
            if (!Schema::hasColumn('delivery_notes', 'transfer_notes')) {
                $table->text('transfer_notes')->nullable()->comment('ملاحظات عند نقل البضاعة للإنتاج');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $columns = [
                'delivery_type_category',
                'warehouse_quantity',
                'warehouse_entry_date',
                'production_transfer_quantity',
                'production_transfer_date',
                'warehouse_remaining_quantity',
                'warehouse_status',
                'transferred_by',
                'transfer_notes'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('delivery_notes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
