<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoxCoil extends Model
{
    protected $table = 'box_coils';

    protected $fillable = [
        'box_id',
        'coil_id',
        'added_at',
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Stage4Box::class, 'box_id');
    }

    public function coil(): BelongsTo
    {
        return $this->belongsTo(Stage3Coil::class, 'coil_id');
    }
}
