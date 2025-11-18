<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoiceItem extends Model
{
    protected $fillable = [
        'purchase_invoice_id',
        'material_id',
        'item_name',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_rate',
        'discount_amount',
        'total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'subtotal' => 'float',
        'tax_rate' => 'float',
        'tax_amount' => 'float',
        'discount_rate' => 'float',
        'discount_amount' => 'float',
        'total' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the purchase invoice that owns this item
     */
    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    /**
     * Get the material associated with this item
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Calculate subtotal
     */
    public function calculateSubtotal(): float
    {
        return round($this->quantity * $this->unit_price, 2);
    }

    /**
     * Calculate tax amount
     */
    public function calculateTaxAmount(): float
    {
        return round($this->subtotal * ($this->tax_rate / 100), 2);
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscountAmount(): float
    {
        return round($this->subtotal * ($this->discount_rate / 100), 2);
    }

    /**
     * Calculate total
     */
    public function calculateTotal(): float
    {
        return round($this->subtotal + $this->tax_amount - $this->discount_amount, 2);
    }

    /**
     * Auto-calculate amounts before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->subtotal = $item->calculateSubtotal();
            $item->tax_amount = $item->calculateTaxAmount();
            $item->discount_amount = $item->calculateDiscountAmount();
            $item->total = $item->calculateTotal();
        });
    }
}
