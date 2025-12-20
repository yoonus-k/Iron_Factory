<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialMovement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'movement_number',
        'movement_type',
        'source',
        'delivery_note_id',
        'reconciliation_log_id',
        'material_detail_id',
        'material_id',
        'unit_id',
        'quantity',
        'unit_price',
        'total_value',
        'from_warehouse_id',
        'to_warehouse_id',
        'supplier_id',
        'destination',
        'description',
        'notes',
        'reference_number',
        'created_by',
        'movement_date',
        'ip_address',
        'user_agent',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total_value' => 'decimal:2',
        'movement_date' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Generate unique movement number
     */
    public static function generateMovementNumber(): string
    {
        $prefix = 'MV';
        $date = now()->format('Ymd');
        
        // استخدام database lock لضمان عدم التكرار عند التسجيل المتزامن
        $lastMovement = self::whereDate('created_at', now())
            ->lockForUpdate()
            ->latest('id')
            ->first();
            
        $sequence = $lastMovement ? (intval(substr($lastMovement->movement_number, -4)) + 1) : 1;

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    public function reconciliationLog(): BelongsTo
    {
        return $this->belongsTo(ReconciliationLog::class);
    }

    public function materialDetail(): BelongsTo
    {
        return $this->belongsTo(MaterialDetail::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function materialBatch(): BelongsTo
    {
        return $this->belongsTo(\Modules\Manufacturing\Entities\MaterialBatch::class, 'batch_id');
    }

    /**
     * Scopes
     */
    public function scopeIncoming($query)
    {
        return $query->where('movement_type', 'incoming');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('movement_type', 'outgoing');
    }

    public function scopeToProduction($query)
    {
        return $query->where('movement_type', 'to_production');
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where(function($q) use ($warehouseId) {
            $q->where('from_warehouse_id', $warehouseId)
              ->orWhere('to_warehouse_id', $warehouseId);
        });
    }

    /**
     * Accessors
     */
    public function getMovementTypeNameAttribute(): string
    {
        // تخصيص الاسم بناءً على المصدر والنوع
        if ($this->movement_type === 'adjustment' && $this->source === 'production' && $this->reference_number) {
            return 'تحديث كمية المستودع';
        }
        
        $types = [
            'incoming' => 'دخول بضاعة',
            'outgoing' => 'خروج بضاعة',
            'transfer' => 'نقل بين مستودعات',
            'to_production' => 'نقل للإنتاج',
            'from_production' => 'إرجاع من الإنتاج',
            'adjustment' => 'تسوية',
            'reconciliation' => 'تعديل بعد التسوية',
            'waste' => 'هدر',
            'return' => 'إرجاع للمورد',
        ];

        return $types[$this->movement_type] ?? $this->movement_type;
    }

    public function getSourceNameAttribute(): string
    {
        $sources = [
            'registration' => 'تسجيل البضاعة',
            'reconciliation' => 'التسوية',
            'production' => 'الإنتاج',
            'transfer' => 'نقل بين مستودعات',
            'manual' => 'تعديل يدوي',
            'system' => 'النظام',
        ];

        return $sources[$this->source] ?? $this->source;
    }

    public function getStatusNameAttribute(): string
    {
        $statuses = [
            'pending' => 'معلق',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
        ];

        return $badges[$this->status] ?? 'secondary';
    }
}
