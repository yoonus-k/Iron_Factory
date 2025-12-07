<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;

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

    public function coils(): HasManyThrough
    {
        return $this->hasManyThrough(
            Stage3Coil::class,
            BoxCoil::class,
            'box_id',      // Foreign key on box_coils table
            'id',          // Foreign key on stage3_coils table
            'id',          // Local key on stage4_boxes table
            'coil_id'      // Local key on box_coils table
        );
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function deliveryNoteItems(): HasMany
    {
        return $this->hasMany(DeliveryNoteItem::class, 'stage4_box_id');
    }

    /**
     * إحضار مواصفات الصندوق بما في ذلك الرجوع للباركود الأب عند الحاجة.
     */
    public function resolvedMaterials()
    {
        $materials = DB::table('box_coils')
            ->join('stage3_coils', 'box_coils.coil_id', '=', 'stage3_coils.id')
            ->where('box_coils.box_id', $this->id)
            ->select('stage3_coils.color', 'stage3_coils.wire_size', 'stage3_coils.plastic_type')
            ->get();

        if ($materials->isEmpty() && $this->parent_barcode) {
            $materials = DB::table('stage3_coils')
                ->where('stage3_coils.barcode', $this->parent_barcode)
                ->select('stage3_coils.color', 'stage3_coils.wire_size', 'stage3_coils.plastic_type')
                ->get();
        }

        return $materials;
    }
}
