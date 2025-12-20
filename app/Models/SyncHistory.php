<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncHistory extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'entity_type',
        'entity_id',
        'data',
        'action',
        'synced_from_local',
        'synced_to_server',
        'pulled_by_manager_at',
        'device_id',
        'ip_address',
    ];

    protected $casts = [
        'data' => 'json',
        'synced_from_local' => 'datetime',
        'synced_to_server' => 'datetime',
        'pulled_by_manager_at' => 'datetime',
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
     * حفظ سجل مزامنة جديد
     */
    public static function recordSync($userId, $entityType, $entityId, $data, $action, $userType = 'staff')
    {
        return self::create([
            'user_id' => $userId,
            'user_type' => $userType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'data' => $data,
            'action' => $action,
            'synced_from_local' => now(),
            'synced_to_server' => now(),
            'device_id' => request()->header('X-Device-ID'),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * تحديد أن المدير سحب البيانات
     */
    public function markAsPulledByManager()
    {
        $this->update([
            'pulled_by_manager_at' => now(),
        ]);
    }

    /**
     * Scope: حسب نوع المستخدم
     */
    public function scopeByUserType($query, $userType)
    {
        return $query->where('user_type', $userType);
    }

    /**
     * Scope: حسب نوع العملية
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: حسب نوع الكيان
     */
    public function scopeOfType($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope: التي لم يسحبها المدير بعد
     */
    public function scopeNotPulled($query)
    {
        return $query->whereNull('pulled_by_manager_at');
    }

    /**
     * Scope: التي سحبها المدير
     */
    public function scopePulled($query)
    {
        return $query->whereNotNull('pulled_by_manager_at');
    }

    /**
     * Scope: منذ تاريخ معين
     */
    public function scopeSince($query, $date)
    {
        return $query->where('synced_to_server', '>=', $date);
    }
}
