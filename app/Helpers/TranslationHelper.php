<?php

namespace App\Helpers;

use App\Models\Translation;

class TranslationHelper
{
    /**
     * الحصول على ترجمة معينة
     *
     * @param string $modelType - اسم Model (e.g., 'App\Models\Product')
     * @param int $modelId - ID السجل
     * @param string $key - المفتاح (e.g., 'name', 'description')
     * @param string|null $locale - اللغة (e.g., 'ar', 'en')
     * @return string|null
     */
    public static function get($modelType, $modelId, $key, $locale = null)
    {
        return Translation::getTranslation($modelType, $modelId, $key, $locale);
    }

    /**
     * حفظ أو تحديث ترجمة
     *
     * @param string $modelType
     * @param int $modelId
     * @param string $key
     * @param string $value
     * @param string|null $locale
     * @return Translation
     */
    public static function save($modelType, $modelId, $key, $value, $locale = null)
    {
        return Translation::saveTranslation($modelType, $modelId, $key, $value, $locale);
    }

    /**
     * حفظ ترجمات متعددة دفعة واحدة
     *
     * @param string $modelType
     * @param int $modelId
     * @param array $translations - ['name' => 'value', 'description' => 'value']
     * @param string|null $locale
     * @return array
     */
    public static function saveMultiple($modelType, $modelId, $translations = [], $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $saved = [];

        foreach ($translations as $key => $value) {
            $saved[$key] = Translation::saveTranslation($modelType, $modelId, $key, $value, $locale);
        }

        return $saved;
    }

    /**
     * حفظ ترجمات لعدة لغات دفعة واحدة
     *
     * @param string $modelType
     * @param int $modelId
     * @param array $data - array with locales as keys
     * @return array
     */
    public static function saveForAllLanguages($modelType, $modelId, $data = [])
    {
        $saved = [];

        foreach ($data as $locale => $translations) {
            foreach ($translations as $key => $value) {
                $saved[$locale][$key] = Translation::saveTranslation(
                    $modelType,
                    $modelId,
                    $key,
                    $value,
                    $locale
                );
            }
        }

        return $saved;
    }

    /**
     * الحصول على كل ترجمات سجل معين
     *
     * @param string $modelType
     * @param int $modelId
     * @param string|null $locale
     * @return array
     */
    public static function getAll($modelType, $modelId, $locale = null)
    {
        return Translation::getTranslations($modelType, $modelId, $locale);
    }

    /**
     * الحصول على ترجمات سجل معين لكل اللغات
     *
     * @param string $modelType
     * @param int $modelId
     * @return array - ['ar' => [...], 'en' => [...], ...]
     */
    public static function getAllLanguages($modelType, $modelId)
    {
        $translations = Translation::where('translatable_type', $modelType)
            ->where('translatable_id', $modelId)
            ->get()
            ->groupBy('locale')
            ->map(function ($group) {
                return $group->pluck('value', 'key')->toArray();
            })
            ->toArray();

        return $translations;
    }

    /**
     * حذف ترجمة معينة
     *
     * @param string $modelType
     * @param int $modelId
     * @param string $key
     * @param string|null $locale
     * @return bool
     */
    public static function delete($modelType, $modelId, $key, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Translation::where('translatable_type', $modelType)
            ->where('translatable_id', $modelId)
            ->where('key', $key)
            ->where('locale', $locale)
            ->delete();
    }

    /**
     * حذف كل ترجمات سجل معين
     *
     * @param string $modelType
     * @param int $modelId
     * @return bool
     */
    public static function deleteAll($modelType, $modelId)
    {
        return Translation::where('translatable_type', $modelType)
            ->where('translatable_id', $modelId)
            ->delete();
    }

    /**
     * البحث عن ترجمات تحتوي على نص معين
     *
     * @param string $modelType
     * @param string $searchText
     * @param string|null $locale
     * @return array
     */
    public static function search($modelType, $searchText, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Translation::where('translatable_type', $modelType)
            ->where('locale', $locale)
            ->where('value', 'like', '%' . $searchText . '%')
            ->get()
            ->groupBy('translatable_id')
            ->toArray();
    }
}
