<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialDetail extends Model
{
    protected $table = 'material_details';

    protected $fillable = [
        'warehouse_id',
        'material_id',
        'quantity',
        'min_quantity',
        'max_quantity',
        'location_in_warehouse',
        'location_in_warehouse_en',
        'last_stock_check',
        'notes',
        'notes_en',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'float',
        'min_quantity' => 'float',
        'max_quantity' => 'float',
        'last_stock_check' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * التحقق من الكمية
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_quantity;
    }

    public function isOverStock(): bool
    {
        return $this->quantity >= $this->max_quantity;
    }
}
