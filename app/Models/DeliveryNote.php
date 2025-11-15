<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryNote extends Model
{
    protected $table = 'delivery_notes';

    protected $fillable = [
        'note_number',
        'material_id',
        'delivered_weight',
        'delivery_date',
        'driver_name',
        'driver_name_en',
        'vehicle_number',
        'received_by',
    ];

    protected $casts = [
        'delivered_weight' => 'float',
        'delivery_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
