<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftHandover extends Model
{
    protected $table = 'shift_handovers';

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'stage_number',
        'shift_assignment_id',
        'handover_items',
        'auto_collected',
        'pending_items_count',
        'notes',
        'notes_en',
        'handover_time',
        'acknowledged_at',
        'acknowledged_by',
        'supervisor_approved',
        'approved_by',
    ];

    protected $casts = [
        'handover_items' => 'array',
        'auto_collected' => 'boolean',
        'handover_time' => 'datetime',
        'acknowledged_at' => 'datetime',
        'supervisor_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function shiftAssignment(): BelongsTo
    {
        return $this->belongsTo(ShiftAssignment::class);
    }

    /**
     * تحقق إذا تم استلام الوردية
     */
    public function isAcknowledged(): bool
    {
        return !is_null($this->acknowledged_at);
    }

    /**
     * تحقق إذا في أشغال معلقة
     */
    public function hasPendingItems(): bool
    {
        return $this->pending_items_count > 0;
    }

    /**
     * استلام الوردية
     */
    public function acknowledge(int $userId): bool
    {
        $this->acknowledged_at = now();
        $this->acknowledged_by = $userId;
        return $this->save();
    }
}
