<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stand extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'stand_number',
        'weight',
        'status',
        'notes',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'status_name',
        'status_badge',
    ];

    // Status constants
    const STATUS_UNUSED = 'unused';
    const STATUS_STAGE1 = 'stage1';
    const STATUS_STAGE2 = 'stage2';
    const STATUS_STAGE3 = 'stage3';
    const STATUS_STAGE4 = 'stage4';
    const STATUS_COMPLETED = 'completed';

    /**
     * Get status name in Arabic
     */
    public function getStatusNameAttribute()
    {
        return match ($this->status) {
            self::STATUS_UNUSED => 'غير مستخدم',
            self::STATUS_STAGE1 => 'المرحلة الأولى',
            self::STATUS_STAGE2 => 'المرحلة الثانية',
            self::STATUS_STAGE3 => 'المرحلة الثالثة',
            self::STATUS_STAGE4 => 'المرحلة الرابعة',
            self::STATUS_COMPLETED => 'مكتمل',
            default => 'غير محدد',
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            self::STATUS_UNUSED => 'um-badge-secondary',
            self::STATUS_STAGE1 => 'um-badge-info',
            self::STATUS_STAGE2 => 'um-badge-primary',
            self::STATUS_STAGE3 => 'um-badge-warning',
            self::STATUS_STAGE4 => 'um-badge-danger',
            self::STATUS_COMPLETED => 'um-badge-success',
            default => 'um-badge-secondary',
        };
    }

    /**
     * Scope: Active stands
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: By status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('stand_number', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%");
        });
    }
}
