<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WasteLimit extends Model
{
    protected $table = 'waste_limits';

    protected $fillable = [
        'stage_number',
        'stage_name',
        'stage_name_en',
        'max_waste_percentage',
        'warning_percentage',
        'alert_supervisors',
        'stop_production',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'max_waste_percentage' => 'float',
        'warning_percentage' => 'float',
        'alert_supervisors' => 'boolean',
        'stop_production' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getName($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'ar' ? $this->stage_name : $this->stage_name_en ?? $this->stage_name;
    }
}
