<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialType extends Model
{
    protected $table = 'material_types';

    protected $fillable = [
        'type_code',
        'type_name',
        'type_name_en',
        'category',
        'category_en',
        'description',
        'description_en',
        'specifications',
        'default_unit',
        'standard_cost',
        'storage_conditions',
        'storage_conditions_en',
        'shelf_life_days',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'specifications' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'material_type_id');
    }

    public function getName($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'ar' ? $this->type_name : $this->type_name_en ?? $this->type_name;
    }
}
