<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandUsageHistory extends Model
{
    protected $table = 'stand_usage_history';

    protected $fillable = [
        'stand_id',
        'user_id',
        'material_id',
        'material_barcode',
        'material_type',
        'wire_size',
        'total_weight',
        'net_weight',
        'stand_weight',
        'waste_percentage',
        'cost',
        'notes',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'wire_size' => 'decimal:2',
        'total_weight' => 'decimal:2',
        'net_weight' => 'decimal:2',
        'stand_weight' => 'decimal:2',
        'waste_percentage' => 'decimal:2',
        'cost' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_IN_USE = 'in_use';
    const STATUS_COMPLETED = 'completed';
    const STATUS_RETURNED = 'returned';

    // Relationships
    public function stand()
    {
        return $this->belongsTo(Stand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeInUse($query)
    {
        return $query->where('status', self::STATUS_IN_USE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeByStand($query, $standId)
    {
        return $query->where('stand_id', $standId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
