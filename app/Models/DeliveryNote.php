<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

enum DeliveryNoteStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'قيد الانتظار',
            self::APPROVED => 'موافق عليه',
            self::REJECTED => 'مرفوض',
            self::COMPLETED => 'مكتمل',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::COMPLETED => 'blue',
        };
    }
}

class DeliveryNote extends Model
{
    protected $table = 'delivery_notes';

    protected $fillable = [
        'note_number',
        'type', // incoming / outgoing
        'status', // pending, approved, rejected, completed
        'material_id',
        'material_detail_id', // ربط مع تفاصيل المادة بالمستودع (لتجنب التكرار)
        'delivery_quantity', // كمية الأذن المسلمة
        'delivered_weight',
        'actual_weight', // الوزن الفعلي من الميزان
        'invoice_weight', // وزن الفاتورة
        'weight_discrepancy', // الفرق
        'delivery_date',
        'driver_name',
        'driver_name_en',
        'vehicle_number',
        'received_by',
        'recorded_by', // من سجل الأذن
        'approved_by', // من وافق على الأذن
        'approved_at',
        'supplier_id', // المورد (للواردة)
        'destination_id', // الوجهة (للصادرة)
        'invoice_number', // رقم الفاتورة
        'invoice_reference_number', // رقم مرجع الفاتورة
        'is_active',
    ];

    protected $casts = [
        'delivery_quantity' => 'float',
        'delivered_weight' => 'float',
        'actual_weight' => 'float',
        'invoice_weight' => 'float',
        'weight_discrepancy' => 'float',
        'delivery_date' => 'date',
        'approved_at' => 'datetime',
        'status' => DeliveryNoteStatus::class,
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the material associated with this delivery note
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the user who received the delivery
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the user who recorded this delivery note
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get the user who approved this delivery note
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the supplier (for incoming delivery notes)
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the destination warehouse (for outgoing delivery notes)
     */
    public function destination()
    {
        return $this->belongsTo(Warehouse::class, 'destination_id');
    }

    /**
     * Get the material detail from warehouse (to avoid duplicating data)
     * This links to the actual warehouse inventory
     */
    public function materialDetail()
    {
        return $this->belongsTo(MaterialDetail::class);
    }

    /**
     * Get operation logs for this delivery note
     */
    public function operationLogs(): HasMany
    {
        return $this->hasMany(OperationLog::class, 'record_id')->where('table_name', 'delivery_notes');
    }

    /**
     * Calculate weight discrepancy
     */
    public function calculateDiscrepancy(): void
    {
        if ($this->actual_weight && $this->invoice_weight) {
            $this->weight_discrepancy = $this->actual_weight - $this->invoice_weight;
        }
    }

    /**
     * Boot model - calculate discrepancy before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->calculateDiscrepancy();
        });
    }

    /**
     * Check if this is an incoming delivery note
     */
    public function isIncoming(): bool
    {
        return $this->type === 'incoming';
    }

    /**
     * Check if this is an outgoing delivery note
     */
    public function isOutgoing(): bool
    {
        return $this->type === 'outgoing';
    }

    /**
     * Check if delivery note is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if delivery note is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if delivery note is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if delivery note is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Scope to get only incoming delivery notes
     */
    public function scopeIncoming($query)
    {
        return $query->where('type', 'incoming');
    }

    /**
     * Scope to get only outgoing delivery notes
     */
    public function scopeOutgoing($query)
    {
        return $query->where('type', 'outgoing');
    }

    /**
     * Scope to get only pending delivery notes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get only approved delivery notes
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
