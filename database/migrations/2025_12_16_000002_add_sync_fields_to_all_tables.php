<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة حقول المزامنة لجميع الجداول التشغيلية
     * 
     * الحقول المضافة:
     * - sync_status: حالة المزامنة (pending, synced, failed)
     * - is_synced: هل تمت المزامنة (boolean)
     * - synced_at: وقت المزامنة
     * - local_id: معرف محلي (UUID) للتمييز قبل المزامنة
     * - device_id: معرف الجهاز الذي أنشأ السجل
     */
    public function up(): void
    {
        // قائمة الجداول التي تحتاج حقول المزامنة
        $tables = [
            // مراحل الإنتاج
            'stage1_stands',
            'stage2_processed',
            'stage3_coils',
            'stage4_boxes',
            'box_coils',
            'stands',
            'stand_usage_history',
            'wrappings',
            
            // إدارة المواد والمستودعات
            'materials',
            'material_details',
            'material_batches',
            'material_movements',
            'delivery_notes',
            'delivery_note_items',
            'delivery_note_coils',
            'warehouse_transactions',
            'warehouse_intake_requests',
            'coil_transfers',
            
            // المشتريات والموردين
            'purchase_invoices',
            'purchase_invoice_items',
            'suppliers',
            'customers',
            
            // العمال والنوبات
            'users',
            'workers',
            'shift_assignments',
            // 'shift_handovers', // تم إضافته مسبقاً
            'worker_stage_history',
            'stage_suspensions',
            
            // التتبع والسجلات
            'barcodes',
            'product_tracking',
            'iron_journey_logs',
            'operation_logs',
            'reconciliation_logs',
            'registration_logs',
            'waste_tracking',
            'production_confirmations',
            'additives_inventory',
            'notifications',
            'generated_reports',
            'daily_statistics',
        ];

        foreach ($tables as $tableName) {
            // تحقق من وجود الجدول
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // تحديد العمود الذي سيتم الإضافة بعده
                $afterColumn = Schema::hasColumn($tableName, 'updated_at') ? 'updated_at' : 
                              (Schema::hasColumn($tableName, 'created_at') ? 'created_at' : null);
                
                // تحقق من عدم وجود الحقول مسبقاً
                if (!Schema::hasColumn($tableName, 'sync_status')) {
                    if ($afterColumn) {
                        $table->enum('sync_status', ['pending', 'synced', 'failed'])
                              ->default('synced')
                              ->after($afterColumn)
                              ->comment('حالة المزامنة');
                    } else {
                        $table->enum('sync_status', ['pending', 'synced', 'failed'])
                              ->default('synced')
                              ->comment('حالة المزامنة');
                    }
                }
                
                if (!Schema::hasColumn($tableName, 'is_synced')) {
                    $table->boolean('is_synced')
                          ->default(true)
                          ->after('sync_status')
                          ->comment('هل تمت المزامنة');
                }
                
                if (!Schema::hasColumn($tableName, 'synced_at')) {
                    $table->timestamp('synced_at')
                          ->nullable()
                          ->after('is_synced')
                          ->comment('وقت المزامنة');
                }
                
                if (!Schema::hasColumn($tableName, 'local_id')) {
                    $table->string('local_id', 100)
                          ->nullable()
                          ->after('synced_at')
                          ->unique()
                          ->comment('معرف محلي (UUID)');
                }
                
                if (!Schema::hasColumn($tableName, 'device_id')) {
                    $table->string('device_id', 100)
                          ->nullable()
                          ->after('local_id')
                          ->comment('معرف الجهاز');
                }
                
                // إضافة Indexes
                try {
                    $table->index('sync_status');
                } catch (\Exception $e) {
                    // Index already exists
                }
                
                try {
                    $table->index('is_synced');
                } catch (\Exception $e) {
                    // Index already exists
                }
                
                try {
                    $table->index('local_id');
                } catch (\Exception $e) {
                    // Index already exists
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'stage1_stands', 'stage2_processed', 'stage3_coils', 'stage4_boxes',
            'box_coils', 'stands', 'stand_usage_history', 'wrappings',
            'materials', 'material_details', 'material_batches', 'material_movements',
            'delivery_notes', 'delivery_note_items', 'delivery_note_coils',
            'warehouse_transactions', 'warehouse_intake_requests', 'coil_transfers',
            'purchase_invoices', 'purchase_invoice_items', 'suppliers', 'customers',
            'users', 'workers', 'shift_assignments', 'worker_stage_history', 'stage_suspensions',
            'barcodes', 'product_tracking', 'iron_journey_logs', 'operation_logs',
            'reconciliation_logs', 'registration_logs', 'waste_tracking',
            'production_confirmations', 'additives_inventory', 'notifications',
            'generated_reports', 'daily_statistics',
        ];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // حذف Indexes أولاً
                $columns = ['sync_status', 'is_synced', 'local_id'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        try {
                            $table->dropIndex(["{$tableName}_{$column}_index"]);
                        } catch (\Exception $e) {
                            // Index قد لا يكون موجود
                        }
                    }
                }
                
                // حذف الأعمدة
                $columnsToDelete = ['device_id', 'local_id', 'synced_at', 'is_synced', 'sync_status'];
                foreach ($columnsToDelete as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
