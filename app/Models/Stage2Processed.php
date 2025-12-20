<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage2Processed extends Model
{
    use Syncable;
    protected $table = 'stage2_processed';

    protected $fillable = [
        'barcode',
        'parent_barcode',
        'stage1_id',
        'process_details',
        'process_details_en',
        'input_weight',
        'output_weight',
        'waste',
        'remaining_weight',
        'status',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'input_weight' => 'float',
        'output_weight' => 'float',
        'waste' => 'float',
        'remaining_weight' => 'float',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_STARTED = 'started';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CONSUMED = 'consumed';

    public function stage1(): BelongsTo
    {
        return $this->belongsTo(Stage1Stand::class, 'stage1_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stage3Coils(): HasMany
    {
        return $this->hasMany(Stage3Coil::class, 'stage2_id');
    }
}
