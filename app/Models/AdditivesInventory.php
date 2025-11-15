<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditivesInventory extends Model
{
    protected $table = 'additives_inventory';

    protected $fillable = [
        'type',
        'name',
        'name_en',
        'color',
        'color_en',
        'quantity',
        'unit',
        'cost_per_unit',
        'supplier_id',
        'expiry_date',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'float',
        'cost_per_unit' => 'float',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const TYPE_DYE = 'dye';
    const TYPE_PLASTIC = 'plastic';

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getName($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'ar' ? $this->name : $this->name_en ?? $this->name;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && now()->isAfter($this->expiry_date);
    }
}
