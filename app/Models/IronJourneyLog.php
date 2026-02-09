<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Syncable;

class IronJourneyLog extends Model
{
    use HasFactory, Syncable;

    protected $table = 'iron_journey_logs';

    protected $fillable = [
        'barcode',
        'stage',
        'action',
        'input_barcode',
        'output_barcode',
        'input_weight',
        'output_weight',
        'waste_amount',
        'waste_percentage',
        'worker_id',
        'shift_id',
        'notes',
        'metadata',
        'is_synced',
        'sync_status',
        'synced_at',
    ];

    protected $casts = [
        'input_weight' => 'decimal:2',
        'output_weight' => 'decimal:2',
        'waste_amount' => 'decimal:2',
        'waste_percentage' => 'decimal:2',
        'metadata' => 'array',
        'is_synced' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
