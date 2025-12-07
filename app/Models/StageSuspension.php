<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageSuspension extends Model
{
    protected $fillable = [
        'stage_number',
        'batch_barcode',
        'batch_id',
        'input_weight',
        'output_weight',
        'waste_weight',
        'waste_percentage',
        'allowed_percentage',
        'status',
        'suspension_reason',
        'suspended_by',
        'suspended_at',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'additional_data',
    ];

    protected $casts = [
        'input_weight' => 'decimal:2',
        'output_weight' => 'decimal:2',
        'waste_weight' => 'decimal:2',
        'waste_percentage' => 'decimal:2',
        'allowed_percentage' => 'decimal:2',
        'suspended_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'additional_data' => 'array',
    ];

    public function suspendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getStageName(): string
    {
        $names = [
            1 => 'المرحلة الأولى - السحب',
            2 => 'المرحلة الثانية - الجلفنة',
            3 => 'المرحلة الثالثة - اللف',
            4 => 'المرحلة الرابعة - التعبئة',
        ];

        return $names[$this->stage_number] ?? "المرحلة {$this->stage_number}";
    }

    public function getStatusBadge(): string
    {
        return match($this->status) {
            'suspended' => '<span class="badge bg-danger">موقوفة</span>',
            'approved' => '<span class="badge bg-success">تمت الموافقة</span>',
            'rejected' => '<span class="badge bg-secondary">مرفوضة</span>',
            default => '<span class="badge bg-warning">غير معروف</span>',
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeForStage($query, int $stage)
    {
        return $query->where('stage_number', $stage);
    }
}
