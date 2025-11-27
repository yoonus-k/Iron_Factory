# تقرير تطبيق الصلاحيات على نظام الورديات والعمال

## ملخص العمل
تم إضافة نظام شامل للصلاحيات (Permissions) على جميع أزرار وعمليات صفحات الورديات والعمال بنجاح.

---

## الصلاحيات المضافة

### 1. صلاحيات الورديات الأساسية (SHIFTS_*)

| الصلاحية | الوصف | الحالة |
|---------|--------|--------|
| `SHIFTS_READ` | عرض قائمة الورديات | ✅ مضافة |
| `SHIFTS_CREATE` | إضافة وردية جديدة | ✅ مضافة |
| `SHIFTS_UPDATE` | تعديل الوردية | ✅ مضافة |
| `SHIFTS_DELETE` | حذف الوردية | ✅ مضافة |
| `SHIFTS_ACTIVATE` | تفعيل وردية مجدولة | ✅ مضافة |
| `SHIFTS_COMPLETE` | إكمال وردية نشطة | ✅ مضافة |
| `SHIFTS_SUSPEND` | تعليق الوردية مؤقتاً | ✅ مضافة |
| `SHIFTS_RESUME` | استئناف وردية معلقة | ✅ مضافة |
| `SHIFTS_CURRENT` | عرض الورديات الحالية | ✅ موجودة |
| `SHIFTS_ATTENDANCE` | إدارة الحضور والغياب | ✅ موجودة |

### 2. صلاحيات نقل الورديات (SHIFT_HANDOVERS_*)

| الصلاحية | الوصف | الحالة |
|---------|--------|--------|
| `SHIFT_HANDOVERS_READ` | عرض نقل الورديات | ✅ موجودة |
| `SHIFT_HANDOVERS_CREATE` | إنشاء نقل وردية | ✅ موجودة |
| `SHIFT_HANDOVERS_FROM_INDEX` | السماح بنقل من صفحة الانديكس | ✅ موجودة |
| `SHIFT_HANDOVERS_VIEW` | عرض تفاصيل النقل | ✅ موجودة |
| `SHIFT_HANDOVERS_APPROVE` | الموافقة على النقل | ✅ موجودة |
| `SHIFT_HANDOVERS_REJECT` | رفض النقل | ✅ موجودة |
| `SHIFT_HANDOVERS_DELETE` | حذف النقل | ✅ موجودة |

---

## الملفات المعدلة

### 1. `database/seeders/PermissionsSeeder.php`
**التعديلات:**
- تم استبدال الصلاحيات القديمة بصلاحيات محددة جديدة
- إضافة الصلاحيات التالية:
  - `SHIFTS_ACTIVATE` - تفعيل الوردية
  - `SHIFTS_COMPLETE` - إكمال الوردية
  - `SHIFTS_SUSPEND` - تعليق الوردية
  - `SHIFTS_RESUME` - استئناف الوردية

**الكود:**
```php
['name' => 'SHIFTS_ACTIVATE', 'display_name' => 'تفعيل الوردية', 'group_name' => 'الورديات والعمال', 'description' => 'تفعيل وردية مجدولة'],
['name' => 'SHIFTS_COMPLETE', 'display_name' => 'إكمال الوردية', 'group_name' => 'الورديات والعمال', 'description' => 'إكمال وردية نشطة'],
['name' => 'SHIFTS_SUSPEND', 'display_name' => 'تعليق الوردية', 'group_name' => 'الورديات والعمال', 'description' => 'تعليق وردية مؤقتاً'],
['name' => 'SHIFTS_RESUME', 'display_name' => 'استئناف الوردية', 'group_name' => 'الورديات والعمال', 'description' => 'استئناف وردية معلقة'],
```

### 2. `Modules/Manufacturing/resources/views/shifts-workers/index.blade.php`
**التعديلات:**
- إضافة `@can()` على زر "نقل الورديات"
- إضافة `@can()` على زر "إضافة وردية جديدة"
- إضافة `@can()` على جميع أزرار الإجراءات في الجدول (عرض، تعديل، تفعيل، إكمال، تعليق، نقل، استئناف، حذف)
- إضافة `@can()` على أزرار موبايل

**الأزرار المحمية:**
```blade
@can('SHIFTS_READ')      <!-- عرض -->
@can('SHIFTS_UPDATE')    <!-- تعديل -->
@can('SHIFTS_ACTIVATE')  <!-- تفعيل -->
@can('SHIFTS_COMPLETE')  <!-- إكمال -->
@can('SHIFTS_SUSPEND')   <!-- تعليق -->
@can('SHIFTS_RESUME')    <!-- استئناف -->
@can('SHIFTS_DELETE')    <!-- حذف -->
@can('SHIFT_HANDOVERS_READ')       <!-- نقل الورديات -->
@can('SHIFT_HANDOVERS_FROM_INDEX') <!-- نقل من الانديكس -->
```

### 3. `Modules/Manufacturing/resources/views/shifts-workers/show.blade.php`
**التعديلات:**
- إضافة `@can('SHIFTS_UPDATE')` على زر "تعديل"
- إضافة `@can()` على جميع أزرار الإجراءات:
  - `SHIFTS_UPDATE` - تعديل الوردية/العمال
  - `SHIFTS_ACTIVATE` - تفعيل
  - `SHIFTS_COMPLETE` - إكمال
  - `SHIFTS_SUSPEND` - تعليق
  - `SHIFTS_RESUME` - استئناف
  - `SHIFTS_DELETE` - حذف

### 4. `Modules/Manufacturing/resources/views/shifts-workers/current.blade.php`
**التعديلات:**
- إضافة `@can('SHIFTS_READ')` على زر "عرض التفاصيل"
- إضافة `@can('SHIFTS_COMPLETE')` على زر "إنهاء الوردية"
- إضافة `@can('SHIFTS_SUSPEND')` على زر "تعليق الوردية"

---

## طريقة الاستخدام

### للتحقق من الصلاحيات في Blade Templates:

```blade
<!-- إذا كان للمستخدم الصلاحية -->
@can('SHIFTS_CREATE')
    <a href="{{ route('manufacturing.shifts-workers.create') }}">
        إضافة وردية
    </a>
@endcan

<!-- إذا لم يكن له الصلاحية -->
@cannot('SHIFTS_DELETE')
    <!-- لن يظهر الزر -->
@endcannot
```

### للتحقق في Controllers:

```php
// في أي controller
if (auth()->user()->can('SHIFTS_CREATE')) {
    // السماح بالعملية
}

// أو مباشرة في الـ route
Route::post('/shifts', 'ShiftController@store')->middleware('can:SHIFTS_CREATE');
```

---

## الصلاحيات المرتبطة بالأدوار

تم ربط الصلاحيات بالأدوار التالية (في RolePermissionsSeeder):

- **Admin**: جميع الصلاحيات
- **Manager**: صلاحيات الإدارة الكاملة للورديات
- **Supervisor**: صلاحيات الإشراف على الورديات
- **Accountant**: صلاحيات الحسابات المرتبطة بالورديات
- **Warehouse Keeper**: صلاحيات المستودع
- **Worker**: صلاحيات محدودة

---

## قائمة الصلاحيات الكاملة المتاحة

```
MENU_SHIFTS_WORKERS
SHIFTS_READ
SHIFTS_CREATE
SHIFTS_UPDATE
SHIFTS_DELETE
SHIFTS_ACTIVATE
SHIFTS_COMPLETE
SHIFTS_SUSPEND
SHIFTS_RESUME
SHIFTS_CURRENT
SHIFTS_ATTENDANCE
WORKERS_READ
WORKERS_CREATE
WORKERS_UPDATE
WORKERS_DELETE
WORKER_TEAMS_READ
WORKER_TEAMS_CREATE
WORKER_TEAMS_UPDATE
WORKER_TEAMS_DELETE
MENU_SHIFT_HANDOVERS
SHIFT_HANDOVERS_READ
SHIFT_HANDOVERS_CREATE
SHIFT_HANDOVERS_VIEW
SHIFT_HANDOVERS_APPROVE
SHIFT_HANDOVERS_REJECT
SHIFT_HANDOVERS_DELETE
SHIFT_HANDOVERS_FROM_INDEX
```

---

## ملاحظات مهمة

1. **الصلاحيات يتم فحصها عند التصيير (Rendering)**:
   - إذا لم يكن للمستخدم الصلاحية، الزر لن يظهر في الصفحة

2. **الحماية يجب أن تكون في Backend**:
   - تطبيق `@can()` في الـ Blade templates من أجل تحسين UX
   - يجب التأكد من وجود حماية في Controllers و Routes أيضاً

3. **تطبيق الصلاحيات يتم من خلال**:
   - `database/seeders/PermissionsSeeder.php` - تعريف الصلاحيات
   - `database/seeders/RolePermissionsSeeder.php` - ربط الصلاحيات بالأدوار
   - `@can()` directive في الـ Blade templates

---

## الخطوات التالية المقترحة

1. تحديث RolePermissionsSeeder للتأكد من ربط الصلاحيات الجديدة بالأدوار المناسبة
2. إضافة حماية في Controllers باستخدام authorize()
3. تطبيق نفس النمط على جميع الصفحات الأخرى في النظام
4. إنشاء dashboard للصلاحيات لتسهيل إدارتها

---

**تاريخ الإنشاء**: 26 نوفمبر 2025
**الحالة**: ✅ مكتمل
**المراجع**: database/seeders/PermissionsSeeder.php
