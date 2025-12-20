<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncLog extends Model
{
    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'status',
        'error_message',
        'data_payload',
        'synced_at'
    ];

    protected $casts = [
        'data_payload' => 'json',
        'synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * علاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * حفظ عملية مزامنة جديدة
     */
    public static function logSync($userId, $entityType, $entityId, $data, $status = 'pending')
    {
        return self::create([
            'user_id' => $userId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'data_payload' => $data,
            'status' => $status,
        ]);
    }

    /**
     * تحديث الحالة إلى مزامن
     */
    public function markAsSynced()
    {
        $this->update([
            'status' => 'synced',
            'synced_at' => now(),
        ]);
    }

    /**
     * تحديث الحالة إلى فاشل
     */
    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Scope: العمليات المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
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
     * Scope: حسب نوع الكيان
     */
    public function scopeOfType($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }
}
