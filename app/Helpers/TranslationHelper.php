<?php

namespace App\Helpers;

use App\Models\Translation;

/**
 * Helper class لإدارة الترجمات
 * Helper class for managing translations
 */
class TranslationHelper
{
    /**
     * الحصول على ترجمة معينة
     * Get specific translation
     *
     * Example: trans_get('App\\Models\\Material', 1, 'name', 'ar')
     */
    public static function get($modelType, $modelId, $key, $locale = null)
    {
        return Translation::getTranslation($modelType, $modelId, $key, $locale);
    }

    /**
     * حفظ ترجمة
     * Save translation
     *
     * Example: trans_save('App\\Models\\Material', 1, 'name', 'اسم المادة', 'ar')
     */
    public static function save($modelType, $modelId, $key, $value, $locale = null)
    {
        return Translation::saveTranslation($modelType, $modelId, $key, $value, $locale);
    }

    /**
     * الحصول على كل الترجمات
     * Get all translations for a model
     */
    public static function getAll($modelType, $modelId, $locale = null)
    {
        return Translation::getTranslations($modelType, $modelId, $locale);
    }

    /**
     * الحصول على قيمة مع fallback
     * Get value with fallback to database column
     */
    public static function fallback($model, $attribute, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        // Try translation first
        $translated = Translation::getTranslation(
            get_class($model),
            $model->id,
            $attribute,
            $locale
        );

        if ($translated) return $translated;

        // Fallback to database field
        $fieldWithLocale = $attribute . '_' . $locale;
        if (isset($model->$fieldWithLocale)) {
            return $model->$fieldWithLocale;
        }

        // Default field
        return isset($model->$attribute) ? $model->$attribute : null;
    }

    /**
     * عرض الترجمة بناءً على اللغة الحالية
     * Display translation based on current locale
     */
    public static function display($model, $attribute, $locale = null)
    {
        return self::fallback($model, $attribute, $locale);
    }

    /**
     * حذف ترجمة معينة
     * Delete specific translation
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
     * حذف كل الترجمات لموديل
     * Delete all translations for model
     */
    public static function deleteAll($modelType, $modelId)
    {
        return Translation::where('translatable_type', $modelType)
            ->where('translatable_id', $modelId)
            ->delete();
    }

    /**
     * التحقق من وجود ترجمة
     * Check if translation exists
     */
    public static function exists($modelType, $modelId, $key, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Translation::where('translatable_type', $modelType)
            ->where('translatable_id', $modelId)
            ->where('key', $key)
            ->where('locale', $locale)
            ->exists();
    }

    /**
     * الحصول على حقل بجميع اللغات
     * Get attribute in all locales
     *
     * Returns: ['ar' => 'القيمة بالعربية', 'en' => 'Value in English']
     */
    public static function getInAllLocales($modelType, $modelId, $key)
    {
        return Translation::where('translatable_type', $modelType)
            ->where('translatable_id', $modelId)
            ->where('key', $key)
            ->pluck('value', 'locale')
            ->toArray();
    }

    /**
     * تحديث الترجمات بكفاءة
     * Batch update translations
     *
     * Example:
     * trans_batch_save($material, [
     *     'ar' => ['name' => 'اسم', 'notes' => 'ملاحظات'],
     *     'en' => ['name' => 'Name', 'notes' => 'Notes']
     * ])
     */
    public static function batchSave($model, array $translationsByLocale)
    {
        $modelType = get_class($model);
        $modelId = $model->id;

        foreach ($translationsByLocale as $locale => $translations) {
            foreach ($translations as $key => $value) {
                self::save($modelType, $modelId, $key, $value, $locale);
            }
        }

        return $model;
    }

    /**
     * البحث عن موديلات بناءً على ترجمة
     * Search models by translation value
     *
     * Example:
     * trans_search('App\\Models\\Material', 'name', 'اسم', 'ar')
     */
    public static function search($modelType, $key, $value, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Translation::where('translatable_type', $modelType)
            ->where('key', $key)
            ->where('value', 'LIKE', '%' . $value . '%')
            ->where('locale', $locale)
            ->pluck('translatable_id');
    }
}
