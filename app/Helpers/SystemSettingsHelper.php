<?php

namespace App\Helpers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SystemSettingsHelper
{
    /**
     * Get a system setting value
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("system_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = SystemSetting::where('setting_key', $key)->first();
            return $setting ? $setting->setting_value : $default;
        });
    }

    /**
     * Get waste percentage for production (single value for all stages)
     * @deprecated Use getStageWastePercentage() instead
     */
    public static function getProductionWastePercentage(): float
    {
        $value = self::get('production_waste_percentage', 3); // Default 3%
        return (float) $value;
    }

    /**
     * Get waste percentage for a specific stage
     */
    public static function getStageWastePercentage(int $stage): float
    {
        $key = "stage{$stage}_waste_percentage";
        $value = self::get($key, $stage); // Default to stage number (1%, 2%, 3%, 4%)
        return (float) $value;
    }

    /**
     * Check if waste percentage exceeds the allowed limit
     */
    public static function checkWastePercentage(int $stage, float $inputWeight, float $outputWeight): array
    {
        $inputWeight = max(0, $inputWeight);
        $outputWeight = max(0, min($outputWeight, $inputWeight));
        $wasteWeight = max(0, $inputWeight - $outputWeight);
        $allowedPercentage = self::getStageWastePercentage($stage);
        $actualWaste = $inputWeight > 0
            ? ($wasteWeight / $inputWeight) * 100
            : 0;
        $exceeded = $actualWaste > $allowedPercentage;

        return [
            'exceeded' => $exceeded,
            'waste_weight' => round($wasteWeight, 2),
            'actual_waste' => round($actualWaste, 2),
            'waste_percentage' => round($actualWaste, 2),
            'allowed_percentage' => $allowedPercentage,
            'difference' => round($actualWaste - $allowedPercentage, 2),
            'input_weight' => round($inputWeight, 3),
            'output_weight' => round($outputWeight, 3),
            'should_alert' => $exceeded && self::isWasteAlertsEnabled(),
            'should_suspend' => $exceeded && self::isAutoSuspendEnabled(),
        ];
    }

    /**
     * Check if waste alerts are enabled
     */
    public static function isWasteAlertsEnabled(): bool
    {
        return self::get('enable_waste_alerts', '1') === '1';
    }

    /**
     * Check if auto suspend on waste exceed is enabled
     */
    public static function isAutoSuspendEnabled(): bool
    {
        return self::get('auto_suspend_on_waste_exceed', '1') === '1';
    }

    /**
     * Get max allowed percentage (general)
     */
    public static function getMaxAllowedPercentage(): float
    {
        return (float) self::get('max_allowed_percentage', 5);
    }

    /**
     * Clear settings cache
     */
    public static function clearCache(?string $key = null): void
    {
        if ($key) {
            Cache::forget("system_setting_{$key}");
        } else {
            // Clear all system settings cache
            $settings = SystemSetting::pluck('setting_key');
            foreach ($settings as $settingKey) {
                Cache::forget("system_setting_{$settingKey}");
            }
        }
    }

    /**
     * Get all settings by category
     */
    public static function getByCategory(string $category): array
    {
        return Cache::remember("system_settings_category_{$category}", 3600, function () use ($category) {
            return SystemSetting::where('category', $category)
                ->pluck('setting_value', 'setting_key')
                ->toArray();
        });
    }

    /**
     * Get company information
     */
    public static function getCompanyInfo(): array
    {
        return [
            'name_ar' => self::get('company_name_ar', 'مصنع السلك للحديد'),
            'name_en' => self::get('company_name_en', 'Wire Iron Factory'),
            'phone' => self::get('company_phone', ''),
            'address' => self::get('company_address', ''),
        ];
    }
}
