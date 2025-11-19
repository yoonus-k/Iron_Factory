<?php

namespace Modules\Manufacturing\Entities;

use Illuminate\Database\Eloquent\Model;

class MaterialBatch extends Model
{
    protected $table = 'material_batches';

    protected $fillable = [
        'material_id',
        'unit_id',
        'batch_code',
        'initial_quantity',
        'available_quantity',
        'batch_date',
        'expire_date',
        'warehouse_id',
        'unit_price',
        'total_value',
        'notes',
    ];

    // علاقات
    public function material()
    {
        return $this->belongsTo(\App\Models\Material::class, 'material_id');
    }

    public function unit()
    {
        return $this->belongsTo(\App\Models\Unit::class, 'unit_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class, 'warehouse_id');
    }
}
