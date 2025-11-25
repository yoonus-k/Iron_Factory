<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionStage extends Model
{
    use HasFactory;

    protected $table = 'production_stages';

    protected $fillable = [
        'stage_code',
        'stage_name',
        'stage_name_en',
        'stage_order',
        'description',
        'estimated_duration',
        'is_active',
    ];

    protected $casts = [
        'stage_order' => 'integer',
        'estimated_duration' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * الحصول على جميع المراحل النشطة مرتبة
     */
    public static function getActiveStages()
    {
        return self::where('is_active', true)
            ->orderBy('stage_order')
            ->get();
    }

    /**
     * الحصول على المرحلة بالكود
     */
    public static function getByCode(string $code)
    {
        return self::where('stage_code', $code)->first();
    }

    /**
     * الحصول على المرحلة التالية
     */
    public function getNextStage()
    {
        return self::where('stage_order', '>', $this->stage_order)
            ->where('is_active', true)
            ->orderBy('stage_order')
            ->first();
    }

    /**
     * الحصول على المرحلة السابقة
     */
    public function getPreviousStage()
    {
        return self::where('stage_order', '<', $this->stage_order)
            ->where('is_active', true)
            ->orderBy('stage_order', 'desc')
            ->first();
    }

    /**
     * التحقق من أنها المرحلة الأولى
     */
    public function isFirstStage(): bool
    {
        return $this->stage_order === 1;
    }

    /**
     * التحقق من أنها المرحلة الأخيرة
     */
    public function isLastStage(): bool
    {
        $maxOrder = self::where('is_active', true)->max('stage_order');
        return $this->stage_order === $maxOrder;
    }
}
