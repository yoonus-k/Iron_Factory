<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Translation extends Model
{
    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'locale',
        'key',
        'value',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * المالك - يمكن يكون أي Eloquent Model
     */
    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * البحث عن ترجمة معينة
     */
    public static function getTranslation($modelType, $modelId, $key, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return self::where('translatable_type', $modelType)
            ->where('translatable_id', $modelId)
            ->where('key', $key)
            ->where('locale', $locale)
            ->first()?->value;
    }

    /**
     * حفظ ترجمة جديدة أو تحديثها
     */
    public static function saveTranslation($modelType, $modelId, $key, $value, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return self::updateOrCreate(
            [
                'translatable_type' => $modelType,
                'translatable_id' => $modelId,
                'locale' => $locale,
                'key' => $key,
            ],
            [
                'value' => $value,
            ]
        );
    }

    /**
     * الحصول على ترجمات record معين بلغة محددة
     */
    public static function getTranslations($modelType, $modelId, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return self::where('translatable_type', $modelType)
            ->where('translatable_id', $modelId)
            ->where('locale', $locale)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * الحصول على جميع الترجمات لكل اللغات المتاحة
     */
    public static function getAllLanguageTranslations($modelType, $modelId)
    {
        $translations = self::where('translatable_type', $modelType)
            ->where('translatable_id', $modelId)
            ->get();

        $result = [];

        foreach ($translations as $translation) {
            if (!isset($result[$translation->locale])) {
                $result[$translation->locale] = [];
            }
            $result[$translation->locale][$translation->key] = $translation->value;
        }

        return $result;
    }

    /**
     * الحصول على قائمة اللغات المتاحة لـ record معين
     */
    public static function getAvailableLanguages($modelType, $modelId)
    {
        return self::where('translatable_type', $modelType)
            ->where('translatable_id', $modelId)
            ->distinct()
            ->pluck('locale')
            ->toArray();
    }
}
