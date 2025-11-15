<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage1Stand extends Model
{
    protected $table = 'stage1_stands';

    protected $fillable = [
        'barcode',
        'parent_barcode',
        'material_id',
        'stand_number',
        'stand_number_en',
        'wire_size',
        'wire_size_en',
        'weight',
        'waste',
        'remaining_weight',
        'status',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'weight' => 'float',
        'waste' => 'float',
        'remaining_weight' => 'float',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_CREATED = 'created';
    const STATUS_IN_PROCESS = 'in_process';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CONSUMED = 'consumed';

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stage2Processed(): HasMany
    {
        return $this->hasMany(Stage2Processed::class, 'stage1_id');
    }
}
