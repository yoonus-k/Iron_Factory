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
        'original_weight',        // ✅ جديد - تم نقله من Material
        'remaining_weight',       // ✅ جديد - تم نقله من Material
        'unit_id',               // ✅ جديد - تم نقله من Material
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
        'original_weight' => 'float',      // ✅ جديد
        'remaining_weight' => 'float',     // ✅ جديد
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

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);  // ✅ جديد - العلاقة مع وحدة القياس
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * الحصول على اسم الوحدة
     * @return string
     */
    public function getUnitName(): string
    {
        return $this->unit?->unit_name ?? 'وحدة';
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
