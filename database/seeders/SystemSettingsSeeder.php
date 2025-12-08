<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $settings = [
            // إعدادات الحسابات والنسب
            [
                'setting_key' => 'max_allowed_percentage',
                'setting_value' => '5',
                'setting_value_en' => '5',
                'setting_type' => 'number',
                'category' => 'calculations',
                'description' => 'النسبة المسموح بها للفرق بين الوزن الفعلي والوزن المتوقع (بالنسبة المئوية)',
                'description_en' => 'Maximum allowed percentage difference between actual and expected weight',
                'is_public' => false,
            ],
            [
                'setting_key' => 'min_weight_threshold',
                'setting_value' => '100',
                'setting_value_en' => '100',
                'setting_type' => 'number',
                'category' => 'calculations',
                'description' => 'الحد الأدنى للوزن المسموح به (كجم)',
                'description_en' => 'Minimum weight threshold (kg)',
                'is_public' => false,
            ],
            [
                'setting_key' => 'max_weight_threshold',
                'setting_value' => '10000',
                'setting_value_en' => '10000',
                'setting_type' => 'number',
                'category' => 'calculations',
                'description' => 'الحد الأقصى للوزن المسموح به (كجم)',
                'description_en' => 'Maximum weight threshold (kg)',
                'is_public' => false,
            ],
            
            // إعدادات الإنتاج
            [
                'setting_key' => 'production_batch_size',
                'setting_value' => '1000',
                'setting_value_en' => '1000',
                'setting_type' => 'number',
                'category' => 'production',
                'description' => 'الحجم الافتراضي لدفعة الإنتاج (كجم)',
                'description_en' => 'Default production batch size (kg)',
                'is_public' => false,
            ],
            [
                'setting_key' => 'enable_quality_check',
                'setting_value' => '1',
                'setting_value_en' => '1',
                'setting_type' => 'boolean',
                'category' => 'production',
                'description' => 'تفعيل فحص الجودة للمنتجات',
                'description_en' => 'Enable quality check for products',
                'is_public' => false,
            ],
            [
                'setting_key' => 'auto_approve_batches',
                'setting_value' => '0',
                'setting_value_en' => '0',
                'setting_type' => 'boolean',
                'category' => 'production',
                'description' => 'الموافقة التلقائية على دفعات الإنتاج',
                'description_en' => 'Auto approve production batches',
                'is_public' => false,
            ],
            
            // إعدادات نسب الهدر المسموح بها في المراحل
            [
                'setting_key' => 'stage1_waste_percentage',
                'setting_value' => '1',
                'setting_value_en' => '1',
                'setting_type' => 'number',
                'category' => 'waste_limits',
                'description' => 'نسبة الهدر المسموح بها في المرحلة الأولى (الاستاندات) (%)',
                'description_en' => 'Allowed waste percentage in Stage 1 (Stands) (%)',
                'is_public' => false,
            ],
            [
                'setting_key' => 'stage2_waste_percentage',
                'setting_value' => '2',
                'setting_value_en' => '2',
                'setting_type' => 'number',
                'category' => 'waste_limits',
                'description' => 'نسبة الهدر المسموح بها في المرحلة الثانية (المعالجة) (%)',
                'description_en' => 'Allowed waste percentage in Stage 2 (Processing) (%)',
                'is_public' => false,
            ],
            [
                'setting_key' => 'stage3_waste_percentage',
                'setting_value' => '3',
                'setting_value_en' => '3',
                'setting_type' => 'number',
                'category' => 'waste_limits',
                'description' => 'نسبة الهدر المسموح بها في المرحلة الثالثة (اللفائف) (%)',
                'description_en' => 'Allowed waste percentage in Stage 3 (Coils) (%)',
                'is_public' => false,
            ],
            [
                'setting_key' => 'stage4_waste_percentage',
                'setting_value' => '4',
                'setting_value_en' => '4',
                'setting_type' => 'number',
                'category' => 'waste_limits',
                'description' => 'نسبة الهدر المسموح بها في المرحلة الرابعة (التعبئة) (%)',
                'description_en' => 'Allowed waste percentage in Stage 4 (Packaging) (%)',
                'is_public' => false,
            ],
            [
                'setting_key' => 'enable_waste_alerts',
                'setting_value' => '1',
                'setting_value_en' => '1',
                'setting_type' => 'boolean',
                'category' => 'waste_limits',
                'description' => 'تفعيل إرسال تنبيهات عند تجاوز نسب الهدر المسموح بها',
                'description_en' => 'Enable waste percentage alerts',
                'is_public' => false,
            ],
            [
                'setting_key' => 'auto_suspend_on_waste_exceed',
                'setting_value' => '1',
                'setting_value_en' => '1',
                'setting_type' => 'boolean',
                'category' => 'waste_limits',
                'description' => 'إيقاف المرحلة تلقائياً عند تجاوز نسبة الهدر المسموح بها',
                'description_en' => 'Auto suspend stage when waste percentage is exceeded',
                'is_public' => false,
            ],
            
            // إعدادات المستودع
            [
                'setting_key' => 'warehouse_alert_threshold',
                'setting_value' => '100',
                'setting_value_en' => '100',
                'setting_type' => 'number',
                'category' => 'warehouse',
                'description' => 'الحد الأدنى للمخزون قبل إرسال تنبيه (كجم)',
                'description_en' => 'Minimum inventory level before alert (kg)',
                'is_public' => false,
            ],
            [
                'setting_key' => 'enable_warehouse_tracking',
                'setting_value' => '1',
                'setting_value_en' => '1',
                'setting_type' => 'boolean',
                'category' => 'warehouse',
                'description' => 'تفعيل تتبع حركة المواد في المستودعات',
                'description_en' => 'Enable material movement tracking in warehouses',
                'is_public' => false,
            ],
            
            // إعدادات النظام العامة
            [
                'setting_key' => 'company_name_ar',
                'setting_value' => 'مصنع السلك للحديد',
                'setting_value_en' => 'Wire Iron Factory',
                'setting_type' => 'string',
                'category' => 'general',
                'description' => 'اسم الشركة',
                'description_en' => 'Company name',
                'is_public' => true,
            ],
            [
                'setting_key' => 'company_phone',
                'setting_value' => '+966 XX XXX XXXX',
                'setting_value_en' => '+966 XX XXX XXXX',
                'setting_type' => 'string',
                'category' => 'general',
                'description' => 'رقم هاتف الشركة',
                'description_en' => 'Company phone number',
                'is_public' => true,
            ],
            [
                'setting_key' => 'company_address',
                'setting_value' => 'الرياض، المملكة العربية السعودية',
                'setting_value_en' => 'Riyadh, Saudi Arabia',
                'setting_type' => 'string',
                'category' => 'general',
                'description' => 'عنوان الشركة',
                'description_en' => 'Company address',
                'is_public' => true,
            ],
            
            // إعدادات التقارير
            [
                'setting_key' => 'report_retention_days',
                'setting_value' => '365',
                'setting_value_en' => '365',
                'setting_type' => 'number',
                'category' => 'reports',
                'description' => 'عدد الأيام للاحتفاظ بالتقارير',
                'description_en' => 'Number of days to retain reports',
                'is_public' => false,
            ],
            [
                'setting_key' => 'enable_daily_reports',
                'setting_value' => '1',
                'setting_value_en' => '1',
                'setting_type' => 'boolean',
                'category' => 'reports',
                'description' => 'تفعيل التقارير اليومية التلقائية',
                'description_en' => 'Enable automatic daily reports',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['setting_key' => $setting['setting_key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
