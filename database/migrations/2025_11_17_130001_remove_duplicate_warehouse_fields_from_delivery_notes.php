<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * حذف جميع الحقول المكررة من جدول delivery_notes
     * هذه الحقول موجودة بالفعل في جدول material_details
     */
    public function up(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            // حذف الـ foreign key أولاً قبل حذف الفيلد
            try {
                $table->dropForeign('delivery_notes_transferred_by_foreign');
            } catch (\Exception $e) {
                // الـ foreign key قد يكون غير موجود
            }

            $columns = [
                'delivery_type_category',           // لا يوجد بديل حالياً لكن يمكن استخدام type
                'warehouse_quantity',               // موجود في material_details.quantity
                'warehouse_entry_date',             // موجود في material_details
                'production_transfer_quantity',     // موجود في material_details
                'production_transfer_date',         // موجود في material_details
                'warehouse_remaining_quantity',     // موجود في material_details.remaining_weight
                'warehouse_status',                 // موجود في material_details
                'transferred_by',                   // يمكن تتبعه من خلال سجلات النقل
                'transfer_notes'                    // يمكن تتبعه من خلال سجلات النقل
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('delivery_notes', $column)) {
                    $table->dropColumn($column);
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
            $table->enum('delivery_type_category', ['incoming', 'outgoing'])
                ->default('incoming')
                ->comment('نوع الأذن: داخلة أو طالعة');

            $table->decimal('warehouse_quantity', 10, 2)
                ->nullable()
                ->comment('الكمية المسجلة في المستودع');

            $table->timestamp('warehouse_entry_date')
                ->nullable()
                ->comment('تاريخ دخول البضاعة للمستودع');

            $table->decimal('production_transfer_quantity', 10, 2)
                ->default(0)
                ->comment('الكمية المنقولة للإنتاج');

            $table->timestamp('production_transfer_date')
                ->nullable()
                ->comment('تاريخ النقل للإنتاج');

            $table->decimal('warehouse_remaining_quantity', 10, 2)
                ->nullable()
                ->comment('الكمية المتبقية في المستودع');

            $table->enum('warehouse_status', ['pending', 'in_warehouse', 'partially_transferred', 'fully_transferred'])
                ->default('pending')
                ->comment('حالة البضاعة في المستودع');

            $table->foreignId('transferred_by')
                ->nullable()
                ->constrained('users')
                ->comment('معرف المستخدم الذي نقل البضاعة');

            $table->text('transfer_notes')
                ->nullable()
                ->comment('ملاحظات عند نقل البضاعة للإنتاج');
        });
    }
};
