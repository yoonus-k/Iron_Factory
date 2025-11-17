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
        'actual_weight',          // ✅ الوزن الفعلي من الميزان
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
        'actual_weight' => 'float',        // ✅ جديد
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

    public function unit()
{
    return $this->belongsTo(Unit::class, 'unit_id');
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
     * تحديث كمية المادة عند تسجيل أذن واردة
     * @param float $quantity الكمية الجديدة المستقبلة
     * @param float $actualWeight الوزن الفعلي
     * @param float $originalWeight الوزن الأصلي
     */
    public function addIncomingQuantity(float $quantity, float $actualWeight, float $originalWeight): void
    {
        // زيادة الكمية
        $this->quantity += $quantity;

        // إضافة الأوزان
        $this->actual_weight = ($this->actual_weight ?? 0) + $actualWeight;
        $this->original_weight = ($this->original_weight ?? 0) + $originalWeight;

        // حفظ التحديثات
        $this->save();
    }

    /**
     * تحديث كمية المادة عند تسجيل أذن صادرة
     * @param float $quantity الكمية المخرجة
     */
    public function reduceOutgoingQuantity(float $quantity): void
    {
        if ($this->quantity >= $quantity) {
            // تقليل الكمية
            $this->quantity -= $quantity;

            // حفظ التحديثات
            $this->save();
        } else {
            throw new \Exception('الكمية المتاحة غير كافية. الكمية المتاحة: ' . $this->quantity);
        }
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
