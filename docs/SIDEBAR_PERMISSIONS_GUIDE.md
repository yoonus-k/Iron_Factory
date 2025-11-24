# 🔒 دليل الصلاحيات في القائمة الجانبية (Sidebar)

## 📌 ملخص التحديث

تم إضافة **صلاحيات شاملة** على جميع عناصر القائمة الجانبية بحيث يشوف كل دور فقط الخيارات المناسبة له.

---

## 🎯 الصلاحيات المستخدمة في Sidebar

| الخيار | الصلاحية | الدور |
|------|---------|------|
| **لوحة التحكم** | `VIEW_MAIN_DASHBOARD` | Admin, Manager, Supervisor, Worker |
| **المستودع** | `MANAGE_WAREHOUSES` | Admin, Manager, Warehouse Keeper |
| **المرحلة الأولى** | `STAGE1_STANDS` | Admin, Manager, Supervisor, Worker |
| **المرحلة الثانية** | `STAGE2_PROCESSING` | Admin, Manager, Supervisor, Worker |
| **المرحلة الثالثة** | `STAGE3_COILS` | Admin, Manager, Supervisor, Worker |
| **المرحلة الرابعة** | `STAGE4_PACKAGING` | Admin, Manager, Supervisor, Worker |
| **تتبع الإنتاج** | `MANAGE_MOVEMENTS` | Admin, Manager, Supervisor, Warehouse Keeper |
| **الورديات والعمال** | `MANAGE_MOVEMENTS` | Admin, Manager, Supervisor, Warehouse Keeper |
| **الهدر والجودة** | `VIEW_COSTS` | Admin, Manager, Supervisor |
| **التقارير** | `VIEW_REPORTS` | Admin, Manager, Supervisor |
| **الإدارة** | `MANAGE_USERS` | Admin, Manager |
| **الإعدادات** | Admin فقط | Admin |

---

## 📝 كود Blade Directives

تم إضافة 8 blade directives جديدة في `AppServiceProvider`:

### 1. **@canView**
يتحقق من صلاحية القراءة (عرض)
```blade
@canView('PERMISSION_CODE')
    {{-- محتوى يظهر للمستخدمين الذين لديهم صلاحية قراءة --}}
@endcanView
```

### 2. **@canCreate**
يتحقق من صلاحية الإنشاء
```blade
@canCreate('PERMISSION_CODE')
    {{-- زر أو حقل للإنشاء --}}
@endcanCreate
```

### 3. **@canUpdate**
يتحقق من صلاحية التعديل
```blade
@canUpdate('PERMISSION_CODE')
    {{-- زر أو حقل للتعديل --}}
@endcanUpdate
```

### 4. **@canDelete**
يتحقق من صلاحية الحذف
```blade
@canDelete('PERMISSION_CODE')
    {{-- زر الحذف --}}
@endcanDelete
```

### 5. **@canApprove**
يتحقق من صلاحية الموافقة
```blade
@canApprove('PERMISSION_CODE')
    {{-- زر الموافقة --}}
@endcanApprove
```

### 6. **@canExport**
يتحقق من صلاحية التصدير
```blade
@canExport('PERMISSION_CODE')
    {{-- زر التصدير --}}
@endcanExport
```

### 7. **@hasRole**
يتحقق من دور المستخدم
```blade
@hasRole('ADMIN')
    {{-- محتوى للإدمن فقط --}}
@endhasRole
```

### 8. **@isAdmin**
يتحقق إذا كان المستخدم أدمن
```blade
@isAdmin
    {{-- محتوى للإدمن فقط --}}
@endisAdmin
```

---

## 🔍 مثال من الـ Sidebar المحدث

```blade
<!-- المستودع - يظهر فقط لمن لديه صلاحية MANAGE_WAREHOUSES -->
@canView('MANAGE_WAREHOUSES')
<li class="has-submenu">
    <a href="javascript:void(0)" class="submenu-toggle">
        <i class="fas fa-warehouse"></i>
        <span>{{ __('app.menu.warehouse') }}</span>
        <i class="fas fa-chevron-down arrow"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="{{ route('manufacturing.warehouse-products.index') }}">
                <i class="fas fa-box"></i> المواد الخام
            </a>
        </li>
        <li>
            <a href="{{ route('manufacturing.warehouses.index') }}">
                <i class="fas fa-box"></i> المتاجر
            </a>
        </li>
    </ul>
</li>
@endcanView

<!-- المرحلة الأولى - يظهر فقط لمن لديه صلاحية STAGE1_STANDS -->
@canView('STAGE1_STANDS')
<li class="has-submenu">
    <a href="javascript:void(0)" class="submenu-toggle">
        <i class="fas fa-cut"></i>
        <span>المرحلة الأولى</span>
    </a>
    <!-- ... المحتوى ... -->
</li>
@endcanView
```

---

## 📊 مخطط توزيع الصلاحيات على الأدوار

```
┌─────────────────────────────────────────────────────────────┐
│                     Sidebar Permissions                     │
├─────────────────────────────────────────────────────────────┤
│ لوحة التحكم          │ ✅ Admin │ ✅ Manager │ ✅ Super │ ✅ Worker
├─────────────────────────────────────────────────────────────┤
│ المستودع             │ ✅ Admin │ ✅ Manager │ ❌ Super │ ❌ Worker
│ المرحلة الأولى        │ ✅ Admin │ ✅ Manager │ ✅ Super │ ✅ Worker
│ المرحلة الثانية       │ ✅ Admin │ ✅ Manager │ ✅ Super │ ✅ Worker
│ المرحلة الثالثة       │ ✅ Admin │ ✅ Manager │ ✅ Super │ ✅ Worker
│ المرحلة الرابعة       │ ✅ Admin │ ✅ Manager │ ✅ Super │ ✅ Worker
│ تتبع الإنتاج         │ ✅ Admin │ ✅ Manager │ ✅ Super │ ❌ Worker
│ الورديات والعمال     │ ✅ Admin │ ✅ Manager │ ✅ Super │ ❌ Worker
│ الهدر والجودة        │ ✅ Admin │ ✅ Manager │ ✅ Super │ ❌ Worker
│ التقارير             │ ✅ Admin │ ✅ Manager │ ✅ Super │ ❌ Worker
│ الإدارة              │ ✅ Admin │ ✅ Manager │ ❌ Super │ ❌ Worker
│ الإعدادات            │ ✅ Admin │ ❌ Manager │ ❌ Super │ ❌ Worker
└─────────────────────────────────────────────────────────────┘
```

---

## 🛠️ الملفات المحدثة

### 1. **resources/views/layout/sidebar.blade.php**
- إضافة `@canView` و `@canCreate` و `@canUpdate` على جميع الخيارات
- إضافة `@if(isAdmin())` للخيارات الخاصة بالـ Admin
- تجميع العناصر المرتبطة بصلاحيات واحدة

### 2. **app/Providers/AppServiceProvider.php**
- إضافة method `registerPermissionDirectives()`
- تسجيل 8 blade directives جديدة
- استخدام helper functions من `PermissionHelper.php`

---

## 🧪 اختبار الصلاحيات

### 1. تسجيل دخول بدور مختلف
```
Admin:      يشوف كل الخيارات ✅
Manager:    يشوف الأساسية + الإدارة ✅
Supervisor: يشوف الأساسية فقط ❌ الإعدادات
Worker:     يشوف لوحة التحكم فقط ❌ الباقي
```

### 2. التحقق من الـ HTML
افتح المتصفح وعاين الـ HTML - يجب أن لا تشوف العناصر الممنوعة مطلقاً

---

## 🔐 نقاط أمنية مهمة

⚠️ **تنبيه أمني**: الـ Blade directives توفر **حماية من الواجهة فقط**
- لا تخفي العناصر من الـ Admin فقط!
- يجب حماية الـ API و Routes أيضاً
- تأكد من التحقق من الصلاحيات في `Middleware` و `Controller`

### مثال حماية شاملة:
```php
// في Controller
public function index()
{
    // تحقق من الصلاحية
    if (!canRead('MANAGE_USERS')) {
        abort(403, 'Unauthorized');
    }
    // ... الباقي
}

// في Route
Route::get('/users', [UserController::class, 'index'])
    ->middleware('check.permission:MANAGE_USERS');
```

---

## 📚 أمثلة إضافية

### مثال 1: إخفاء زر بناءً على الصلاحية
```blade
@canDelete('MANAGE_USERS')
<button class="btn btn-danger" onclick="deleteUser()">
    <i class="fas fa-trash"></i> حذف المستخدم
</button>
@endcanDelete
```

### مثال 2: عرض رسالة بديلة
```blade
@canView('MANAGE_WAREHOUSES')
    <div class="card">
        <!-- محتوى المستودع -->
    </div>
@else
    <div class="alert alert-warning">
        <i class="fas fa-lock"></i> المستودع متاح للمديرين فقط
    </div>
@endcanView
```

### مثال 3: شروط متعددة
```blade
@if(isAdmin() || (canView('MANAGE_USERS') && canUpdate('MANAGE_USERS')))
    <div class="admin-panel">
        <!-- لوحة تحكم خاصة -->
    </div>
@endif
```

---

## ✅ قائمة التحقق

عند إضافة خيار جديد للـ Sidebar:

- [ ] أضفت `@canView('PERMISSION_CODE')`
- [ ] اخترت الصلاحية المناسبة
- [ ] اختبرت مع أدوار مختلفة
- [ ] حميت الـ Controller/Middleware
- [ ] وثقت الصلاحية الجديدة

---

## 🔄 تحديث الصلاحيات

إذا أضفت صلاحية جديدة:

1. **أضفها في Database**:
```bash
php artisan db:seed PermissionsSeeder
```

2. **أضفها في Sidebar**:
```blade
@canView('NEW_PERMISSION')
    <!-- محتوى جديد -->
@endcanView
```

3. **وثقها هنا**:
أضف سطر جديد في جدول الصلاحيات أعلاه

---

**آخر تحديث**: 2025-11-24
**الحالة**: ✅ تم التطبيق
