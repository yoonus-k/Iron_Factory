<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Syncable;

class MaterialBatch extends Model
{
    use HasFactory, Syncable;

    protected $table = 'material_batches';

    protected $fillable = [
        'batch_code',
        'material_id',
        'material_detail_id',
        'supplier_id',
        'warehouse_id',
        'quantity',
        'unit_id',
        'unit_price',
        'total_price',
        'production_date',
        'expiry_date',
        'status',
        'notes',
        'production_barcode',
        'coil_number',
        'transferred_to_production',
        'is_synced',
        'sync_status',
        'synced_at',
    ];

    protected $casts = [
        'production_date' => 'date',
        'expiry_date' => 'date',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'transferred_to_production' => 'decimal:2',
        'is_synced' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function materialDetail()
    {
        return $this->belongsTo(MaterialDetail::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
