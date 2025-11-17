<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReconciliationLog extends Model
{
    protected $table = 'reconciliation_logs';

    protected $fillable = [
        'delivery_note_id',
        'purchase_invoice_id',
        'actual_weight',
        'invoice_weight',
        'financial_impact',
        'action',
        'reason',
        'comments',
        'decided_by',
        'decided_at',
    ];

    protected $casts = [
        'actual_weight' => 'float',
        'invoice_weight' => 'float',
        'financial_impact' => 'float',
        'decided_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the delivery note
     */
    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    /**
     * Get the purchase invoice
     */
    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    /**
     * Get the user who made the decision
     */
    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    /**
     * Get discrepancy in KG
     */
    public function getDiscrepancyKg(): float
    {
        return $this->actual_weight - $this->invoice_weight;
    }

    /**
     * Get discrepancy percentage
     */
    public function getDiscrepancyPercentage(): float
    {
        if ($this->invoice_weight == 0) {
            return 0;
        }
        return ($this->getDiscrepancyKg() / $this->invoice_weight) * 100;
    }

    /**
     * Check if discrepancy is positive (overcharged)
     */
    public function isOvercharged(): bool
    {
        return $this->getDiscrepancyKg() > 0;
    }

    /**
     * Check if discrepancy is negative (undercharged)
     */
    public function isUndercharged(): bool
    {
        return $this->getDiscrepancyKg() < 0;
    }

    /**
     * Check if action is accepted
     */
    public function isAccepted(): bool
    {
        return $this->action === 'accepted';
    }

    /**
     * Check if action is rejected
     */
    public function isRejected(): bool
    {
        return $this->action === 'rejected';
    }

    /**
     * Check if action is adjusted
     */
    public function isAdjusted(): bool
    {
        return $this->action === 'adjusted';
    }

    /**
     * Check if action is pending
     */
    public function isPending(): bool
    {
        return $this->action === 'pending';
    }

    /**
     * Scope to get pending decisions
     */
    public function scopePending($query)
    {
        return $query->where('action', 'pending');
    }

    /**
     * Scope to get accepted logs
     */
    public function scopeAccepted($query)
    {
        return $query->where('action', 'accepted');
    }

    /**
     * Scope to get rejected logs
     */
    public function scopeRejected($query)
    {
        return $query->where('action', 'rejected');
    }

    /**
     * Scope to get adjusted logs
     */
    public function scopeAdjusted($query)
    {
        return $query->where('action', 'adjusted');
    }

    /**
     * Scope to get discrepancies
     */
    public function scopeWithDiscrepancies($query)
    {
        return $query->whereNotRaw('actual_weight = invoice_weight');
    }
}
