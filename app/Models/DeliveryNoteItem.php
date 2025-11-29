<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryNoteItem extends Model
{
    protected $fillable = [
        'delivery_note_id',
        'stage4_box_id',
        'barcode',
        'packaging_type',
        'weight',
    ];

    protected $casts = [
        'weight' => 'decimal:3',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقة مع الإذن
     */
    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    /**
     * العلاقة مع الكرتونة
     */
    public function stage4Box(): BelongsTo
    {
        return $this->belongsTo(Stage4Box::class);
    }
}
