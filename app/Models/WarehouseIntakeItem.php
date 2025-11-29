<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseIntakeItem extends Model
{
    protected $fillable = [
        'intake_request_id',
        'stage4_box_id',
        'barcode',
        'packaging_type',
        'weight',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
    ];

    /**
     * العلاقات
     */
    public function intakeRequest(): BelongsTo
    {
        return $this->belongsTo(WarehouseIntakeRequest::class, 'intake_request_id');
    }

    public function stage4Box(): BelongsTo
    {
        return $this->belongsTo(Stage4Box::class, 'stage4_box_id');
    }
}
