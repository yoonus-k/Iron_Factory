# نظام الصلاحيات - ملخص سريع 🚀

## ✅ ما تم إنجازه

### 1. Middleware للحماية
- ✅ `CheckPermission.php` - للتحقق من الصلاحيات
- ✅ `CheckRole.php` - للتحقق من الأدوار
- ✅ مسجلة في `bootstrap/app.php`

### 2. Helper Functions (دوال مساعدة)
- ✅ `hasPermission($code, $action)` - التحقق من صلاحية
- ✅ `canCreate()`, `canRead()`, `canUpdate()`, `canDelete()`, `canApprove()`, `canExport()`
- ✅ `hasRole()`, `hasAnyRole()`, `isAdmin()`
- ✅ `getRoleLevel()` - المستوى الهرمي
- ✅ مسجلة في `composer.json`

### 3. Controllers (الواجهات الإدارية)
- ✅ `RoleController.php` - إدارة الأدوار
- ✅ `PermissionController.php` - إدارة الصلاحيات

### 4. Views (الواجهات)
- ✅ `roles/index.blade.php` - قائمة الأدوار
- ✅ `roles/create.blade.php` - إضافة دور
- ✅ `roles/edit.blade.php` - تعديل دور
- ✅ `permissions/index.blade.php` - قائمة الصلاحيات
- ✅ `permissions/create.blade.php` - إضافة صلاحية
- ✅ `permissions/edit.blade.php` - تعديل صلاحية
- ✅ `test-permissions.blade.php` - صفحة اختبار

### 5. Seeders (البيانات الأولية)
- ✅ `PermissionsSeeder.php` - 27 صلاحية افتراضية
- ✅ `RolePermissionsSeeder.php` - ربط الصلاحيات بالأدوار

### 6. Routes (المسارات)
- ✅ `/roles` - إدارة الأدوار
- ✅ `/permissions` - إدارة الصلاحيات
- ✅ `/test-permissions` - اختبار النظام

---

## 🎯 كيفية الاستخدام السريعة

### في Blade Templates
```blade
{{-- إظهار/إخفاء حسب الصلاحية --}}
@if(canCreate('STAGE1_STANDS'))
    <button>إضافة</button>
@endif

@if(canDelete('STAGE1_STANDS'))
    <button>حذف</button>
@endif

@if(isAdmin())
    <a href="/roles">إدارة الأدوار</a>
@endif
```

### في Routes
```php
// حماية بالدور
Route::middleware(['role:ADMIN'])->group(function () {
    Route::resource('roles', RoleController::class);
});

// حماية بالصلاحية
Route::middleware(['permission:STAGE1_STANDS,create'])->group(function () {
    Route::post('/stage1/store', [Stage1Controller::class, 'store']);
});
```

### في Controllers
```php
public function __construct()
{
    $this->middleware('permission:STAGE1_STANDS,create')->only(['create', 'store']);
    $this->middleware('permission:STAGE1_STANDS,read')->only(['index', 'show']);
    $this->middleware('permission:STAGE1_STANDS,update')->only(['edit', 'update']);
    $this->middleware('permission:STAGE1_STANDS,delete')->only(['destroy']);
}
```

---

## 📊 الأدوار الافتراضية

| الدور | المستوى | الصلاحيات |
|-------|---------|-----------|
| **ADMIN** | 100 | كل شيء ✓ |
| **MANAGER** | 80 | كل شيء إلا الحذف |
| **SUPERVISOR** | 60 | الإنتاج + التقارير |
| **ACCOUNTANT** | 50 | الفواتير + التقارير |
| **WAREHOUSE_KEEPER** | 40 | المخازن + الحركات |
| **WORKER** | 20 | مراحل الإنتاج فقط |

---

## 🔑 الصلاحيات الرئيسية

### الإنتاج
- `STAGE1_STANDS` - المرحلة الأولى
- `STAGE2_PROCESSING` - المرحلة الثانية
- `STAGE3_COILS` - المرحلة الثالثة
- `STAGE4_PACKAGING` - المرحلة الرابعة

### الإدارة
- `MANAGE_USERS` - المستخدمين
- `MANAGE_ROLES` - الأدوار
- `MANAGE_PERMISSIONS` - الصلاحيات
- `MANAGE_MATERIALS` - المواد الخام
- `MANAGE_SUPPLIERS` - الموردين

### المخازن
- `MANAGE_WAREHOUSES` - إدارة المخازن
- `WAREHOUSE_TRANSFERS` - التحويلات
- `MANAGE_MOVEMENTS` - الحركات

### المالية
- `PURCHASE_INVOICES` - فواتير الشراء
- `SALES_INVOICES` - فواتير المبيعات

### التقارير
- `VIEW_REPORTS` - التقارير العامة
- `PRODUCTION_REPORTS` - تقارير الإنتاج
- `INVENTORY_REPORTS` - تقارير المخزون

---

## 🌐 الروابط المهمة

- **إدارة الأدوار:** [http://localhost:8000/roles](http://localhost:8000/roles)
- **إدارة الصلاحيات:** [http://localhost:8000/permissions](http://localhost:8000/permissions)
- **اختبار النظام:** [http://localhost:8000/test-permissions](http://localhost:8000/test-permissions)

---

## 🛠️ الأوامر المفيدة

```bash
# ربط الصلاحيات بالأدوار (تشغيل مرة واحدة)
php artisan db:seed --class=RolePermissionsSeeder

# مسح الكاش
php artisan cache:clear
php artisan config:clear
```

---

## 📝 مثال كامل: حماية المرحلة الأولى

### 1. في Routes
```php
// routes/web.php
Route::middleware(['auth', 'permission:STAGE1_STANDS,read'])->group(function () {
    Route::get('/stage1', [Stage1Controller::class, 'index'])->name('stage1.index');
});

Route::middleware(['auth', 'permission:STAGE1_STANDS,create'])->group(function () {
    Route::get('/stage1/create', [Stage1Controller::class, 'create'])->name('stage1.create');
    Route::post('/stage1', [Stage1Controller::class, 'store'])->name('stage1.store');
});
```

### 2. في Blade
```blade
{{-- stage1/index.blade.php --}}
<div class="mb-3">
    @if(canCreate('STAGE1_STANDS'))
        <a href="{{ route('stage1.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة جديد
        </a>
    @endif
    
    @if(canExport('STAGE1_STANDS'))
        <button onclick="exportData()" class="btn btn-success">
            <i class="fas fa-download"></i> تصدير
        </button>
    @endif
</div>

<table class="table">
    <tbody>
        @foreach($stands as $stand)
        <tr>
            <td>{{ $stand->barcode }}</td>
            <td>
                @if(canUpdate('STAGE1_STANDS'))
                    <a href="{{ route('stage1.edit', $stand) }}" class="btn btn-sm btn-primary">
                        تعديل
                    </a>
                @endif
                
                @if(canDelete('STAGE1_STANDS'))
                    <form action="{{ route('stage1.destroy', $stand) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                    </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
```

---

## ⚡ نصائح سريعة

1. **Admin له كل الصلاحيات دائماً** - لا حاجة لتعيين صلاحيات محددة
2. **استخدم الدوال المساعدة في Blade** - أسهل وأوضح
3. **استخدم Middleware في Routes** - حماية أقوى
4. **اختبر دائماً في `/test-permissions`** - قبل التطبيق الفعلي

---

## 📞 إذا واجهت مشكلة

1. تحقق من `/test-permissions` لمعرفة صلاحياتك
2. تأكد من تشغيل `RolePermissionsSeeder`
3. امسح الكاش: `php artisan cache:clear`
4. تأكد من أن المستخدم له دور محدد

---

**✨ جاهز للاستخدام! افتح `/test-permissions` للاختبار**
