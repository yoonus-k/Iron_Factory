<?php

/**
 * =============================================================================
 * نظام منع تكرار البيانات في تسجيل البضاعة
 * Duplicate Prevention System Configuration
 * =============================================================================
 *
 * هذا الملف يحتوي على إعدادات نظام منع التكرار في تسجيل البضاعة بالمستودع
 *
 * التاريخ: 17 نوفمبر 2025
 * الإصدار: 2.0
 */

return [
    /**
     * تفعيل/تعطيل نظام منع التكرار
     * Enable/Disable duplicate prevention system
     */
    'enabled' => env('DUPLICATE_PREVENTION_ENABLED', true),

    /**
     * طريقة توليد المفتاح الفريد
     * Unique key generation method
     * Options: 'hash', 'uuid', 'composite'
     */
    'key_method' => env('DUPLICATE_KEY_METHOD', 'hash'),

    /**
     * خوارزمية التجزئة
     * Hashing algorithm for key generation
     */
    'hash_algorithm' => 'md5', // 'md5' أو 'sha1'

    /**
     * حد أقصى لعدد محاولات التسجيل المسموحة
     * Maximum registration attempts allowed
     */
    'max_attempts' => env('MAX_REGISTRATION_ATTEMPTS', 5),

    /**
     * تحذير عند هذا العدد من المحاولات
     * Show warning at this attempt number
     */
    'warning_threshold' => 2,

    /**
     * رسائل النظام
     * System Messages
     */
    'messages' => [
        'duplicate_detected' => 'تم اكتشاف محاولة تسجيل سابقة لنفس الشحنة',
        'max_attempts_reached' => 'تم تجاوز الحد الأقصى لمحاولات التسجيل',
        'data_found' => 'تم العثور على بيانات مسجلة سابقاً',
        'registered_successfully' => 'تم تسجيل البضاعة بنجاح',
        'confirm_before_proceed' => 'الرجاء تأكيد البيانات قبل المتابعة',
    ],

    /**
     * الحقول المستخدمة في توليد المفتاح
     * Fields used for key generation
     */
    'key_fields' => [
        'note_number',      // رقم الشحنة
        'supplier_id',      // معرف المورد
        'created_at',       // تاريخ الإنشاء (اليوم)
    ],

    /**
     * الحقول المطلوبة للتسجيل
     * Required fields for registration
     */
    'required_fields' => [
        'actual_weight',     // الوزن الفعلي
        'material_type_id',  // نوع المادة
        'location',          // موقع التخزين
    ],

    /**
     * التحقق من الصحة
     * Validation Rules
     */
    'validation' => [
        'actual_weight' => 'required|numeric|min:0.01|max:100000',
        'material_type_id' => 'required|exists:material_types,id',
        'location' => 'required|string|max:100',
        'notes' => 'nullable|string|max:1000',
    ],

    /**
     * الألوان والرموز
     * Colors and Icons
     */
    'ui' => [
        'colors' => [
            'pending' => '#f39c12',      // أصفر - معلقة
            'registered' => '#27ae60',   // أخضر - مسجلة
            'warning' => '#e74c3c',      // أحمر - تحذير
            'info' => '#3498db',         // أزرق - معلومات
        ],
        'icons' => [
            'pending' => '⏳',
            'registered' => '✅',
            'warning' => '⚠️',
            'duplicate' => '📋',
            'success' => '✓',
        ],
    ],

    /**
     * إعدادات الإشعارات
     * Notification Settings
     */
    'notifications' => [
        'enabled' => true,
        'channels' => ['mail', 'database'],
        'alert_on_duplicate' => true,
        'alert_on_max_attempts' => true,
    ],

    /**
     * إعدادات التسجيل
     * Logging Settings
     */
    'logging' => [
        'enabled' => true,
        'log_duplicates' => true,
        'log_attempts' => true,
        'retention_days' => 90,
    ],

    /**
     * إعدادات الأداء
     * Performance Settings
     */
    'performance' => [
        'cache_enabled' => true,
        'cache_ttl' => 3600, // ساعة واحدة
        'batch_check' => true,
    ],
];
