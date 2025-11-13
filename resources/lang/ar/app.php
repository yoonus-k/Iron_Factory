<?php

return [
    // القوائم الرئيسية
    'menu' => [
        'dashboard' => 'لوحة التحكم',
        'warehouse' => 'المستودع',
        'production' => 'الإنتاج',
        'production_tracking' => 'تتبع الإنتاج',
        'quality' => 'الجودة والهدر',
        'reports' => 'التقارير والإحصائيات',
        'shifts' => 'الورديات والعمال',
        'management' => 'الإدارة',
        'settings' => 'الإعدادات',
    ],

    // المستودع
    'warehouse' => [
        'add' => 'إضافة مستودع',
        'name' => 'اسم المستودع',
        'location' => 'الموقع',
        'capacity' => 'السعة',
        'current_stock' => 'المخزون الحالي',
        'material_type' => 'نوع المادة',
        'quantity' => 'الكمية',
        'unit' => 'الوحدة',
        'barcode' => 'الباركود',
        'remaining' => 'المتبقي',
        'raw_materials' => 'المواد الخام',
        'stores' => 'المستودع',
        'delivery_notes' => 'أذون التسليم',
        'purchase_invoices' => 'فواتير المشتريات',
        'suppliers' => 'الموردين',
        'additives' => 'الصبغات والبلاستيك',
    ],

    // مراحل الإنتاج
    'production' => [
        'barcode' => 'الباركود',
        'parent_barcode' => 'باركود المرحلة السابقة',
        'weight' => 'الوزن (كجم)',
        'color' => 'اللون',
        'wire_size' => 'مقاس السلك',
        'stand_number' => 'رقم الاستاند',
        'coil_number' => 'رقم الكويل',
        'box_count' => 'عدد الكراتين',
        'waste' => 'الهدر',
        'status' => 'الحالة',
        'date' => 'التاريخ',
        'barcode_scan' => 'مسح الباركود',
        'waste_tracking' => 'تتبع الهدر',
        'waste_statistics' => 'إحصائيات الهدر',
        'quality_monitoring' => 'مراقبة الجودة',
        'downtime_tracking' => 'الأعطال والتوقفات',
        'waste_limits' => 'حدود الهدر المسموحة',
        'iron_journey' => 'رحلة الحديد',
        
        // المرحلة 1
        'stage1' => [
            'title' => 'المرحلة 1: التقسيم والاستاندات',
            'list' => 'قائمة الاستاندات',
            'create_new' => 'إنشاء استاند جديد',
        ],
        
        // المرحلة 2
        'stage2' => [
            'title' => 'المرحلة 2: المعالجة',
            'list' => 'المواد قيد المعالجة',
            'start_new' => 'بدء معالجة جديدة',
            'complete' => 'إنهاء معالجة',
        ],
        
        // المرحلة 3
        'stage3' => [
            'title' => 'المرحلة 3: تصنيع الكويلات',
            'list' => 'قائمة الكويلات',
            'create_new' => 'إنشاء كويل جديد',
            'add_additives' => 'إضافة صبغة/بلاستيك',
            'completed' => 'كويلات مكتملة',
        ],
        
        // المرحلة 4
        'stage4' => [
            'title' => 'المرحلة 4: التعبئة والتغليف',
            'list' => 'الكراتين المعبأة',
            'create_new' => 'إنشاء كرتون جديد',
        ],
    ],

    // الألوان
    'colors' => [
        'red' => 'أحمر',
        'blue' => 'أزرق',
        'green' => 'أخضر',
        'yellow' => 'أصفر',
        'white' => 'أبيض',
        'black' => 'أسود',
    ],

    // الحالات
    'status' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'pending' => 'معلق',
        'in_progress' => 'قيد التنفيذ',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',
    ],

    // الأزرار
    'buttons' => [
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'view' => 'عرض',
        'add' => 'إضافة',
        'back' => 'رجوع',
        'next' => 'التالي',
        'previous' => 'السابق',
        'print' => 'طباعة',
        'export' => 'تصدير',
        'import' => 'استيراد',
        'search' => 'بحث',
        'filter' => 'تصفية',
        'reset' => 'إعادة تعيين',
        'login' => 'تسجيل الدخول',
    ],

    // الرسائل
    'messages' => [
        'success' => [
            'saved' => 'تم الحفظ بنجاح!',
            'updated' => 'تم التحديث بنجاح!',
            'deleted' => 'تم الحذف بنجاح!',
            'created' => 'تم الإنشاء بنجاح!',
        ],
        'error' => [
            'general' => 'حدث خطأ ما، يرجى المحاولة مرة أخرى',
            'not_found' => 'العنصر غير موجود',
            'unauthorized' => 'ليس لديك صلاحية للقيام بهذا الإجراء',
            'validation' => 'يرجى التحقق من البيانات المدخلة',
        ],
        'confirm' => [
            'delete' => 'هل أنت متأكد من الحذف؟',
            'cancel' => 'هل أنت متأكد من الإلغاء؟',
        ],
    ],

    // التقارير
    'reports' => [
        'daily_report' => 'التقرير اليومي',
        'weekly_report' => 'التقرير الأسبوعي',
        'monthly_report' => 'التقرير الشهري',
        'production_report' => 'تقرير الإنتاج',
        'waste_report' => 'تقرير الهدر',
        'efficiency_report' => 'تقرير الكفاءة',
        'from_date' => 'من تاريخ',
        'to_date' => 'إلى تاريخ',
        'generate' => 'إنشاء التقرير',
        'daily' => 'التقرير اليومي',
        'weekly' => 'التقرير الأسبوعي',
        'monthly' => 'التقرير الشهري',
        'production_stats' => 'إحصائيات الإنتاج',
        'waste_distribution' => 'توزيع الهدر',
    ],

    // المستخدمين
    'users' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'role' => 'الدور',
        'shift' => 'الوردية',
        'is_active' => 'نشط',
        'manage_users' => 'إدارة المستخدمين',
        'roles' => 'الأدوار والصلاحيات',
        'activity_log' => 'سجل الأنشطة',
        'shifts_list' => 'قائمة الورديات',
        'add_shift' => 'إضافة وردية جديدة',
        'current_shifts' => 'الورديات الحالية',
        'attendance' => 'سجل الحضور',
        'login' => 'تسجيل الدخول',
        'logout' => 'تسجيل الخروج',
        'register' => 'تسجيل جديد',
        'username_or_email' => 'اسم المستخدم أو البريد الإلكتروني',
        'enter_username_or_email' => 'أدخل اسم المستخدم أو البريد',
        'enter_password' => 'أدخل كلمة المرور',
        'remember_me' => 'تذكرني',
        'forgot_password' => 'نسيت كلمة المرور؟',
        'no_account' => 'ليس لديك حساب؟',
        'register_now' => 'سجل الآن',
        'role_types' => [
            'admin' => 'مدير',
            'manager' => 'مشرف',
            'supervisor' => 'مراقب',
            'worker' => 'عامل',
        ],
        'shift_types' => [
            'morning' => 'صباحي',
            'evening' => 'مسائي',
            'night' => 'ليلي',
        ],
    ],

    // الوحدات
    'units' => [
        'kg' => 'كجم',
        'ton' => 'طن',
        'piece' => 'قطعة',
        'box' => 'كرتون',
        'meter' => 'متر',
    ],

    // المزامنة
    'sync' => [
        'status' => 'حالة المزامنة',
        'online' => 'متصل',
        'offline' => 'غير متصل',
        'pending' => 'معلقة',
        'syncing' => 'قيد المزامنة',
        'synced' => 'مكتملة',
        'failed' => 'فاشلة',
        'sync_now' => 'مزامنة الآن',
        'last_sync' => 'آخر مزامنة',
    ],
    
    // الإعدادات
    'settings' => [
        'general' => 'إعدادات عامة',
        'calculations' => 'المعادلات والحسابات',
        'barcode_settings' => 'إعدادات الباركود',
        'notifications' => 'الإشعارات والتنبيهات',
    ],
];
