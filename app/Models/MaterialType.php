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
        
        switch ($locale) {
            case 'ar':
                return $this->type_name ?? $this->type_name_en ?? "N/A";
            case 'en':
                return $this->type_name_en ?? $this->type_name ?? "N/A";
            default:
                return $this->type_name_en ?? $this->type_name ?? "N/A";
        }
    }

    /**
     * الحصول على الاسم الكامل بصيغة (عربي - إنجليزي)
     */
    public function getFullName($locale = null)
    {
        $ar = $this->type_name ?? '';
        $en = $this->type_name_en ?? '';
        
        if ($ar && $en) {
            return "$ar ($en)";
        } elseif ($ar) {
            return $ar;
        } elseif ($en) {
            return $en;
        }
        return "N/A";
    }

    /**
     * الحصول على وصف النوع حسب اللغة
     */
    public function getDescription($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        switch ($locale) {
            case 'ar':
                return $this->description ?? $this->description_en;
            case 'en':
                return $this->description_en ?? $this->description;
            default:
                return $this->description_en ?? $this->description;
        }
    }

    public function operationLogs(): HasMany
    {
        return $this->hasMany(OperationLog::class, 'record_id')->where('table_name', 'material_types');
    }
}
