<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationLog extends Model
{
    protected $table = 'registration_logs';

    protected $fillable = [
        'delivery_note_id',
        'weight_recorded',
        'supplier_id',
        'material_type_id',
        'material_id',
        'unit_id',
        'location',
        'registered_by',
        'registered_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'weight_recorded' => 'float',
        'registered_at' => 'datetime',
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
     * Get the user who registered
     */
    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /**
     * Get the supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the material type
     */
    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }

    /**
     * Get the material
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the unit
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get formatted weight
     */
    public function getFormattedWeight(): string
    {
        return number_format($this->weight_recorded, 2) . ' كيلو';
    }
}
