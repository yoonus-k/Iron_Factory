<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = [
        'warehouse_code',
        'warehouse_name',
        'warehouse_name_en',
        'warehouse_type',
        'location',
        'location_en',
        'description',
        'description_en',
        'capacity',
        'capacity_unit',
        'manager_name',
        'manager_name_en',
        'contact_number',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function materialDetails(): HasMany
    {
        return $this->hasMany(MaterialDetail::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WarehouseTransaction::class);
    }

    public function getName($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'ar' ? $this->warehouse_name : $this->warehouse_name_en ?? $this->warehouse_name;
    }

    public function getLocation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'ar' ? $this->location : $this->location_en ?? $this->location;
    }

    public function getDescription($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $locale === 'ar' ? $this->description : $this->description_en ?? $this->description;
    }  public function operationLogs(): HasMany
    {
        return $this->hasMany(OperationLog::class, 'record_id')->where('table_name', 'materials');
    }


}
