<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftTransferHistory extends Model
{
    protected $table = 'shift_transfer_history';

    protected $fillable = [
        'shift_id',
        'from_supervisor_id',
        'to_supervisor_id',
        'old_data',
        'new_data',
        'transfer_notes',
        'transfer_type',
        'transferred_by',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_TEAM = 'team';
    const TYPE_MIXED = 'mixed';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * العلاقات
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(ShiftAssignment::class);
    }

    public function fromSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_supervisor_id');
    }

    public function toSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_supervisor_id');
    }

    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Helper Methods
     */
    public function getTransferTypeLabel(): string
    {
        return match($this->transfer_type) {
            self::TYPE_INDIVIDUAL => 'عمال أفراد',
            self::TYPE_TEAM => 'مجموعات عمال',
            self::TYPE_MIXED => 'مختلط (أفراد + مجموعات)',
            default => $this->transfer_type,
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_APPROVED => 'موافق عليه',
            self::STATUS_REJECTED => 'مرفوض',
            default => $this->status,
        };
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getOldWorkerCount(): int
    {
        $individual = count($this->old_data['individual_worker_ids'] ?? []);
        $team = count($this->old_data['team_worker_ids'] ?? []);
        return $individual + $team;
    }

    public function getNewWorkerCount(): int
    {
        $individual = count($this->new_data['individual_worker_ids'] ?? []);
        $team = count($this->new_data['team_worker_ids'] ?? []);
        return $individual + $team;
    }
}
