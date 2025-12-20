<?php

return [

    /*
    |--------------------------------------------------------------------------
    | إعدادات السيرفر المركزي (Central Server)
    |--------------------------------------------------------------------------
    |
    | هذه الإعدادات للاتصال بالسيرفر المركزي الأونلاين
    | 
    */

    // URL السيرفر المركزي
    'central_server_url' => env('CENTRAL_SERVER_URL', 'https://your-central-server.com'),

    // API Token للمصادقة
    'central_server_token' => env('CENTRAL_SERVER_TOKEN', ''),

    // معرف السيرفر المحلي (لتمييز السيرفرات المختلفة)
    'local_server_id' => env('LOCAL_SERVER_ID', 'local-1'),

    // اسم السيرفر المحلي
    'local_server_name' => env('LOCAL_SERVER_NAME', 'المصنع - السيرفر المحلي 1'),

    // هل السيرفر المحلي يعمل كـ Central Server أيضاً؟
    'is_central_server' => env('IS_CENTRAL_SERVER', false),

    /*
    |--------------------------------------------------------------------------
    | إعدادات المزامنة
    |--------------------------------------------------------------------------
    */

    // تفعيل المزامنة التلقائية
    'auto_sync_enabled' => env('AUTO_SYNC_ENABLED', true),

    // فترة المزامنة التلقائية (بالدقائق)
    'auto_sync_interval' => env('AUTO_SYNC_INTERVAL', 1),

    // أقصى عدد للعمليات المعلقة في كل دفعة
    'batch_size' => env('SYNC_BATCH_SIZE', 100),

    // عدد محاولات إعادة المزامنة عند الفشل
    'max_retries' => env('SYNC_MAX_RETRIES', 3),

    // الوقت بين محاولات إعادة المزامنة (بالثواني)
    'retry_delay' => env('SYNC_RETRY_DELAY', 60),

    // مدة الاحتفاظ بسجلات المزامنة الناجحة (بالأيام)
    'logs_retention_days' => env('SYNC_LOGS_RETENTION', 30),

    /*
    |--------------------------------------------------------------------------
    | إعدادات الاتصال
    |--------------------------------------------------------------------------
    */

    // Timeout للاتصال بالسيرفر المركزي (بالثواني)
    'connection_timeout' => env('SYNC_CONNECTION_TIMEOUT', 30),

    // التحقق من SSL
    'verify_ssl' => env('SYNC_VERIFY_SSL', true),

    // استخدام Proxy
    'use_proxy' => env('SYNC_USE_PROXY', false),
    'proxy_url' => env('SYNC_PROXY_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | إعدادات الأمان
    |--------------------------------------------------------------------------
    */

    // تشفير البيانات قبل الإرسال
    'encrypt_data' => env('SYNC_ENCRYPT_DATA', true),

    // مفتاح التشفير
    'encryption_key' => env('SYNC_ENCRYPTION_KEY', env('APP_KEY')),

    // السماح فقط بالـ IPs المحددة
    'whitelist_ips' => env('SYNC_WHITELIST_IPS', ''),

    /*
    |--------------------------------------------------------------------------
    | إعدادات الأولويات
    |--------------------------------------------------------------------------
    |
    | أولويات أنواع الكيانات للمزامنة (كلما زاد الرقم زادت الأولوية)
    */

    'entity_priorities' => [
        'users' => 10,
        'materials' => 9,
        'delivery_notes' => 8,
        'purchase_invoices' => 7,
        'stage1_stands' => 6,
        'stage2_processed' => 5,
        'stage3_coils' => 4,
        'stage4_boxes' => 3,
        'workers' => 2,
        'default' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | الجداول المستثناة من المزامنة
    |--------------------------------------------------------------------------
    |
    | الجداول التي لا يجب مزامنتها مع السيرفر المركزي
    */

    'excluded_tables' => [
        'cache',
        'jobs',
        'failed_jobs',
        'migrations',
        'password_resets',
        'personal_access_tokens',
        'sessions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks
    |--------------------------------------------------------------------------
    |
    | URLs لإرسال إشعارات عند أحداث المزامنة
    */

    'webhooks' => [
        'on_sync_success' => env('WEBHOOK_SYNC_SUCCESS', ''),
        'on_sync_failure' => env('WEBHOOK_SYNC_FAILURE', ''),
    ],

];
