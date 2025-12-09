<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoilTransfer extends Model
{
    protected $table = 'coil_transfers';

    protected $fillable = [
        'coil_id',
        'transfer_weight',
        'production_barcode',
        'warehouse_barcode',
        'transferred_by',
        'transferred_at',
        'notes',
    ];

    protected $casts = [
        'transfer_weight' => 'float',
        'transferred_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the coil that was transferred
     */
    public function coil(): BelongsTo
    {
        return $this->belongsTo(DeliveryNoteCoil::class, 'coil_id');
    }

    /**
     * Get the user who performed the transfer
     */
    public function transferredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    /**
     * Check if this transfer resulted in a split (warehouse barcode exists)
     */
    public function isSplit(): bool
    {
        return !is_null($this->warehouse_barcode);
    }

    /**
     * Generate production barcode
     */
    public static function generateProductionBarcode(DeliveryNoteCoil $coil): string
    {
        $prefix = 'PROD';
        $coilId = str_pad($coil->id, 6, '0', STR_PAD_LEFT);
        $timestamp = now()->format('ymdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        
        return "{$prefix}-{$coilId}-{$timestamp}-{$random}";
    }

    /**
     * Generate warehouse barcode for remaining weight
     */
    public static function generateWarehouseBarcode(DeliveryNoteCoil $coil): string
    {
        $prefix = 'WH';
        $coilId = str_pad($coil->id, 6, '0', STR_PAD_LEFT);
        $timestamp = now()->format('ymdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        
        return "{$prefix}-{$coilId}-{$timestamp}-{$random}";
    }
}
