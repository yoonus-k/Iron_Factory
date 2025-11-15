<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemFormula extends Model
{
    protected $table = 'system_formulas';

    protected $fillable = [
        'formula_name',
        'formula_name_en',
        'stage_number',
        'formula_expression',
        'variables',
        'default_values',
        'description',
        'description_en',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'default_values' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getName($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'ar' ? $this->formula_name : $this->formula_name_en ?? $this->formula_name;
    }
}
