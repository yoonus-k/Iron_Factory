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
        'batch_id', // ✅ رقم الدفعة
        'production_barcode', // ✅ باركود الإنتاج المولد عند النقل
        'warehouse_id', // ✅ المستودع - يكون إجباري عند عدم وجود material_detail_id
        'delivery_quantity', // كمية الأذن المسلمة
        'delivered_weight',
        'actual_weight', // الوزن الفعلي من الميزان ✅
        'weight_from_scale', // ✅ الوزن المسجل من الميزان (جديد)
        'invoice_weight', // وزن الفاتورة
        'weight_discrepancy', // الفرق
        'quantity', // ✅ الكمية المسجلة لهذه الأذن على حدة
        'quantity_used', // ✅ الكمية المستخدمة من هذه الأذن
        'quantity_remaining', // ✅ الكمية المتبقية من هذه الأذن
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
        'notes', // ملاحظات ✅
        // ========== الحقول الجديدة ==========
        'registration_status',
        'registered_by',
        'registered_at',
        'purchase_invoice_id',
        'invoice_date',
        'reconciliation_status',
        'reconciliation_notes',
        'reconciled_by',
        'reconciled_at',
        'is_locked',
        'lock_reason',
        // ========== حقول منع التكرار ==========
        'deduplicate_key',
        'registration_attempts',
        'last_registration_log_id',
        // ========== حقول المراحل والتأكيد ==========
        'production_stage',
        'production_stage_name',
        'coil_number', // ✅ رقم الكويل (اختياري)
        'assigned_to',
        'transfer_status',
        'confirmed_by',
        'confirmed_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'actual_received_quantity',
    ];

    protected $casts = [
        'delivery_quantity' => 'float',
        'delivered_weight' => 'float',
        'actual_weight' => 'float',
        'weight_from_scale' => 'float', // ✅ الوزن من الميزان
        'invoice_weight' => 'float',
        'weight_discrepancy' => 'float',
        'quantity' => 'float', // ✅ الكمية لهذه الأذن
        'quantity_used' => 'float', // ✅ الكمية المستخدمة
        'quantity_remaining' => 'float', // ✅ الكمية المتبقية
        'delivery_date' => 'date',
        'approved_at' => 'datetime',
        'status' => DeliveryNoteStatus::class,
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // ========== الحقول الجديدة ==========
        'registered_at' => 'datetime',
        'invoice_date' => 'date',
        'reconciled_at' => 'datetime',
        'is_locked' => 'boolean',
        // ========== حقول المراحل والتأكيد ==========
        'confirmed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'actual_received_quantity' => 'decimal:2',
    ];

    /**
     * Get the material associated with this delivery note
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the purchase invoice associated with this delivery note
     */
    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    /**
     * Get reconciliation logs for this delivery note
     */
    public function reconciliationLogs(): HasMany
    {
        return $this->hasMany(ReconciliationLog::class);
    }

    /**
     * Get registration logs for this delivery note
     */
    public function registrationLogs(): HasMany
    {
        return $this->hasMany(RegistrationLog::class);
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
     * Get the user who created/recorded this delivery note (alias for recordedBy)
     */
    public function creator(): BelongsTo
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
     * Get the user who registered this delivery note
     */
    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /**
     * Get the user who reconciled this delivery note
     */
    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    /**
     * Get the user who transferred to production
     */
    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    /**
     * العلاقة مع الموظف المكلف بالاستلام
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * العلاقة مع الموظف الذي أكد الاستلام
     */
    public function confirmedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * العلاقة مع الموظف الذي رفض الاستلام
     */
    public function rejectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * العلاقة مع سجل التأكيد
     */
    public function productionConfirmation()
    {
        return $this->hasOne(ProductionConfirmation::class);
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
     * Get the material batch
     */
    public function materialBatch(): BelongsTo
    {
        return $this->belongsTo(\Modules\Manufacturing\Entities\MaterialBatch::class, 'batch_id');
    }

    /**
     * Get the product tracking (for production stages)
     */
    public function productTracking()
    {
        return $this->hasOne(\App\Models\ProductTracking::class, 'barcode', 'production_barcode');
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
     * Get the warehouse associated with this delivery note
     * ✅ جديد - للسماح بتسجيل الأذن بدون مادة محددة
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
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

    /**
     * Check if linked to invoice
     */
    public function isLinkedToInvoice(): bool
    {
        return $this->purchase_invoice_id !== null;
    }

    /**
     * Check if registered in warehouse
     */
    public function isRegistered(): bool
    {
        return $this->registration_status === 'registered' ||
               $this->registration_status === 'in_production' ||
               $this->registration_status === 'completed';
    }

    /**
     * Check if can be moved to production
     */
    public function canBeMovedToProduction(): bool
    {
        return $this->registration_status === 'registered' && !$this->is_locked;
    }

    /**
     * Check if reconciliation is complete
     */
    public function isReconciled(): bool
    {
        return $this->reconciliation_status === 'matched' ||
               $this->reconciliation_status === 'adjusted';
    }

    /**
     * Get discrepancy from reconciliation logs
     */
    public function getDiscrepancy(): float
    {
        return $this->weight_discrepancy ?? 0;
    }

    /**
     * Get discrepancy percentage
     */
    public function getDiscrepancyPercentage(): float
    {
        return $this->discrepancy_percentage ?? 0;
    }

    /**
     * Scope to get pending registrations
     */
    public function scopePendingRegistration($query)
    {
        return $query->where('registration_status', 'not_registered');
    }

    /**
     * Scope to get pending reconciliation
     */
    public function scopePendingReconciliation($query)
    {
        return $query->where('reconciliation_status', 'pending');
    }

    /**
     * Scope to get discrepancies
     */
    public function scopeWithDiscrepancies($query)
    {
        return $query->where('reconciliation_status', 'discrepancy');
    }
}
