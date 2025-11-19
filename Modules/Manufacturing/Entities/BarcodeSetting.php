<?php

namespace Modules\Manufacturing\Entities;

use Illuminate\Database\Eloquent\Model;

class BarcodeSetting extends Model
{
    protected $table = 'barcode_settings';
    protected $fillable = [
        'type', 'prefix', 'current_number', 'year', 'format', 'auto_increment', 'padding', 'is_active'
    ];
}
