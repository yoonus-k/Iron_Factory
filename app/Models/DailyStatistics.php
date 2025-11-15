<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyStatistics extends Model
{
    protected $table = 'daily_statistics';

    protected $fillable = [
        'statistics_date',
        'total_materials_received',
        'total_stands_created',
        'total_coils_produced',
        'total_boxes_packed',
        'total_waste_amount',
        'waste_percentage',
        'active_workers',
        'created_by',
        'calculated_at',
    ];

    protected $casts = [
        'statistics_date' => 'date',
        'total_materials_received' => 'float',
        'total_waste_amount' => 'float',
        'waste_percentage' => 'float',
        'calculated_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
