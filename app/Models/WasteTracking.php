<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WasteTracking extends Model
{
    protected $table = 'waste_tracking';

    protected $fillable = [
        'stage_number',
        'item_barcode',
        'waste_amount',
        'waste_percentage',
        'waste_reason',
        'waste_reason_en',
        'reported_by',
        'supervisor_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'waste_amount' => 'float',
        'waste_percentage' => 'float',
        'supervisor_approved' => 'boolean',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
