<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryNoteCoil extends Model
{
    protected $table = 'delivery_note_coils';

    protected $fillable = [
        'delivery_note_id',
        'coil_number',
        'coil_weight',
        'remaining_weight',
        'coil_barcode',
        'status',
        'notes',
    ];

    protected $casts = [
        'coil_weight' => 'float',
        'remaining_weight' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the delivery note that owns the coil
     */
    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class, 'delivery_note_id');
    }

    /**
     * Get all transfers for this coil
     */
    public function transfers(): HasMany
    {
        return $this->hasMany(CoilTransfer::class, 'coil_id');
    }

    /**
     * Check if coil is available for transfer
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->remaining_weight > 0;
    }

    /**
     * Check if coil is partially used
     */
    public function isPartiallyUsed(): bool
    {
        return $this->status === 'partially_used';
    }

    /**
     * Check if coil is fully used
     */
    public function isFullyUsed(): bool
    {
        return $this->status === 'fully_used';
    }

    /**
     * Update coil status based on remaining weight
     */
    public function updateStatus(): void
    {
        if ($this->remaining_weight <= 0) {
            $this->status = 'fully_used';
        } elseif ($this->remaining_weight < $this->coil_weight) {
            $this->status = 'partially_used';
        } else {
            $this->status = 'available';
        }
        $this->save();
    }

    /**
     * Get usage percentage
     */
    public function getUsagePercentage(): float
    {
        if ($this->coil_weight <= 0) {
            return 0;
        }
        $used = $this->coil_weight - $this->remaining_weight;
        return ($used / $this->coil_weight) * 100;
    }

    /**
     * Generate unique coil barcode from barcode_settings
     */
    public static function generateBarcode(DeliveryNote $deliveryNote, int $coilNumber): string
    {
        // محاولة جلب إعدادات الباركود للكويلات
        $barcodeSetting = \App\Models\BarcodeSetting::where('type', 'coil')
            ->where('is_active', true)
            ->first();

        if ($barcodeSetting) {
            $year = date('Y');
            $nextNumber = $barcodeSetting->current_number + 1;
            $numberStr = str_pad($nextNumber, $barcodeSetting->padding, '0', STR_PAD_LEFT);
            $barcode = str_replace(
                ['{prefix}', '{year}', '{number}'],
                [$barcodeSetting->prefix, $year, $numberStr],
                $barcodeSetting->format
            );
            $barcodeSetting->current_number = $nextNumber;
            $barcodeSetting->save();
            return $barcode;
        }

        // Fallback: إذا لم توجد إعدادات، استخدم الطريقة القديمة
        $prefix = 'COIL';
        $noteId = str_pad($deliveryNote->id, 6, '0', STR_PAD_LEFT);
        $coilNum = str_pad($coilNumber, 3, '0', STR_PAD_LEFT);
        $timestamp = now()->format('ymdHis');
        
        return "{$prefix}-{$noteId}-{$coilNum}-{$timestamp}";
    }
}
