<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wrapping extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'wrapping_number',
        'weight',
        'description',
        'is_active',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Scope للحصول على اللفافات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * الحصول على جميع اللفافات النشطة
     */
    public static function getActiveWrappings()
    {
        return self::active()->orderBy('wrapping_number')->get();
    }
}
