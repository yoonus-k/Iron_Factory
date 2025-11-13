# 🔐 نظام الأدوار والصلاحيات - Iron Factory

## 📋 نظرة عامة

تم إنشاء نظام شامل للأدوار والصلاحيات مع **3 جداول جديدة** + تحديث جدول المستخدمين.

---

## 🗄️ الجداول المُنشأة

### 1️⃣ جدول الأدوار (roles)
**الملف:** `2024_01_01_000000_create_roles_table.php`

**الحقول:**
- `id` - المعرف
- `role_name` - اسم الدور بالعربية
- `role_name_en` - اسم الدور بالإنجليزية
- `role_code` - رمز الدور (فريد)
- `description` - وصف الدور
- `level` - مستوى الصلاحية (0-100)
- `is_system` - دور نظام (لا يمكن حذفه)
- `is_active` - حالة الدور
- `created_by` - من أنشأ الدور
- `timestamps` - التواريخ

**الأدوار الافتراضية:**
| الرمز | الاسم | المستوى | وصف |
|------|-------|---------|-----|
| ADMIN | مدير عام | 100 | صلاحيات كاملة |
| MANAGER | مدير إنتاج | 80 | إدارة الإنتاج والتقارير |
| SUPERVISOR | مشرف | 60 | مراقبة المراحل |
| WORKER | عامل | 20 | تنفيذ العمليات |
| WAREHOUSE_KEEPER | أمين مستودع | 40 | إدارة المستودعات |
| ACCOUNTANT | محاسب | 50 | إدارة الفواتير |

---

### 2️⃣ جدول الصلاحيات (permissions)
**الملف:** `2024_01_01_000001_create_permissions_table.php`

**الحقول:**
- `id` - المعرف
- `permission_name` - اسم الصلاحية بالعربية
- `permission_name_en` - اسم الصلاحية بالإنجليزية
- `permission_code` - رمز الصلاحية (فريد)
- `module` - القسم/الوحدة
- `description` - وصف الصلاحية
- `is_system` - صلاحية نظام
- `is_active` - حالة الصلاحية
- `created_by` - من أنشأ الصلاحية
- `timestamps` - التواريخ

**الوحدات (Modules):**
- `users` - المستخدمين
- `roles` - الأدوار
- `warehouses` - المستودعات
- `materials` - المواد
- `production` - الإنتاج
- `waste` - الهدر
- `shifts` - الورديات
- `reports` - التقارير
- `suppliers` - الموردين
- `accounting` - المحاسبة
- `settings` - الإعدادات

---

### 3️⃣ جدول الوسيط (role_permissions)
**الملف:** `2024_01_01_000002_create_role_permissions_table.php`

**الحقول:**
- `id` - المعرف
- `role_id` - رقم الدور
- `permission_id` - رقم الصلاحية
- `can_create` - يمكنه الإنشاء
- `can_read` - يمكنه القراءة
- `can_update` - يمكنه التعديل
- `can_delete` - يمكنه الحذف
- `can_approve` - يمكنه الموافقة
- `can_export` - يمكنه التصدير
- `created_by` - من أنشأ العلاقة
- `timestamps` - التواريخ

**القيود:**
- Unique constraint: كل دور يرتبط بصلاحية واحدة فقط
- Cascade delete: حذف العلاقات عند حذف الدور أو الصلاحية

---

### 4️⃣ تحديث جدول المستخدمين (users)
**الملف:** `2024_01_01_000003_create_users_table.php`

**الحقول المُضافة:**
- `role_id` - رقم الدور (Foreign Key → roles)
- `role` (enum) - للتوافقية القديمة

---

### 5️⃣ بيانات الصلاحيات الافتراضية
**الملف:** `2024_01_01_000029_seed_permissions_table.php`

**27 صلاحية** موزعة على الوحدات:

#### 👥 المستخدمين (2 صلاحية)
- `users.manage` - إدارة المستخدمين
- `users.view` - عرض المستخدمين

#### 🔐 الأدوار (2 صلاحية)
- `roles.manage` - إدارة الأدوار
- `permissions.assign` - تعيين الصلاحيات

#### 📦 المستودعات (3 صلاحيات)
- `warehouses.manage` - إدارة المستودعات
- `warehouses.view` - عرض المستودعات
- `warehouses.transactions` - حركات المستودع

#### 🏭 المواد (3 صلاحيات)
- `materials.manage` - إدارة المواد
- `materials.view` - عرض المواد
- `materials.receive` - استلام المواد

#### ⚙️ الإنتاج (5 صلاحيات)
- `production.stage1` - المرحلة الأولى
- `production.stage2` - المرحلة الثانية
- `production.stage3` - المرحلة الثالثة
- `production.stage4` - المرحلة الرابعة
- `production.view` - عرض الإنتاج

#### ♻️ الهدر (3 صلاحيات)
- `waste.report` - تسجيل الهدر
- `waste.approve` - الموافقة على الهدر
- `waste.view` - عرض تقارير الهدر

#### 🕐 الورديات (2 صلاحية)
- `shifts.manage` - إدارة الورديات
- `shifts.handover` - تسليم الوردية

#### 📊 التقارير (3 صلاحيات)
- `reports.view` - عرض التقارير
- `reports.export` - تصدير التقارير
- `reports.advanced` - التقارير المتقدمة

#### 💰 الموردين والمحاسبة (2 صلاحية)
- `suppliers.manage` - إدارة الموردين
- `invoices.manage` - إدارة الفواتير

#### ⚙️ الإعدادات (2 صلاحية)
- `settings.manage` - إعدادات النظام
- `formulas.manage` - إدارة المعادلات

---

## 🔄 الترتيب الصحيح للملفات

```
000000 - create_roles_table ⭐ جديد
000001 - create_permissions_table ⭐ جديد
000002 - create_role_permissions_table ⭐ جديد
000003 - create_users_table ⭐ (محدث - يحتوي على role_id)
000004 - create_user_permissions_table
000005 - create_shift_assignments_table
000006 - create_suppliers_table
000007 - create_purchase_invoices_table
... (بقية الجداول)
000029 - seed_permissions_table ⭐ جديد
```

---

## 💡 أمثلة على الاستخدام

### 1️⃣ إنشاء دور جديد
```php
$role = Role::create([
    'role_name' => 'مدير مبيعات',
    'role_name_en' => 'Sales Manager',
    'role_code' => 'SALES_MANAGER',
    'description' => 'إدارة المبيعات والعملاء',
    'level' => 70,
    'is_system' => false,
    'created_by' => auth()->id()
]);
```

### 2️⃣ تعيين صلاحيات للدور
```php
$role->permissions()->attach($permissionId, [
    'can_create' => true,
    'can_read' => true,
    'can_update' => true,
    'can_delete' => false,
    'can_approve' => true,
    'can_export' => true,
    'created_by' => auth()->id()
]);
```

### 3️⃣ إنشاء مستخدم مع دور
```php
$user = User::create([
    'name' => 'أحمد محمد',
    'email' => 'ahmed@example.com',
    'password' => bcrypt('password'),
    'role_id' => 4, // عامل
    'shift' => 'morning',
    'is_active' => true
]);
```

### 4️⃣ التحقق من الصلاحيات
```php
// هل المستخدم لديه صلاحية؟
if ($user->role->hasPermission('materials.manage', 'create')) {
    // يمكنه إنشاء مواد
}

// الحصول على جميع صلاحيات الدور
$permissions = $user->role->permissions()
    ->with('permission')
    ->get();
```

### 5️⃣ تصفية حسب المستوى
```php
// الأدوار التي مستواها أعلى من 50
$seniorRoles = Role::where('level', '>', 50)->get();

// التحقق من مستوى الدور
if ($user->role->level >= 80) {
    // دور إداري عالي
}
```

---

## 🎯 Eloquent Relationships المقترحة

### Model: Role
```php
class Role extends Model
{
    // علاقة مع المستخدمين
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    // علاقة مع الصلاحيات عبر الوسيط
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withPivot(['can_create', 'can_read', 'can_update', 'can_delete', 'can_approve', 'can_export'])
            ->withTimestamps();
    }
    
    // التحقق من صلاحية معينة
    public function hasPermission($permissionCode, $action = 'read')
    {
        return $this->permissions()
            ->where('permission_code', $permissionCode)
            ->wherePivot('can_' . $action, true)
            ->exists();
    }
}
```

### Model: Permission
```php
class Permission extends Model
{
    // علاقة مع الأدوار
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withPivot(['can_create', 'can_read', 'can_update', 'can_delete', 'can_approve', 'can_export'])
            ->withTimestamps();
    }
}
```

### Model: User
```php
class User extends Authenticatable
{
    // علاقة مع الدور
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    // التحقق من صلاحية المستخدم
    public function can($permissionCode, $action = 'read')
    {
        return $this->role && $this->role->hasPermission($permissionCode, $action);
    }
    
    // هل المستخدم أدمن؟
    public function isAdmin()
    {
        return $this->role && $this->role->role_code === 'ADMIN';
    }
}
```

---

## 🔒 Middleware مقترح

```php
// app/Http/Middleware/CheckPermission.php
class CheckPermission
{
    public function handle($request, Closure $next, $permission, $action = 'read')
    {
        if (!auth()->user()->can($permission, $action)) {
            abort(403, 'غير مصرح لك بهذه العملية');
        }
        
        return $next($request);
    }
}

// استخدام في Routes
Route::middleware(['auth', 'permission:materials.manage,create'])
    ->post('/materials', [MaterialController::class, 'store']);
```

---

## ✅ المميزات

1. ✅ **نظام مرن**: إضافة أدوار وصلاحيات جديدة بسهولة
2. ✅ **صلاحيات دقيقة**: 6 مستويات (create, read, update, delete, approve, export)
3. ✅ **مستويات هرمية**: level من 0-100
4. ✅ **حماية النظام**: أدوار وصلاحيات is_system
5. ✅ **تتبع كامل**: created_by في جميع الجداول
6. ✅ **توافقية**: حقل role القديم للتوافقية
7. ✅ **صلاحيات جاهزة**: 27 صلاحية موزعة على 11 وحدة

---

## 🚀 التشغيل

```bash
php artisan migrate
```

سيتم إنشاء:
- ✅ 6 أدوار افتراضية
- ✅ 27 صلاحية موزعة
- ✅ نظام كامل للتحكم بالصلاحيات

**جاهز للعمل! 🎉**
