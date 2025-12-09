<?php

return [
    // Page Titles
    'title.create' => 'إضافة استاند جديد',
    'title.edit' => 'تعديل الاستاند',
    'title.index' => 'إدارة الاستاندات',
    'title.show' => 'تفاصيل الاستاند',
    'title.usage_history' => 'تاريخ استخدام الاستاندات',

    // Breadcrumb
    'breadcrumb.dashboard' => 'لوحة التحكم',
    'breadcrumb.stands' => 'الاستاندات',
    'breadcrumb.add_new' => 'إضافة جديد',
    'breadcrumb.edit' => 'تعديل',

    // Headers
    'header.add_stand' => 'إضافة استاند جديد',
    'header.edit_stand' => 'تعديل الاستاند',
    'header.manage_stands' => 'إدارة الاستاندات',
    'header.stand_details' => 'تفاصيل الاستاند',
    'header.usage_history' => 'تاريخ استخدام الاستاندات',

    // Form Labels
    'form.stand_number' => 'رقم الاستاند',
    'form.weight' => 'الوزن (كجم)',
    'form.notes' => 'ملاحظات',
    'form.status' => 'المرحلة الحالية',
    'form.is_active' => 'الحالة',
    'form.created_at' => 'تاريخ الإنشاء',
    'form.updated_at' => 'تاريخ آخر تحديث',
    'form.last_used_at' => 'آخر استخدام',

    // Form Placeholders
    'placeholder.stand_number' => 'أدخل رقم الاستاند',
    'placeholder.weight' => 'أدخل وزن الاستاند بالكيلوجرام',
    'placeholder.notes' => 'أضف أي ملاحظات إضافية حول الاستاند...',
    'placeholder.search' => 'البحث برقم الاستاند...',

    // Form Help Text
    'help.stand_number' => 'أدخل رقم فريد للاستاند',
    'help.stand_number_readonly' => 'رقم الاستاند لا يمكن تعديله',
    'help.notes' => 'الحد الأقصى 1000 حرف',
    'help.default_status' => 'سيتم إنشاء الاستاند بحالة غير مستخدم بشكل افتراضي، ويمكنك تحديث الحالة لاحقاً',

    // Status Options
    'status.unused' => 'غير مستخدم',
    'status.stage1' => 'المرحلة الأولى',
    'status.stage2' => 'المرحلة الثانية',
    'status.stage3' => 'المرحلة الثالثة',
    'status.stage4' => 'المرحلة الرابعة',
    'status.completed' => 'مكتمل',
    'status.in_use' => 'قيد الاستخدام',
    'status.returned' => 'مُرجع',

    // Active/Inactive
    'active' => 'نشط',
    'inactive' => 'غير نشط',

    // Card Sections
    'card.basic_info' => 'المعلومات الأساسية',
    'card.dates' => 'التواريخ',
    'card.notes' => 'الملاحظات',
    'card.stand_data' => 'بيانات الاستاند',
    'card.edit_data' => 'تعديل بيانات الاستاند',
    'card.stands_list' => 'قائمة الاستاندات',

    // Buttons
    'btn.add_new' => 'إضافة استاند جديد',
    'btn.save' => 'حفظ الاستاند',
    'btn.save_changes' => 'حفظ التعديلات',
    'btn.cancel' => 'إلغاء',
    'btn.back' => 'رجوع',
    'btn.edit' => 'تعديل',
    'btn.view' => 'عرض',
    'btn.delete' => 'حذف الاستاند',
    'btn.enable' => 'تفعيل الاستاند',
    'btn.disable' => 'تعطيل الاستاند',
    'btn.search' => 'بحث',
    'btn.reset' => 'إعادة تعيين',
    'btn.usage_history' => 'تاريخ الاستخدام',

    // Alert Messages
    'alert.validation_error' => 'يوجد أخطاء في البيانات المدخلة:',
    'alert.success' => 'تم بنجاح',
    'alert.error' => 'حدث خطأ',
    'alert.note' => 'ملاحظة:',
    'alert.confirm_delete' => 'هل أنت متأكد من حذف هذا الاستاند؟',
    'alert.confirm_delete_warning' => 'هذا الإجراء لا يمكن التراجع عنه!',

    // Filter Options
    'filter.all_statuses' => 'جميع الحالات',
    'filter.date' => 'التاريخ',

    // List Messages
    'message.no_stands' => 'لا توجد استاندات بعد. ابدأ بإضافة استاند جديد!',
    'message.no_stands_mobile' => 'لا توجد استاندات بعد',

    // Info Labels
    'info.stand_info' => 'معلومات الاستاند',
    'info.dates_section' => 'التواريخ',
    'info.weight_unit' => 'كجم',
    'info.showing' => 'عرض',
    'info.to' => 'إلى',
    'info.of' => 'من أصل',
    'info.stand' => 'استاند',

    // Statistics
    'stats.total_stands' => 'إجمالي الاستاندات',
    'stats.active_stands' => 'الاستاندات النشطة',
    'stats.inactive_stands' => 'الاستاندات غير النشطة',
    'stats.completed_stands' => 'الاستاندات المكتملة',
    'stats.in_use' => 'قيد الاستخدام',
    'stats.total_uses' => 'إجمالي الاستخدام',

    // Validation Messages
    'validation.stand_number_required' => 'رقم الاستاند مطلوب',
    'validation.stand_number_unique' => 'رقم الاستاند موجود بالفعل',
    'validation.weight_required' => 'الوزن مطلوب',
    'validation.weight_numeric' => 'الوزن يجب أن يكون رقم صحيح',
    'validation.weight_min' => 'الوزن يجب أن يكون أكبر من صفر',
];
