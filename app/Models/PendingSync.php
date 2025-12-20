<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PendingSync extends Model
{
    protected $fillable = [
        'user_id',
        'entity_type',
        'local_id',
        'entity_id',
        'action',
        'data',
        'related_data',
        'priority',
        'status',
        'retry_count',
        'max_retries',
        'last_error',
        'created_at_local',
        'last_attempt_at',
        'synced_at',
    ];

    protected $casts = [
        'data' => 'json',
        'related_data' => 'json',
        'created_at_local' => 'datetime',
        'last_attempt_at' => 'datetime',
        'synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'priority' => 'integer',
        'retry_count' => 'integer',
        'max_retries' => 'integer',
    ];

    /**
     * علاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // توليد local_id تلقائياً إذا لم يكن موجود
        static::creating(function ($model) {
            if (empty($model->local_id)) {
                $model->local_id = (string) Str::uuid();
            }
        });
    }

    /**
     * إضافة عملية معلقة
     */
    public static function addPending($userId, $entityType, $action, $data, $priority = 0, $relatedData = null)
    {
        return self::create([
            'user_id' => $userId,
            'entity_type' => $entityType,
            'action' => $action,
            'data' => $data,
            'related_data' => $relatedData,
            'priority' => $priority,
            'status' => 'pending',
            'created_at_local' => now(),
        ]);
    }

    /**
     * تحديد أن العملية قيد المعالجة
     */
    public function markAsProcessing()
    {
        $this->update([
            'status' => 'processing',
            'last_attempt_at' => now(),
        ]);
    }

    /**
     * تحديد أن العملية تمت بنجاح
     */
    public function markAsSynced($entityId = null)
    {
        $this->update([
            'status' => 'synced',
            'entity_id' => $entityId,
            'synced_at' => now(),
        ]);
    }

    /**
     * تحديد أن العملية فشلت
     */
    public function markAsFailed($error)
    {
        $this->increment('retry_count');
        
        $status = $this->retry_count >= $this->max_retries ? 'failed' : 'pending';
        
        $this->update([
            'status' => $status,
            'last_error' => $error,
            'last_attempt_at' => now(),
        ]);
    }

    /**
     * إعادة محاولة العملية
     */
    public function retry()
    {
        if ($this->retry_count < $this->max_retries) {
            $this->update([
                'status' => 'pending',
                'last_error' => null,
            ]);
        }
    }

    /**
     * Scope: العمليات المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
                     ->whereColumn('retry_count', '<', 'max_retries');
    }

    /**
     * Scope: العمليات قيد المعالجة
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope: العمليات المزامنة
     */
    public function scopeSynced($query)
    {
        return $query->where('status', 'synced');
    }

    /**
     * Scope: العمليات الفاشلة
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: ترتيب حسب الأولوية
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc')
                     ->orderBy('created_at', 'asc');
    }

    /**
     * Scope: حسب نوع الكيان
     */
    public function scopeOfType($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope: حسب نوع العملية
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }
}
