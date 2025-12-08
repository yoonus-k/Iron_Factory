<?php

namespace App\Traits;

use App\Models\Translation;

/**
 * Trait HasMultilingualContent
 * 
 * يسهل التعامل مع الترجمات للموديلات المختلفة
 * Simplifies working with translations for different models
 */
trait HasMultilingualContent
{
    /**
     * تأكد من وجود علاقة translations في الموديل
     * Make sure you have morphMany relation in your model:
     * public function getTranslations_relation() { ... }
     */

    /**
     * الحصول على ترجمة أو قيمة افتراضية من الحقل المباشر
     * Get translation or fallback to direct database column
     */
    public function getLocalizedAttribute($attributeName, $locale = null, $fallbackField = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        // حاول الحصول على الترجمة
        $translated = Translation::getTranslation(
            static::class,
            $this->id,
            $attributeName,
            $locale
        );

        if ($translated) return $translated;

        // اعتمد على الحقل المباشر
        if ($fallbackField) {
            return $this->getAttribute($fallbackField);
        }

        // أو حاول الحقل مع اللغة مثل name_ar, name_en
        $fieldWithLocale = $attributeName . '_' . $locale;
        if ($this->hasAttribute($fieldWithLocale)) {
            return $this->getAttribute($fieldWithLocale);
        }

        return null;
    }

    /**
     * حفظ ترجمة لحقل معين
     * Save translation for a specific attribute
     */
    public function saveLocalizedAttribute($attributeName, $value, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        Translation::saveTranslation(
            static::class,
            $this->id,
            $attributeName,
            $value,
            $locale
        );

        return $this;
    }

    /**
     * حفظ ترجمات متعددة مرة واحدة
     * Save multiple translations at once
     */
    public function saveLocalizedAttributes(array $translations, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        foreach ($translations as $key => $value) {
            $this->saveLocalizedAttribute($key, $value, $locale);
        }

        return $this;
    }

    /**
     * الحصول على الترجمات بالكامل لموديل معين
     * Get all translations for a model in specific locale
     */
    public function getLocalizedAttributes($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Translation::getTranslations(
            static::class,
            $this->id,
            $locale
        );
    }

    /**
     * حذف ترجمة معينة
     * Delete specific translation
     */
    public function deleteTranslation($attributeName, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        Translation::where('translatable_type', static::class)
            ->where('translatable_id', $this->id)
            ->where('key', $attributeName)
            ->where('locale', $locale)
            ->delete();

        return $this;
    }

    /**
     * حذف كل الترجمات
     * Delete all translations for this model
     */
    public function deleteAllTranslations()
    {
        Translation::where('translatable_type', static::class)
            ->where('translatable_id', $this->id)
            ->delete();

        return $this;
    }

    /**
     * التحقق من وجود ترجمة
     * Check if translation exists
     */
    public function hasTranslation($attributeName, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return Translation::where('translatable_type', static::class)
            ->where('translatable_id', $this->id)
            ->where('key', $attributeName)
            ->where('locale', $locale)
            ->exists();
    }

    /**
     * الحصول على جميع الترجمات لحقل معين بجميع اللغات
     * Get all translations for specific attribute in all locales
     */
    public function getAttributeInAllLocales($attributeName)
    {
        return Translation::where('translatable_type', static::class)
            ->where('translatable_id', $this->id)
            ->where('key', $attributeName)
            ->pluck('value', 'locale')
            ->toArray();
    }

    /**
     * دالة مساعدة عملية - عرض الترجمة أو الحقل المباشر
     * Helper method - display translation or direct field
     */
    public function trans($attributeName, $locale = null)
    {
        return $this->getLocalizedAttribute($attributeName, $locale);
    }

    /**
     * Magic getter للتعامل مع حقول ديناميكية مثل trans_name
     * Magic getter for dynamic attributes like trans_name, trans_notes
     */
    public function __get($name)
    {
        if (str_starts_with($name, 'trans_')) {
            $attribute = substr($name, 6); // إزالة接头词 trans_
            return $this->trans($attribute);
        }

        return parent::__get($name);
    }
}
