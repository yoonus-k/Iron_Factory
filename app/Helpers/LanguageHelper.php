<?php

if (!function_exists('__t')) {
    /**
     * Translate with fallback
     * 
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    function __t($key, $replace = [], $locale = null)
    {
        return __('app.' . $key, $replace, $locale);
    }
}

if (!function_exists('get_language_name')) {
    /**
     * Get current language name
     * 
     * @return string
     */
    function get_language_name()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? 'العربية' : 'English';
    }
}

if (!function_exists('get_language_direction')) {
    /**
     * Get current language direction
     * 
     * @return string
     */
    function get_language_direction()
    {
        return app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
    }
}

if (!function_exists('is_rtl')) {
    /**
     * Check if current language is RTL
     * 
     * @return bool
     */
    function is_rtl()
    {
        return app()->getLocale() === 'ar';
    }
}

if (!function_exists('trans_choice_locale')) {
    /**
     * Get translation with pluralization
     * 
     * @param string $key
     * @param int $number
     * @param array $replace
     * @return string
     */
    function trans_choice_locale($key, $number, $replace = [])
    {
        if (app()->getLocale() === 'ar') {
            // قواعد الجمع العربية
            if ($number == 0) return __('app.' . $key . '.zero', $replace);
            if ($number == 1) return __('app.' . $key . '.one', $replace);
            if ($number == 2) return __('app.' . $key . '.two', $replace);
            if ($number >= 3 && $number <= 10) return __('app.' . $key . '.few', $replace);
            return __('app.' . $key . '.many', $replace);
        }
        
        return trans_choice('app.' . $key, $number, $replace);
    }
}
