<?php

namespace Modules\Manufacturing\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Material;
use App\Models\Unit;
use App\Models\Warehouse;

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
        'status',
    ];

    protected $casts = [
        'initial_quantity' => 'float',
        'available_quantity' => 'float',
        'unit_price' => 'float',
        'total_value' => 'float',
        'batch_date' => 'date',
        'expire_date' => 'date',
    ];

    const STATUS_AVAILABLE = 'available';
    const STATUS_IN_PRODUCTION = 'in_production';
    const STATUS_CONSUMED = 'consumed';

    /**
     * العلاقة مع المادة
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * العلاقة مع الوحدة
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * العلاقة مع المستودع
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * التحقق من توفر الكمية
     */
    public function hasAvailableQuantity(float $quantity): bool
    {
        return $this->available_quantity >= $quantity;
    }

    /**
     * خصم كمية من المتاح
     */
    public function reduceQuantity(float $quantity): void
    {
        if (!$this->hasAvailableQuantity($quantity)) {
            throw new \Exception('الكمية المتاحة غير كافية');
        }

        $this->available_quantity -= $quantity;
        
        // تحديث الحالة إذا نفذت الكمية
        if ($this->available_quantity <= 0) {
            $this->status = self::STATUS_CONSUMED;
        }
        
        $this->save();
    }

    /**
     * الحصول على نسبة الاستهلاك
     */
    public function getConsumptionPercentage(): float
    {
        if ($this->initial_quantity <= 0) {
            return 0;
        }

        return (($this->initial_quantity - $this->available_quantity) / $this->initial_quantity) * 100;
    }
}
