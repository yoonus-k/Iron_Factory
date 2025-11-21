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
        'weight',
        'weight_unit',
        'recorded_by',
        'approved_by',
        'approved_at',
        'paid_at',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'weight' => 'float',
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

    /**
     * Get delivery notes linked to this invoice
     */
    public function deliveryNotes(): HasMany
    {
        return $this->hasMany(DeliveryNote::class, 'purchase_invoice_id');
    }

    /**
     * Get reconciliation logs for this invoice
     */
    public function reconciliationLogs(): HasMany
    {
        return $this->hasMany(ReconciliationLog::class);
    }

    /**
     * Get operation logs for this invoice
     */
    public function operationLogs(): HasMany
    {
        return $this->hasMany(OperationLog::class, 'record_id')->where('table_name', 'purchase_invoices');
    }

    /**
     * Get invoice items
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    /**
     * Calculate total from items
     */
    public function calculateTotalFromItems(): float
    {
        return $this->items()->sum('total') ?? 0;
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

    /**
     * Get total actual weight from linked delivery notes
     */
    public function getTotalActualWeight(): float
    {
        return $this->deliveryNotes()->sum('actual_weight') ?? 0;
    }

    /**
     * Get total invoice weight from linked delivery notes
     */
    public function getTotalInvoiceWeight(): float
    {
        return $this->deliveryNotes()->sum('invoice_weight') ?? 0;
    }

    /**
     * Get total discrepancy
     */
    public function getTotalDiscrepancy(): float
    {
        return $this->getTotalActualWeight() - $this->getTotalInvoiceWeight();
    }

    /**
     * Get total discrepancy percentage
     */
    public function getDiscrepancyPercentage(): float
    {
        $invoiceWeight = $this->getTotalInvoiceWeight();
        if ($invoiceWeight == 0) {
            return 0;
        }
        return ($this->getTotalDiscrepancy() / $invoiceWeight) * 100;
    }

    /**
     * Check if has discrepancies
     */
    public function hasDiscrepancies(): bool
    {
        return abs($this->getTotalDiscrepancy()) > 0.01;
    }

    /**
     * Check if all delivery notes are reconciled
     */
    public function areAllReconciled(): bool
    {
        $unreconciled = $this->deliveryNotes()
            ->whereNotIn('reconciliation_status', ['matched', 'adjusted'])
            ->count();

        return $unreconciled === 0;
    }

    /**
     * Scope to get invoices with discrepancies
     */
    public function scopeWithDiscrepancies($query)
    {
        return $query->whereHas('deliveryNotes', function ($q) {
            $q->where('reconciliation_status', 'discrepancy');
        });
    }

    /**
     * Scope to get invoices pending reconciliation
     */
    public function scopePendingReconciliation($query)
    {
        return $query->whereHas('deliveryNotes', function ($q) {
            $q->whereNotIn('reconciliation_status', ['matched', 'adjusted', 'rejected']);
        });
    }
}
