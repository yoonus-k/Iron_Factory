<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case PAID = 'paid';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'مسودة',
            self::PENDING => 'قيد الانتظار',
            self::APPROVED => 'موافق عليه',
            self::REJECTED => 'مرفوض',
            self::PAID => 'مدفوع',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::PAID => 'info',
        };
    }

    public static function getStatusOptions(): array
    {
        return self::cases();
    }
}

class PurchaseInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'invoice_reference_number',
        'supplier_id',
        'total_amount',
        'currency',
        'invoice_date',
        'due_date',
        'status',
        'payment_terms',
        'notes',
        'notes_en',
        'recorded_by',
        'approved_by',
        'approved_at',
        'paid_at',
        'is_active',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'status' => InvoiceStatus::class,
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CANCELLED = 'cancelled';

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who recorded this invoice
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get the user who approved this invoice
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Get delivery notes linked to this invoice
     */
    public function deliveryNotes(): HasMany
    {
        return $this->hasMany(DeliveryNote::class, 'invoice_number', 'invoice_number');
    }

    /**
     * Get operation logs for this invoice
     */
    public function operationLogs(): HasMany
    {
        return $this->hasMany(OperationLog::class, 'record_id')->where('table_name', 'purchase_invoices');
    }

    /**
     * Check if invoice is draft
     */
    public function isDraft(): bool
    {
        return $this->status === InvoiceStatus::DRAFT;
    }

    /**
     * Check if invoice is pending approval
     */
    public function isPending(): bool
    {
        return $this->status === InvoiceStatus::PENDING;
    }

    /**
     * Check if invoice is approved
     */
    public function isApproved(): bool
    {
        return $this->status === InvoiceStatus::APPROVED;
    }

    /**
     * Check if invoice is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === InvoiceStatus::REJECTED;
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::PAID;
    }

    /**
     * Scope to get only active invoices
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only pending invoices
     */
    public function scopePending($query)
    {
        return $query->where('status', InvoiceStatus::PENDING->value);
    }

    /**
     * Scope to get only approved invoices
     */
    public function scopeApproved($query)
    {
        return $query->where('status', InvoiceStatus::APPROVED->value);
    }

    /**
     * Scope to get only paid invoices
     */
    public function scopePaid($query)
    {
        return $query->where('status', InvoiceStatus::PAID->value);
    }

    /**
     * Scope to get invoices by supplier
     */
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Calculate days until due
     */
    public function daysUntilDue(): int
    {
        return now()->diffInDays($this->due_date);
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue(): bool
    {
        return !$this->isPaid() && $this->due_date && now()->isAfter($this->due_date);
    }
}
