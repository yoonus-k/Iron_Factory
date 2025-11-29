<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage4Box extends Model
{
    protected $table = 'stage4_boxes';

    protected $fillable = [
        'barcode',
        'packaging_type',
        'packaging_type_en',
        'coils_count',
        'total_weight',
        'waste',
        'status',
        'warehouse_id',
        'customer_info',
        'customer_info_en',
        'shipping_address',
        'shipping_address_en',
        'tracking_number',
        'created_by',
        'packed_at',
        'shipped_at',
    ];

    protected $casts = [
        'total_weight' => 'float',
        'waste' => 'float',
        'packed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_PACKING = 'packing';
    const STATUS_PACKED = 'packed';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function boxCoils(): HasMany
    {
        return $this->hasMany(BoxCoil::class, 'box_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
