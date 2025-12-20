<?php

namespace App\Traits;

use App\Models\PendingSync;
use Illuminate\Support\Str;

/**
 * Trait لتفعيل المزامنة التلقائية للموديلات
 * 
 * الاستخدام:
 * class Material extends Model {
 *     use Syncable;
 * }
 */
trait Syncable
{
    /**
     * Boot the trait
     */
    public static function bootSyncable()
    {
        // عند الإنشاء
        static::creating(function ($model) {
            // إذا لم يكن هناك local_id، قم بإنشاء واحد
            if (empty($model->local_id)) {
                $model->local_id = (string) Str::uuid();
            }

            // ضبط حقول المزامنة الافتراضية
            $model->is_synced = $model->is_synced ?? false;
            $model->sync_status = $model->sync_status ?? 'pending';
            $model->device_id = $model->device_id ?? request()->header('X-Device-ID');
        });

        // بعد الإنشاء
        static::created(function ($model) {
            if ($model->shouldSync()) {
                $model->queueForSync('create');
            }
        });

        // بعد التحديث
        static::updated(function ($model) {
            if ($model->shouldSync()) {
                $model->queueForSync('update');
            }
        });

        // بعد الحذف
        static::deleted(function ($model) {
            if ($model->shouldSync()) {
                $model->queueForSync('delete');
            }
        });
    }

    /**
     * هل يجب مزامنة هذا السجل؟
     */
    public function shouldSync()
    {
        // لا تزامن إذا كان السجل مزامن بالفعل
        if ($this->is_synced === true) {
            return false;
        }

        // لا تزامن إذا كان في وضع الاختبار (يمكن تخصيصه)
        if (app()->environment('testing')) {
            return false;
        }

        return true;
    }

    /**
     * إضافة العملية لقائمة الانتظار
     */
    public function queueForSync($action = 'create', $priority = 0)
    {
        // الحصول على البيانات
        $data = $this->toArray();

        // إزالة الحقول غير الضرورية
        unset($data['is_synced'], $data['sync_status'], $data['synced_at']);

        // الحصول على user_id
        $userId = auth()->id() ?? $this->created_by ?? null;

        if (!$userId) {
            return null;
        }

        // إنشاء pending sync
        return PendingSync::addPending(
            $userId,
            $this->getSyncEntityType(),
            $action,
            $data,
            $priority,
            $this->getRelatedSyncData()
        );
    }

    /**
     * تحديد حالة المزامنة
     */
    public function markAsSynced()
    {
        $this->update([
            'is_synced' => true,
            'sync_status' => 'synced',
            'synced_at' => now(),
        ]);
    }

    /**
     * تحديد أن المزامنة فشلت
     */
    public function markSyncFailed()
    {
        $this->update([
            'is_synced' => false,
            'sync_status' => 'failed',
        ]);
    }

    /**
     * تحديد أن المزامنة معلقة
     */
    public function markSyncPending()
    {
        $this->update([
            'is_synced' => false,
            'sync_status' => 'pending',
        ]);
    }

    /**
     * الحصول على نوع الكيان للمزامنة
     */
    public function getSyncEntityType()
    {
        // استخدم اسم الجدول كنوع الكيان
        return $this->getTable();
    }

    /**
     * الحصول على البيانات المرتبطة (للعلاقات)
     * يمكن تخصيصها في كل موديل
     */
    public function getRelatedSyncData()
    {
        return null;
    }

    /**
     * Scope: السجلات المعلقة للمزامنة
     */
    public function scopePendingSync($query)
    {
        return $query->where('is_synced', false)
                     ->where('sync_status', 'pending');
    }

    /**
     * Scope: السجلات المزامنة
     */
    public function scopeSynced($query)
    {
        return $query->where('is_synced', true)
                     ->where('sync_status', 'synced');
    }

    /**
     * Scope: السجلات الفاشلة
     */
    public function scopeFailedSync($query)
    {
        return $query->where('sync_status', 'failed');
    }

    /**
     * Scope: حسب معرف الجهاز
     */
    public function scopeFromDevice($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    /**
     * Scope: حسب المعرف المحلي
     */
    public function scopeByLocalId($query, $localId)
    {
        return $query->where('local_id', $localId);
    }
}
