<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'unit_code',
        'unit_name',
        'unit_name_en',
        'unit_symbol',
        'unit_type',
        'conversion_factor',
        'base_unit',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'conversion_factor' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'unit_id');
    }

    public function getName($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        switch ($locale) {
            case 'ar':
                return $this->unit_name ?? $this->unit_name_en ?? $this->unit_symbol ?? "N/A";
            case 'en':
                return $this->unit_name_en ?? $this->unit_name ?? $this->unit_symbol ?? "N/A";
            default:
                return $this->unit_name_en ?? $this->unit_name ?? $this->unit_symbol ?? "N/A";
        }
    }

    /**
     * الحصول على الاسم الكامل بصيغة (عربي - إنجليزي)
     */
    public function getFullName($locale = null)
    {
        $ar = $this->unit_name ?? '';
        $en = $this->unit_name_en ?? '';
        $symbol = $this->unit_symbol ?? '';
        
        if ($ar && $en) {
            return "$ar ($en) $symbol";
        } elseif ($ar) {
            return "$ar $symbol";
        } elseif ($en) {
            return "$en $symbol";
        }
        return $symbol ?? "N/A";
    }
      public function operationLogs(): HasMany
    {
        return $this->hasMany(OperationLog::class, 'record_id')->where('table_name', 'units');
    }
}
