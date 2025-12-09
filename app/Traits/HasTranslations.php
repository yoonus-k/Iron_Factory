<?php

namespace App\Traits;

use App\Helpers\TranslationHelper;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTranslations
{
    /**
     * علاقة الترجمات
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(\App\Models\Translation::class, 'translatable');
    }

    /**
     * الحصول على ترجمة لمفتاح معين
     */
    public function getTranslation($key, $locale = null)
    {
        return TranslationHelper::get(get_class($this), $this->id, $key, $locale);
    }

    /**
     * حفظ ترجمة
     */
    public function setTranslation($key, $value, $locale = null)
    {
        return TranslationHelper::save(get_class($this), $this->id, $key, $value, $locale);
    }

    /**
     * حفظ ترجمات متعددة
     */
    public function setTranslations($translations, $locale = null)
    {
        return TranslationHelper::saveMultiple(get_class($this), $this->id, $translations, $locale);
    }

    /**
     * حفظ ترجمات لكل اللغات
     */
    public function setTranslationsForAllLanguages($data)
    {
        return TranslationHelper::saveForAllLanguages(get_class($this), $this->id, $data);
    }

    /**
     * جلب كل ترجمات السجل
     */
    public function getTranslations($locale = null)
    {
        return TranslationHelper::getAll(get_class($this), $this->id, $locale);
    }

    /**
     * جلب ترجمات السجل لكل اللغات
     */
    public function getAllTranslations()
    {
        return TranslationHelper::getAllLanguages(get_class($this), $this->id);
    }

    /**
     * حذف ترجمة
     */
    public function deleteTranslation($key, $locale = null)
    {
        return TranslationHelper::delete(get_class($this), $this->id, $key, $locale);
    }

    /**
     * حذف كل الترجمات
     */
    public function deleteAllTranslations()
    {
        return TranslationHelper::deleteAll(get_class($this), $this->id);
    }
}
