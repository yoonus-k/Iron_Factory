# نظام الصلاحيات والأدوار - دليل المستخدم

## نظرة عامة

تم تطبيق نظام صلاحيات متقدم يعتمد على **RBAC (Role-Based Access Control)** مع إمكانيات:
- **6 أدوار افتراضية** بمستويات هرمية (0-100)
- **27 صلاحية** موزعة على 9 وحدات
- **صلاحيات استثنائية** لكل مستخدم
- **تحكم دقيق** (إنشاء، قراءة، تعديل، حذف، موافقة، تصدير)

---

## الأدوار الافتراضية

| الدور | الكود | المستوى | الوصف |
|-------|------|---------|-------|
| **مدير النظام** | `ADMIN` | 100 | صلاحيات كاملة على جميع الوحدات |
| **المدير** | `MANAGER` | 80 | صلاحيات إدارية واسعة بدون حذف |
| **المشرف** | `SUPERVISOR` | 60 | إشراف على الإنتاج والعمليات |
| **المحاسب** | `ACCOUNTANT` | 50 | إدارة الفواتير والتقارير المالية |
| **أمين المخزن** | `WAREHOUSE_KEEPER` | 40 | إدارة المخازن والحركات |
| **العامل** | `WORKER` | 20 | تنفيذ مراحل الإنتاج فقط |

---

## الصلاحيات حسب الوحدة

### 1. المستخدمين (Users)
- `MANAGE_USERS` - إدارة المستخدمين والموظفين

### 2. الأدوار والصلاحيات (Roles & Permissions)
- `MANAGE_ROLES` - إدارة الأدوار
- `MANAGE_PERMISSIONS` - إدارة الصلاحيات

### 3. المواد الخام (Materials)
- `MANAGE_MATERIALS` - إدارة المواد الخام والمخزون

### 4. الموردين (Suppliers)
- `MANAGE_SUPPLIERS` - إدارة الموردين والموزعين

### 5. المخازن (Warehouses)
- `MANAGE_WAREHOUSES` - إدارة المخازن والمواقع
- `WAREHOUSE_TRANSFERS` - تحويلات المخازن

### 6. الإنتاج (Manufacturing)
- `STAGE1_STANDS` - المرحلة الأولى (الأستندات)
- `STAGE2_PROCESSING` - المرحلة الثانية (المعالجة)
- `STAGE3_COILS` - المرحلة الثالثة (اللفائف)
- `STAGE4_PACKAGING` - المرحلة الرابعة (التعبئة)

### 7. الفواتير (Invoices)
- `PURCHASE_INVOICES` - فواتير الشراء
- `SALES_INVOICES` - فواتير المبيعات

### 8. الحركات (Movements)
- `MANAGE_MOVEMENTS` - إدارة حركات المخزون

### 9. التقارير (Reports)
- `VIEW_REPORTS` - عرض التقارير
- `PRODUCTION_REPORTS` - تقارير الإنتاج
- `INVENTORY_REPORTS` - تقارير المخزون

### 10. لوحة التحكم (Dashboard)
- `VIEW_DASHBOARD` - عرض لوحة التحكم

---

## كيفية الاستخدام

### 1. الدوال المساعدة (Helper Functions)

#### التحقق من الصلاحيات
```php
// التحقق من صلاحية معينة
if (hasPermission('STAGE1_STANDS', 'create')) {
    // المستخدم يمكنه إنشاء في المرحلة الأولى
}

// اختصارات للصلاحيات
if (canCreate('STAGE1_STANDS')) { }
if (canRead('STAGE1_STANDS')) { }
if (canUpdate('STAGE1_STANDS')) { }
if (canDelete('STAGE1_STANDS')) { }
if (canApprove('STAGE1_STANDS')) { }
if (canExport('STAGE1_STANDS')) { }
```

#### التحقق من الدور
```php
// التحقق من دور واحد
if (hasRole('ADMIN')) {
    // المستخدم أدمن
}

// التحقق من عدة أدوار
if (hasAnyRole('ADMIN', 'MANAGER')) {
    // المستخدم أدمن أو مدير
}

// اختصار للأدمن
if (isAdmin()) {
    // المستخدم أدمن
}

// الحصول على مستوى الدور
$level = getRoleLevel(); // 0-100
```

### 2. استخدام في Blade

```blade
{{-- إظهار/إخفاء عناصر حسب الصلاحية --}}
@if(canCreate('STAGE1_STANDS'))
    <button>إضافة جديد</button>
@endif

@if(canDelete('STAGE1_STANDS'))
    <button>حذف</button>
@endif

{{-- التحقق من الدور --}}
@if(isAdmin())
    <a href="{{ route('roles.index') }}">إدارة الأدوار</a>
@endif
```

### 3. استخدام Middleware

#### في Routes
```php
// التحقق من الدور
Route::middleware(['role:ADMIN'])->group(function () {
    Route::resource('roles', RoleController::class);
});

Route::middleware(['role:ADMIN,MANAGER'])->group(function () {
    // يسمح للأدمن والمدير
});

// التحقق من الصلاحية والإجراء
Route::middleware(['permission:STAGE1_STANDS,create'])->group(function () {
    Route::post('/stage1/store', [Stage1Controller::class, 'store']);
});

Route::middleware(['permission:STAGE1_STANDS,read'])->group(function () {
    Route::get('/stage1', [Stage1Controller::class, 'index']);
});
```

#### في Controller
```php
public function __construct()
{
    // حماية كل الدوال
    $this->middleware('role:ADMIN');
    
    // أو حماية دوال معينة
    $this->middleware('permission:STAGE1_STANDS,create')->only(['create', 'store']);
    $this->middleware('permission:STAGE1_STANDS,read')->only(['index', 'show']);
    $this->middleware('permission:STAGE1_STANDS,update')->only(['edit', 'update']);
    $this->middleware('permission:STAGE1_STANDS,delete')->only(['destroy']);
}
```

### 4. استخدام في Controller Methods

```php
public function index()
{
    // التحقق اليدوي
    if (!auth()->user()->can('read', 'STAGE1_STANDS')) {
        abort(403, 'ليس لديك صلاحية الوصول');
    }
    
    // أو استخدام authorize
    $this->authorize('read', 'STAGE1_STANDS');
    
    // باقي الكود...
}
```

---

## الواجهات الإدارية

### 1. إدارة الأدوار
**المسار:** `/roles`

**الصلاحية المطلوبة:** `MANAGE_ROLES` (Admin فقط)

**الإمكانيات:**
- عرض جميع الأدوار
- إضافة دور جديد
- تعديل الأدوار (ما عدا أدوار النظام)
- حذف الأدوار (إذا لم تكن مرتبطة بمستخدمين)
- تعيين صلاحيات لكل دور مع تحديد دقيق (إنشاء/قراءة/تعديل/حذف/موافقة/تصدير)

### 2. إدارة الصلاحيات
**المسار:** `/permissions`

**الصلاحية المطلوبة:** `MANAGE_PERMISSIONS` (Admin فقط)

**الإمكانيات:**
- عرض جميع الصلاحيات
- تصفية حسب الوحدة
- إضافة صلاحية جديدة
- تعديل الصلاحيات (ما عدا صلاحيات النظام)
- حذف الصلاحيات (إذا لم تكن مرتبطة بأدوار)

### 3. صفحة اختبار الصلاحيات
**المسار:** `/test-permissions`

**تعرض:**
- معلومات المستخدم الحالي
- الدور والمستوى
- جميع الصلاحيات المتاحة
- اختبار الدوال المساعدة

---

## الملفات المهمة

### Middleware
- `app/Http/Middleware/CheckPermission.php` - التحقق من الصلاحيات
- `app/Http/Middleware/CheckRole.php` - التحقق من الأدوار

### Controllers
- `app/Http/Controllers/RoleController.php` - إدارة الأدوار
- `app/Http/Controllers/PermissionController.php` - إدارة الصلاحيات

### Models
- `app/Models/Role.php` - نموذج الأدوار
- `app/Models/Permission.php` - نموذج الصلاحيات
- `app/Models/UserPermission.php` - الصلاحيات الاستثنائية للمستخدمين
- `app/Models/User.php` - نموذج المستخدم (محدّث)

### Helper
- `app/Helpers/PermissionHelper.php` - الدوال المساعدة

### Seeders
- `database/seeders/PermissionsSeeder.php` - إنشاء الصلاحيات الافتراضية
- `database/seeders/RolePermissionsSeeder.php` - ربط الصلاحيات بالأدوار

### Views
- `resources/views/roles/` - واجهات إدارة الأدوار
- `resources/views/permissions/` - واجهات إدارة الصلاحيات
- `resources/views/test-permissions.blade.php` - صفحة الاختبار

---

## أمثلة عملية

### مثال 1: حماية صفحة المرحلة الأولى
```php
// في routes/web.php
Route::middleware(['permission:STAGE1_STANDS,read'])->group(function () {
    Route::get('/stage1', [Stage1Controller::class, 'index']);
    Route::get('/stage1/{id}', [Stage1Controller::class, 'show']);
});

Route::middleware(['permission:STAGE1_STANDS,create'])->group(function () {
    Route::get('/stage1/create', [Stage1Controller::class, 'create']);
    Route::post('/stage1', [Stage1Controller::class, 'store']);
});
```

### مثال 2: إخفاء أزرار حسب الصلاحية
```blade
{{-- في stage1/index.blade.php --}}
<div class="d-flex gap-2">
    @if(canCreate('STAGE1_STANDS'))
        <a href="{{ route('stage1.create') }}" class="btn btn-primary">
            إضافة جديد
        </a>
    @endif
    
    @if(canExport('STAGE1_STANDS'))
        <button onclick="exportData()" class="btn btn-success">
            تصدير Excel
        </button>
    @endif
</div>

{{-- في جدول البيانات --}}
@if(canUpdate('STAGE1_STANDS') || canDelete('STAGE1_STANDS'))
<td>
    @if(canUpdate('STAGE1_STANDS'))
        <a href="{{ route('stage1.edit', $item) }}" class="btn btn-sm btn-primary">تعديل</a>
    @endif
    
    @if(canDelete('STAGE1_STANDS'))
        <form action="{{ route('stage1.destroy', $item) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
        </form>
    @endif
</td>
@endif
```

### مثال 3: صلاحيات استثنائية لمستخدم معين
```php
// منح صلاحية استثنائية لمستخدم
use App\Models\UserPermission;

UserPermission::create([
    'user_id' => 5,
    'permission_name' => 'STAGE1_STANDS',
    'can_create' => true,
    'can_read' => true,
    'can_update' => true,
    'can_delete' => true, // صلاحية حذف خاصة
]);
```

---

## الأوامر المفيدة

```bash
# تشغيل seeder الصلاحيات
php artisan db:seed --class=PermissionsSeeder

# ربط الصلاحيات بالأدوار
php artisan db:seed --class=RolePermissionsSeeder

# مسح الكاش
php artisan cache:clear
php artisan config:clear
php artisan clear-compiled
```

---

## ملاحظات مهمة

1. **Admin له صلاحيات كاملة دائماً** - حتى بدون ربط صلاحيات محددة
2. **أدوار النظام محمية** - لا يمكن حذفها أو تعديل الكود الخاص بها
3. **الصلاحيات الاستثنائية لها أولوية** - تُفحص قبل صلاحيات الدور
4. **المستويات الهرمية** - كلما زاد المستوى زادت الصلاحيات
5. **التحقق من الصلاحيات تلقائياً** - عند استخدام Middleware

---

## استكشاف الأخطاء

### الخطأ: "ليس لديك صلاحية للوصول"
**الحل:** تحقق من:
1. هل المستخدم له دور؟
2. هل الدور له الصلاحية المطلوبة؟
3. هل الإجراء (create/read/update/delete) صحيح؟

### الخطأ: Helper functions لا تعمل
**الحل:** 
```bash
composer dump-autoload
php artisan config:clear
```

### الخطأ: Middleware لا يعمل
**الحل:** تأكد من تسجيل Middleware في `bootstrap/app.php`

---

## تطوير مستقبلي

- [ ] واجهة لإدارة الصلاحيات الاستثنائية للمستخدمين
- [ ] سجل تتبع التغييرات في الصلاحيات
- [ ] تصدير/استيراد الأدوار والصلاحيات
- [ ] قوالب أدوار جاهزة
- [ ] إشعارات عند تغيير الصلاحيات

---

تم إنشاؤه بواسطة: Iron Factory System  
التاريخ: 22 نوفمبر 2025
