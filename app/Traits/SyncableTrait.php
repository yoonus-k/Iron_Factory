<?php

namespace App\Traits;

use App\Models\PendingSync;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Trait للمزامنة التلقائية
 * يضاف لأي Model تريد مزامنته تلقائياً
 */
trait SyncableTrait
{
    /**
     * Boot the trait
     */
    public static function bootSyncableTrait()
    {
        // عند الإنشاء
        static::created(function ($model) {
            $model->addToSyncQueue('create');
        });

        // عند التحديث
        static::updated(function ($model) {
            $model->addToSyncQueue('update');
        });

        // عند الحذف
        static::deleted(function ($model) {
            $model->addToSyncQueue('delete');
        });
    }

    /**
     * إضافة العملية لقائمة المزامنة
     */
    protected function addToSyncQueue(string $action): void
    {
        try {
            // تجاهل إذا كانت المزامنة معطلة
            if (config('sync.enabled', true) === false) {
                return;
            }

            $userId = Auth::id() ?? 1; // استخدم 1 كـ default إذا لم يكن هناك مستخدم
            $tableName = $this->getTable();

            // إنشاء سجل في pending_syncs
            PendingSync::create([
                'user_id' => $userId,
                'entity_type' => $tableName,
                'entity_id' => $this->getKey(),
                'action' => $action,
                'data' => $action === 'delete' ? ['id' => $this->getKey()] : $this->toArray(),
                'priority' => $this->getSyncPriority(),
                'status' => 'pending',
                'attempts' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info("تمت إضافة {$tableName} #{$this->getKey()} للمزامنة - العملية: {$action}");

        } catch (\Exception $e) {
            Log::error("فشل إضافة العنصر للمزامنة: " . $e->getMessage(), [
                'table' => $this->getTable(),
                'id' => $this->getKey(),
                'action' => $action,
            ]);
        }
    }

    /**
     * الحصول على أولوية المزامنة (يمكن تخصيصها في كل Model)
     */
    protected function getSyncPriority(): int
    {
        return $this->syncPriority ?? 0;
    }

    /**
     * تعطيل المزامنة مؤقتاً لهذا الـ Model
     */
    public function withoutSyncing(callable $callback)
    {
        $originalValue = config('sync.enabled');
        config(['sync.enabled' => false]);

        try {
            return $callback();
        } finally {
            config(['sync.enabled' => $originalValue]);
        }
    }
}
