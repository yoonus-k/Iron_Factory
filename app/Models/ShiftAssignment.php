<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftAssignment extends Model
{
    protected $fillable = [
        'shift_code',
        'shift_type',
        'user_id',
        'supervisor_id',
        'team_id',
        'stage_number',
        'stage_record_barcode',
        'stage_record_id',
        'shift_date',
        'start_time',
        'end_time',
        'actual_end_time',
        'status',
        'notes',
        'suspension_reason',
        'is_active',
        'total_workers',
        'worker_ids',
        'completed_at',
        'suspended_at',
        'resumed_at',
    ];

    protected $casts = [
        'shift_date' => 'date',
        'worker_ids' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'completed_at' => 'datetime',
        'suspended_at' => 'datetime',
        'resumed_at' => 'datetime',
    ];

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_SUSPENDED = 'suspended';

    const TYPE_MORNING = 'morning';
    const TYPE_EVENING = 'evening';
    const TYPE_NIGHT = 'night';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(\App\Models\WorkerTeam::class, 'team_id');
    }

    public function workers()
    {
        // Ensure worker_ids is an array
        $workerIds = $this->worker_ids;

        // Check if it's empty or invalid
        if (!$workerIds || (is_array($workerIds) && empty($workerIds))) {
            return collect();
        }

        // Make sure it's an array
        if (!is_array($workerIds)) {
            $workerIds = json_decode($workerIds, true);
            if (!is_array($workerIds)) {
                return collect();
            }
        }

        // Filter out null/empty values
        $workerIds = array_filter($workerIds);

        if (empty($workerIds)) {
            return collect();
        }

        return \App\Models\Worker::whereIn('id', $workerIds)->get();
    }

    /**
     * Ensure worker_ids is always an array when retrieved
     */
    public function getWorkerIdsAttribute($value)
    {
        if (is_null($value) || $value === '' || $value === '0' || $value === 0) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Ensure worker_ids is always stored as JSON when set
     */
    public function setWorkerIdsAttribute($value)
    {
        if (is_null($value) || $value === '' || $value === '0' || $value === 0) {
            $this->attributes['worker_ids'] = json_encode([]);
        } elseif (is_array($value)) {
            // Filter out empty values and reset keys
            $filtered = array_filter($value);
            $this->attributes['worker_ids'] = json_encode(array_values($filtered));
        } else {
            $this->attributes['worker_ids'] = json_encode([]);
        }
    }

    public function getShiftTypeNameAttribute(): string
    {
        return match($this->shift_type) {
            self::TYPE_MORNING => 'الفترة الأولى (6 ص - 6 م)',
            self::TYPE_EVENING => 'الفترة الثانية (6 م - 6 ص)',
            default => $this->shift_type,
        };
    }

    /**
     * Get shift time range
     */
    public function getShiftTimeRangeAttribute(): string
    {
        return match($this->shift_type) {
            self::TYPE_MORNING => '06:00 - 18:00',
            self::TYPE_EVENING => '18:00 - 06:00',
            default => '',
        };
    }

    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            self::STATUS_SCHEDULED => 'مجدولة',
            self::STATUS_ACTIVE => 'نشطة',
            self::STATUS_COMPLETED => 'مكتملة',
            self::STATUS_CANCELLED => 'ملغية',
            self::STATUS_SUSPENDED => 'معلقة',
            default => $this->status,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('shift_type', $type);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('shift_date', $date);
    }

    public function scopeByStage($query, $stageNumber)
    {
        return $query->where('stage_number', $stageNumber);
    }
}
