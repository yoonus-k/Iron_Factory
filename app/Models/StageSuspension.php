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

    /**
     * جلب باركود الإنتاج الفعلي من الجدول المناسب حسب المرحلة
     */
    public function getProductionBarcodeAttribute(): ?string
    {
        // إذا كان محفوظاً في additional_data، استخدمه
        if (!empty($this->additional_data['production_barcode'])) {
            return $this->additional_data['production_barcode'];
        }

        // إذا لم يكن محفوظاً، ابحث في الجداول حسب المرحلة
        return $this->fetchProductionBarcode();
    }

    /**
     * البحث عن باركود الإنتاج في الجداول المناسبة
     */
    private function fetchProductionBarcode(): ?string
    {
        try {
            $tableName = match($this->stage_number) {
                1 => 'stage1_stands',
                2 => 'stage2_processed',
                3 => 'stage3_coils',
                4 => 'stage4_boxes',
                default => null
            };

            if (!$tableName) {
                return null;
            }

            // البحث في product_tracking أولاً (الأسرع)
            $tracking = \DB::table('product_tracking')
                ->where('input_barcode', $this->batch_barcode)
                ->where('stage', 'stage' . $this->stage_number)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($tracking && $tracking->output_barcode) {
                return $tracking->output_barcode;
            }

            // البحث في الجدول المناسب للمرحلة
            $record = \DB::table($tableName)
                ->where('input_barcode', $this->batch_barcode)
                ->orWhere('parent_barcode', $this->batch_barcode)
                ->orderBy('created_at', 'desc')
                ->first();

            return $record?->barcode ?? $this->batch_barcode;
        } catch (\Exception $e) {
            \Log::error('Error fetching production barcode', [
                'suspension_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return $this->batch_barcode;
        }
    }
}
