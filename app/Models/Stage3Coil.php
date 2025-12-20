<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage3Coil extends Model
{
    use Syncable;
    protected $table = 'stage3_coils';

    protected $fillable = [
        'barcode',
        'parent_barcode',
        'stage2_id',
        'coil_number',
        'coil_number_en',
        'wire_size',
        'wire_size_en',
        'base_weight',
        'dye_weight',
        'plastic_weight',
        'total_weight',
        'net_weight',
        'wrapping_id',
        'wrapping_weight',
        'color',
        'color_en',
        'waste',
        'dye_type',
        'dye_type_en',
        'plastic_type',
        'plastic_type_en',
        'status',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'base_weight' => 'float',
        'dye_weight' => 'float',
        'plastic_weight' => 'float',
        'total_weight' => 'float',
        'net_weight' => 'float',
        'wrapping_weight' => 'float',
        'waste' => 'float',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_CREATED = 'created';
    const STATUS_IN_PROCESS = 'in_process';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PACKED = 'packed';

    public function stage2(): BelongsTo
    {
        return $this->belongsTo(Stage2Processed::class, 'stage2_id');
    }

    public function wrapping(): BelongsTo
    {
        return $this->belongsTo(Wrapping::class, 'wrapping_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function boxCoils(): HasMany
    {
        return $this->hasMany(BoxCoil::class, 'coil_id');
    }
}
