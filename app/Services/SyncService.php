<?php

namespace App\Services;

use App\Models\SyncLog;
use App\Models\SyncHistory;
use App\Models\PendingSync;
use App\Models\UserLastSync;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Exception;

class SyncService
{
    /**
     * رفع البيانات من الجهاز المحلي للسيرفر (Push)
     */
    public function pushToServer($userId, array $data)
    {
        DB::beginTransaction();
        
        try {
            $syncedItems = [];
            $failedItems = [];

            foreach ($data as $item) {
                try {
                    $result = $this->syncSingleItem($userId, $item);
                    
                    if ($result['success']) {
                        $syncedItems[] = $result;
                    } else {
                        $failedItems[] = $result;
                    }
                } catch (Exception $e) {
                    $failedItems[] = [
                        'entity_type' => $item['entity_type'] ?? 'unknown',
                        'local_id' => $item['local_id'] ?? null,
                        'error' => $e->getMessage(),
                        'success' => false,
                    ];
                }
            }

            // تحديث آخر وقت رفع للمستخدم
            $userLastSync = UserLastSync::getOrCreateForUser($userId);
            $userLastSync->updateLastPush();

            DB::commit();

            return [
                'success' => true,
                'synced_count' => count($syncedItems),
                'failed_count' => count($failedItems),
                'synced_items' => $syncedItems,
                'failed_items' => $failedItems,
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Push to server failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * مزامنة عنصر واحد
     */
    protected function syncSingleItem($userId, array $item)
    {
        $entityType = $item['entity_type'];
        $action = $item['action'] ?? 'create';
        $data = $item['data'];
        $localId = $item['local_id'] ?? null;

        // الحصول على Model
        $modelClass = $this->getModelClass($entityType);
        
        if (!$modelClass) {
            throw new Exception("Unknown entity type: {$entityType}");
        }

        // تنفيذ العملية حسب النوع
        switch ($action) {
            case 'create':
                // للـ wrappings: تحقق من wrapping_number لتجنب Duplicate
                if ($entityType === 'wrappings' && isset($data['wrapping_number'])) {
                    $existing = $modelClass::where('wrapping_number', $data['wrapping_number'])->first();
                    if ($existing) {
                        // إذا موجود، عدّل بدلاً من إضافة
                        $existing->update($data);
                        $entity = $existing;
                    } else {
                        $entity = $modelClass::create($data);
                    }
                } else {
                    $entity = $modelClass::create($data);
                }
                break;
                
            case 'update':
                $entityId = $item['entity_id'] ?? null;
                if (!$entityId) {
                    throw new Exception("Entity ID required for update");
                }
                $entity = $modelClass::findOrFail($entityId);
                $entity->update($data);
                break;
                
            case 'delete':
                $entityId = $item['entity_id'] ?? null;
                if (!$entityId) {
                    throw new Exception("Entity ID required for delete");
                }
                $entity = $modelClass::findOrFail($entityId);
                $entity->delete();
                break;
                
            default:
                throw new Exception("Unknown action: {$action}");
        }

        // تحديث حالة المزامنة
        if (isset($entity)) {
            $entity->update([
                'is_synced' => true,
                'sync_status' => 'synced',
                'synced_at' => now(),
            ]);
        }

        // حفظ في سجل المزامنة
        SyncHistory::recordSync(
            $userId,
            $entityType,
            $entity->id ?? null,
            $data,
            $action
        );

        // حفظ في SyncLog
        SyncLog::logSync(
            $userId,
            $entityType,
            $entity->id ?? null,
            $data,
            'synced'
        );

        return [
            'success' => true,
            'entity_type' => $entityType,
            'entity_id' => $entity->id ?? null,
            'local_id' => $localId,
            'action' => $action,
        ];
    }

    /**
     * سحب البيانات من السيرفر للجهاز المحلي (Pull)
     */
    public function pullFromServer($userId, $lastSyncTime = null)
    {
        try {
            $userLastSync = UserLastSync::getOrCreateForUser($userId);
            
            // إذا لم يتم تحديد وقت آخر مزامنة، استخدم آخر وقت سحب
            if (!$lastSyncTime && $userLastSync->last_pull_at) {
                $lastSyncTime = $userLastSync->last_pull_at;
            }

            // الحصول على كل التحديثات منذ آخر مزامنة
            $updates = [];

            // قائمة الجداول التي يجب سحبها
            $tables = $this->getSyncableTables();

            foreach ($tables as $table => $modelClass) {
                try {
                    // فحص ما إذا كان الجدول يحتوي على عمود is_synced
                    $hasIsSynced = Schema::hasColumn($table, 'is_synced');
                    $hasUpdatedAt = Schema::hasColumn($table, 'updated_at');

                    $query = $modelClass::query();

                    // إذا كان العمود موجوداً، فلتر به
                    if ($hasIsSynced) {
                        $query->where('is_synced', true);
                    }

                    // فلتر حسب تاريخ التحديث إذا متوفر
                    if ($lastSyncTime && $hasUpdatedAt) {
                        $query->where('updated_at', '>', $lastSyncTime);
                    }

                    $items = $query->get();

                    if ($items->isNotEmpty()) {
                        $updates[$table] = $items->toArray();
                    }
                } catch (Exception $tableException) {
                    Log::warning("Failed to sync table: $table", [
                        'error' => $tableException->getMessage(),
                    ]);
                    continue;
                }
            }

            // تحديث آخر وقت سحب
            $userLastSync->updateLastPull();

            return [
                'success' => true,
                'last_sync_time' => now()->toIso8601String(),
                'updates' => $updates,
                'total_items' => array_sum(array_map('count', $updates)),
            ];

        } catch (Exception $e) {
            Log::error('Pull from server failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * معالجة العمليات المعلقة
     */
    public function processPendingSyncs($userId = null, $limit = 50)
    {
        $query = PendingSync::pending()->byPriority();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $pendingSyncs = $query->limit($limit)->get();

        if ($pendingSyncs->isEmpty()) {
            return [
                'processed' => 0,
                'failed' => 0,
                'total' => 0,
            ];
        }

        // إذا كان هذا السيرفر هو الجهاز المحلي (IS_CENTRAL_SERVER=false)
        // فالمطلوب إرسال العمليات للسيرفر الأونلاين عبر CentralServerService
        if (!config('sync.is_central_server')) {
            return $this->pushPendingToCentral($pendingSyncs);
        }

        $processed = 0;
        $failed = 0;

        foreach ($pendingSyncs as $sync) {
            try {
                $sync->markAsProcessing();

                $result = $this->syncSingleItem($sync->user_id, [
                    'entity_type' => $sync->entity_type,
                    'action' => $sync->action,
                    'data' => $sync->data,
                    'local_id' => $sync->local_id,
                ]);

                if ($result['success']) {
                    $sync->markAsSynced($result['entity_id'] ?? null);
                    $processed++;
                } else {
                    $sync->markAsFailed($result['error'] ?? 'Unknown error');
                    $failed++;
                }

            } catch (Exception $e) {
                $sync->markAsFailed($e->getMessage());
                $failed++;
                
                Log::error('Failed to process pending sync', [
                    'sync_id' => $sync->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'processed' => $processed,
            'failed' => $failed,
            'total' => $pendingSyncs->count(),
        ];
    }

    /**
     * إرسال العمليات المعلقة للسيرفر المركزي عند العمل كسيرفر محلي
     */
    protected function pushPendingToCentral($pendingSyncs)
    {
        $centralService = app(CentralServerService::class);

        // تجهيز البيانات للإرسال
        $payload = $pendingSyncs->map(function ($sync) {
            return [
                'entity_type' => $this->mapEntityTypeForRemote($sync->entity_type),
                'action' => $sync->action,
                'data' => $sync->data ?? [],
                'local_id' => $sync->local_id,
                'entity_id' => $sync->entity_id,
            ];
        })->values()->all();

        if (empty($payload)) {
            return [
                'processed' => 0,
                'failed' => 0,
                'total' => 0,
            ];
        }

        // وضع العمليات في حالة processing لحين اكتمال الطلب
        foreach ($pendingSyncs as $sync) {
            $sync->markAsProcessing();
        }

        try {
            $response = $centralService->push($payload);

            $syncedItems = collect($response['synced_items'] ?? []);
            $failedItems = collect($response['failed_items'] ?? []);

            // تحديث حالة السجلات الناجحة
            foreach ($syncedItems as $item) {
                $pending = $pendingSyncs->firstWhere('local_id', $item['local_id'] ?? null);
                if ($pending) {
                    $pending->markAsSynced($item['entity_id'] ?? null);
                }
            }

            // تحديث حالة السجلات الفاشلة (إن وجد)
            foreach ($failedItems as $item) {
                $pending = $pendingSyncs->firstWhere('local_id', $item['local_id'] ?? null);
                if ($pending) {
                    $pending->markAsFailed($item['error'] ?? 'Remote sync failed');
                }
            }

            return [
                'processed' => $syncedItems->count(),
                'failed' => $failedItems->count(),
                'total' => $pendingSyncs->count(),
            ];

        } catch (Exception $e) {
            foreach ($pendingSyncs as $sync) {
                $sync->markAsFailed($e->getMessage());
            }

            throw $e;
        }
    }

    /**
     * تحويل اسم الجدول إلى الصيغة التي يفهمها السيرفر المركزي
     */
    protected function mapEntityTypeForRemote($entityType)
    {
        if ($entityType === 'wrappings') {
            return $entityType;
        }

        return Str::singular($entityType);
    }

    /**
     * إضافة عملية للانتظار (عند عدم وجود إنترنت)
     */
    public function addToPendingQueue($userId, $entityType, $action, $data, $priority = 0)
    {
        return PendingSync::addPending($userId, $entityType, $action, $data, $priority);
    }

    /**
     * الحصول على إحصائيات المزامنة للمستخدم
     */
    public function getSyncStats($userId)
    {
        $userLastSync = UserLastSync::getOrCreateForUser($userId);
        
        $pending = PendingSync::where('user_id', $userId)->pending()->count();
        $failed = PendingSync::where('user_id', $userId)->failed()->count();
        
        $userLastSync->updateStats($pending, $failed);

        return [
            'last_pull_at' => $userLastSync->last_pull_at?->toIso8601String(),
            'last_push_at' => $userLastSync->last_push_at?->toIso8601String(),
            'pending_count' => $pending,
            'failed_count' => $failed,
            'total_synced' => SyncLog::where('user_id', $userId)->synced()->count(),
        ];
    }

    /**
     * الحصول على Model class من entity type
     */
    protected function getModelClass($entityType)
    {
        $mapping = $this->getSyncableTables();

        if (isset($mapping[$entityType])) {
            return $mapping[$entityType];
        }

        $singularKey = Str::singular($entityType);
        if (isset($mapping[$singularKey])) {
            return $mapping[$singularKey];
        }

        foreach ($mapping as $table => $modelClass) {
            if (Str::singular($table) === $entityType) {
                return $modelClass;
            }
        }

        return null;
    }

    /**
     * الحصول على قائمة الجداول القابلة للمزامنة
     */
    protected function getSyncableTables()
    {
        return [
            // المواد والمخزون
            'materials' => \App\Models\Material::class,
            'material_details' => \App\Models\MaterialDetail::class,
            'material_movements' => \App\Models\MaterialMovement::class,
            'material_types' => \App\Models\MaterialType::class,
            'units' => \App\Models\Unit::class,
            
            // المستودعات
            'warehouses' => \App\Models\Warehouse::class,
            'warehouse_transactions' => \App\Models\WarehouseTransaction::class,
            'warehouse_intake_requests' => \App\Models\WarehouseIntakeRequest::class,
            'warehouse_intake_items' => \App\Models\WarehouseIntakeItem::class,
            
            // أذون التسليم والفواتير
            'delivery_notes' => \App\Models\DeliveryNote::class,
            'delivery_note_items' => \App\Models\DeliveryNoteItem::class,
            'delivery_note_coils' => \App\Models\DeliveryNoteCoil::class,
            'purchase_invoices' => \App\Models\PurchaseInvoice::class,
            'purchase_invoice_items' => \App\Models\PurchaseInvoiceItem::class,
            
            // مراحل الإنتاج
            'stage1_stands' => \App\Models\Stage1Stand::class,
            'stage2_processed' => \App\Models\Stage2Processed::class,
            'stage3_coils' => \App\Models\Stage3Coil::class,
            'stage4_boxes' => \App\Models\Stage4Box::class,
            'box_coils' => \App\Models\BoxCoil::class,
            'production_stages' => \App\Models\ProductionStage::class,
            'production_confirmations' => \App\Models\ProductionConfirmation::class,
            'stage_suspensions' => \App\Models\StageSuspension::class,
            
            // الاستاندات
            'stands' => \App\Models\Stand::class,
            'stand_usage_history' => \App\Models\StandUsageHistory::class,
            
            // نقل الكويلات والتتبع
            'coil_transfers' => \App\Models\CoilTransfer::class,
            'product_tracking' => \App\Models\ProductTracking::class,
            
            // الهدر
            'waste_limits' => \App\Models\WasteLimit::class,
            'waste_tracking' => \App\Models\WasteTracking::class,
            
            // التغليف
            'wrappings' => \App\Models\Wrapping::class,
            
            // المستخدمين والعمال
            'users' => \App\Models\User::class,
            'user_permissions' => \App\Models\UserPermission::class,
            'workers' => \App\Models\Worker::class,
            'worker_stage_history' => \App\Models\WorkerStageHistory::class,
            'worker_teams' => \App\Models\WorkerTeam::class,
            
            // الصلاحيات والأدوار
            'roles' => \App\Models\Role::class,
            'permissions' => \App\Models\Permission::class,
            
            // الورديات
            'shift_assignments' => \App\Models\ShiftAssignment::class,
            'shift_handovers' => \App\Models\ShiftHandover::class,
            'shift_operation_logs' => \App\Models\ShiftOperationLog::class,
            'shift_transfer_history' => \App\Models\ShiftTransferHistory::class,
            
            // الموردين والعملاء
            'suppliers' => \App\Models\Supplier::class,
            'customers' => \App\Models\Customer::class,
            
            // الباركود
            'barcodes' => \App\Models\Barcode::class,
            'barcode_settings' => \App\Models\BarcodeSetting::class,
            
            // الإعدادات والنظام
            'system_settings' => \App\Models\SystemSetting::class,
            'system_formulas' => \App\Models\SystemFormula::class,
            'translations' => \App\Models\Translation::class,
            
            // الإشعارات والسجلات
            'notifications' => \App\Models\Notification::class,
            'operation_logs' => \App\Models\OperationLog::class,
            'registration_logs' => \App\Models\RegistrationLog::class,
            'reconciliation_logs' => \App\Models\ReconciliationLog::class,
            
            // الإحصائيات والتقارير
            'daily_statistics' => \App\Models\DailyStatistics::class,
            'generated_reports' => \App\Models\GeneratedReport::class,
            
            // المخزون الإضافي
            'additives_inventory' => \App\Models\AdditivesInventory::class,
            
            // المزامنة (لا تُزامن ذاتياً لكن يمكن تتبعها)
            'sync_logs' => \App\Models\SyncLog::class,
            'sync_histories' => \App\Models\SyncHistory::class,
            'pending_syncs' => \App\Models\PendingSync::class,
            'user_last_sync' => \App\Models\UserLastSync::class,
            'user_last_syncs' => \App\Models\UserLastSync::class, // اسم بديل
            
            // الجداول الإضافية
            'material_batches' => \App\Models\MaterialBatch::class,
            'iron_journey_logs' => \App\Models\IronJourneyLog::class,
            'role_permissions' => \App\Models\RolePermission::class,
            'worker_permissions' => \App\Models\WorkerPermission::class,
            
            // ملاحظة: الجداول التالية هي جداول Laravel الداخلية ولا تحتاج للمزامنة:
            // cache, cache_locks, failed_jobs, job_batches, jobs, migrations, 
            // password_reset_tokens, personal_access_tokens, sessions
        ];
    }
}
