# نظام الصلاحيات - نقل الورديات

## نظرة عامة
تم تطبيق نظام شامل للصلاحيات على عمليات نقل الورديات يشمل:
- ✅ صلاحيات على مستوى قاعدة البيانات
- ✅ فحوصات الصلاحيات على مستوى الـ Routes
- ✅ فحوصات الصلاحيات على مستوى الـ Controller
- ✅ فحوصات الصلاحيات على مستوى الـ Views (Blade directives)

---

## 1. قائمة الصلاحيات المضافة

### الصلاحيات المتاحة:

| الرمز | الوصف بالعربية | النوع |
|------|---------------|------|
| `MENU_SHIFT_HANDOVERS` | ظهور قائمة نقل الورديات | قائمة |
| `SHIFT_HANDOVERS_READ` | عرض قائمة النقلات | عرض |
| `SHIFT_HANDOVERS_CREATE` | إنشاء نقل وردية جديد | إنشاء |
| `SHIFT_HANDOVERS_VIEW` | عرض تفاصيل النقل | عرض |
| `SHIFT_HANDOVERS_APPROVE` | الموافقة على النقل | موافقة |
| `SHIFT_HANDOVERS_REJECT` | رفض النقل | رفض |
| `SHIFT_HANDOVERS_DELETE` | حذف النقل | حذف |
| `SHIFT_HANDOVERS_FROM_INDEX` | إجراء نقل من صفحة الورديات | نقل سريع |

**ملاحظة**: جميع الصلاحيات موجودة في قاعدة البيانات تحت المجموعة `نقل الورديات`

---

## 2. ملفات النظام

### ملف الصلاحيات:
- **المسار**: `database/seeders/PermissionsSeeder.php`
- **الدور**: تعريف جميع الصلاحيات في قاعدة البيانات
- **التعديل**: تمت إضافة 8 صلاحيات جديدة (سطور 183-195)

### ملفات الفحص:
- **المسار**: `app/Checks/ShiftHandoverPermissionsCheck.php`
- **الدور**: فحص الصلاحيات على مستوى التطبيق
- **الدوال المتاحة**:
  ```php
  canViewShiftHandovers()      // عرض القائمة
  canCreateHandover()           // إنشاء نقل
  canViewHandoverDetails()      // عرض التفاصيل
  canApproveHandover()          // الموافقة
  canRejectHandover()           // الرفض
  canDeleteHandover()           // الحذف
  canHandoverFromIndex()        // النقل من الـ Index
  canManageHandovers()          // أي صلاحية في الإدارة
  ```

- **المسار**: `app/Checks/ShiftsPermissionsCheck.php`
- **الدور**: فحص صلاحيات الورديات العامة

- **المسار**: `app/Checks/ShiftWorkersPermissionsCheck.php`
- **الدور**: فحص صلاحيات عمال الورديات

### ملف الـ Middleware:
- **المسار**: `app/Http/Middleware/CheckPermission.php`
- **الدور**: التحقق من الصلاحيات على مستوى الـ Routes
- **الاستخدام**: `:permission:PERMISSION_CODE`
- **المسجل في**: `bootstrap/app.php` كـ alias `permission`

---

## 3. تطبيق الصلاحيات

### أ) على مستوى الـ Routes

**الملف**: `Modules/Manufacturing/routes/shifts-workers.php`

```php
// مثال: نقل الورديات
Route::get('shift-handovers/generate-code', [ShiftHandoverController::class, 'generateHandoverCode'])
    ->middleware('permission:SHIFT_HANDOVERS_READ')
    ->name('manufacturing.shift-handovers.generate-code');

Route::post('shift-handovers/{id}/approve', [ShiftHandoverController::class, 'approve'])
    ->middleware('permission:SHIFT_HANDOVERS_APPROVE')
    ->name('manufacturing.shift-handovers.approve');

Route::post('shift-handovers/{id}/reject', [ShiftHandoverController::class, 'reject'])
    ->middleware('permission:SHIFT_HANDOVERS_REJECT')
    ->name('manufacturing.shift-handovers.reject');

Route::resource('shift-handovers', ShiftHandoverController::class)
    ->middleware('permission:SHIFT_HANDOVERS_READ')
    ->names('manufacturing.shift-handovers');
```

**جميع الـ Routes المحدثة**:
- ✅ shift-handovers (جميع العمليات)
- ✅ shifts-workers (الورديات والنقل)
- ✅ workers (العمال)
- ✅ worker-teams (فرق العمل)

### ب) على مستوى الـ Controller

**الملف**: `Modules/Manufacturing/Http/Controllers/ShiftHandoverController.php`

```php
public function store(Request $request)
{
    // التحقق من الصلاحية
    if (!ShiftHandoverPermissionsCheck::canCreateHandover()) {
        abort(403, 'ليس لديك صلاحية لإنشاء نقل وردية');
    }
    // ... باقي الكود
}

public function approve(Request $request, $id)
{
    // التحقق من الصلاحية
    if (!ShiftHandoverPermissionsCheck::canApproveHandover()) {
        abort(403, 'ليس لديك صلاحية للموافقة على نقل الوردية');
    }
    // ... باقي الكود
}

public function reject(Request $request, $id)
{
    // التحقق من الصلاحية
    if (!ShiftHandoverPermissionsCheck::canRejectHandover()) {
        abort(403, 'ليس لديك صلاحية لرفض نقل الوردية');
    }
    // ... باقي الكود
}
```

### ج) على مستوى الـ Views (Blade)

#### 1. صفحة القائمة (`shift-handovers/index.blade.php`)

```blade
@if(!$handover->supervisor_approved)
    @can('SHIFT_HANDOVERS_APPROVE')
    <form method="POST" action="{{ route('manufacturing.shift-handovers.approve', $handover->id) }}">
        @csrf
        <button type="submit" class="um-dropdown-item um-btn-feature">
            <i class="feather icon-check"></i>
            <span>موافقة</span>
        </button>
    </form>
    @endcan

    @can('SHIFT_HANDOVERS_REJECT')
    <button type="button" class="um-dropdown-item um-btn-delete" onclick="openRejectModal({{ $handover->id }})">
        <i class="feather icon-x"></i>
        <span>رفض</span>
    </button>
    @endcan
@endif
```

#### 2. صفحة التفاصيل (`shift-handovers/show.blade.php`)

```blade
@if(!$handover->supervisor_approved)
    <div class="card-body">
        <div class="actions-grid">
            @can('SHIFT_HANDOVERS_APPROVE')
            <form action="{{ route('manufacturing.shift-handovers.approve', $handover->id) }}" method="POST">
                @csrf
                <button type="submit" class="action-btn activate" style="width: 100%;">
                    <div class="action-icon">
                        <svg>...</svg>
                    </div>
                    <div class="action-text">
                        <h4>الموافقة</h4>
                        <p>الموافقة على نقل الوردية</p>
                    </div>
                </button>
            </form>
            @endcan

            @can('SHIFT_HANDOVERS_REJECT')
            <button type="button" class="action-btn delete" onclick="openRejectModal()">
                <div class="action-icon">
                    <svg>...</svg>
                </div>
                <div class="action-text">
                    <h4>الرفض</h4>
                    <p>رفض النقل مع توضيح السبب</p>
                </div>
            </button>
            @endcan
        </div>
    </div>
@endif
```

#### 3. صفحة الورديات (`shifts-workers/index.blade.php`)

```blade
@can('SHIFT_HANDOVERS_FROM_INDEX')
<button type="button" class="um-dropdown-item um-btn-info" onclick="openHandoverModal({{ $shift->id }}, '{{ $shift->shift_code }}')">
    <i class="feather icon-exchange-2"></i>
    <span>نقل الوردية</span>
</button>
@endcan
```

---

## 4. خطوات التفعيل

### الخطوة 1: تشغيل المهجرات والبذور

```bash
# تشغيل المهجرات
php artisan migrate

# تشغيل البذور (تسجيل الصلاحيات)
php artisan db:seed --class=PermissionsSeeder
```

### الخطوة 2: إسناد الصلاحيات للأدوار

ادخل إلى لوحة التحكم:
1. اذهب إلى **الإعدادات** → **الأدوار والصلاحيات**
2. اختر الدور (مثل: مشرف، قائد فريق)
3. اختر الصلاحيات المطلوبة:
   - ✅ SHIFT_HANDOVERS_READ
   - ✅ SHIFT_HANDOVERS_CREATE
   - ✅ SHIFT_HANDOVERS_APPROVE
   - ✅ SHIFT_HANDOVERS_REJECT
4. احفظ التغييرات

### الخطوة 3: اختبار الصلاحيات

#### اختبار على مستوى الـ Route:
```bash
# حاول الوصول إلى الصفحة بدون صلاحية
# يجب أن ترى خطأ 403
```

#### اختبار على مستوى الـ View:
1. سجل دخول بمستخدم بدون الصلاحيات
2. اذهب إلى صفحة نقل الورديات
3. يجب ألا ترى أزرار الموافقة والرفض

---

## 5. خريطة الصلاحيات

### من يمكنه ماذا؟

#### المسؤول العام (Admin)
- ✅ عرض جميع النقلات
- ✅ إنشاء نقل جديد
- ✅ الموافقة على النقلات
- ✅ رفض النقلات
- ✅ حذف النقلات
- ✅ نقل من الـ Index

#### المشرف (Supervisor)
- ✅ عرض جميع النقلات
- ✅ إنشاء نقل جديد
- ✅ الموافقة على النقلات
- ✅ رفض النقلات
- ❌ حذف النقلات
- ✅ نقل من الـ Index

#### قائد الفريق (Team Lead)
- ✅ عرض جميع النقلات
- ✅ إنشاء نقل جديد
- ✅ الموافقة على النقلات
- ❌ رفض النقلات
- ❌ حذف النقلات
- ✅ نقل من الـ Index

#### العامل (Worker)
- ✅ عرض جميع النقلات
- ✅ إنشاء نقل جديد (خاص به فقط)
- ❌ الموافقة على النقلات
- ❌ رفض النقلات
- ❌ حذف النقلات
- ✅ نقل من الـ Index (خاص به فقط)

---

## 6. الأخطاء الشائعة والحلول

### خطأ: "ليس لديك صلاحية للوصول"

**السبب**: المستخدم لا يملك الصلاحية المطلوبة

**الحل**:
1. تحقق من أن المستخدم مسند إليه دور معين
2. تحقق من أن الدور يملك الصلاحية المطلوبة
3. امسح ذاكرة التخزين المؤقتة:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

### خطأ: الصلاحية لا تظهر في قائمة الأدوار

**السبب**: لم يتم تشغيل البذر

**الحل**:
```bash
php artisan db:seed --class=PermissionsSeeder
```

### الأزرار مخفية ولكن يمكن الوصول عبر URL

**السبب**: لم تتم إضافة middleware على الـ Route

**الحل**: تأكد من وجود `.middleware('permission:PERMISSION_CODE')` على الـ Route

---

## 7. ملفات معدلة

### قائمة الملفات التي تم تعديلها:

1. **database/seeders/PermissionsSeeder.php**
   - ✅ إضافة 8 صلاحيات جديدة

2. **app/Checks/ShiftHandoverPermissionsCheck.php**
   - ✅ إنشاء ملف جديد بـ 8 طرق فحص

3. **app/Checks/ShiftsPermissionsCheck.php**
   - ✅ إنشاء ملف جديد بـ 11 طريقة فحص

4. **app/Checks/ShiftWorkersPermissionsCheck.php**
   - ✅ إنشاء ملف جديد بـ 11 طريقة فحص

5. **Modules/Manufacturing/routes/shifts-workers.php**
   - ✅ إضافة middleware على جميع الـ Routes

6. **Modules/Manufacturing/Http/Controllers/ShiftHandoverController.php**
   - ✅ إضافة فحوصات الصلاحيات في store, approve, reject

7. **Modules/Manufacturing/resources/views/shift-handovers/index.blade.php**
   - ✅ إضافة @can directives لأزرار الموافقة والرفض

8. **Modules/Manufacturing/resources/views/shift-handovers/show.blade.php**
   - ✅ إضافة @can directives لأزرار الإجراءات

9. **Modules/Manufacturing/resources/views/shifts-workers/index.blade.php**
   - ✅ إضافة @can directive لزر النقل من الـ Index

10. **bootstrap/app.php**
    - ✅ Middleware مسجل بالفعل (لا تعديلات مطلوبة)

---

## 8. الخطوات التالية (اختيارية)

### للتحسينات المستقبلية:

1. **إضافة سجلات التدقيق**:
   ```php
   Log::info('User approved handover', ['user_id' => Auth::id(), 'handover_id' => $id]);
   ```

2. **تقارير الصلاحيات**:
   - إنشاء صفحة تقارير توضح استخدام الصلاحيات

3. **تحديث الصلاحيات تلقائياً**:
   - إضافة أوامر Artisan لتحديث الصلاحيات

4. **اختبارات الصلاحيات**:
   - كتابة اختبارات PHPUnit للتحقق من الصلاحيات

---

## 9. الدعم والمساعدة

### للأسئلة والاستفسارات:

1. **التحقق من الصلاحيات في قاعدة البيانات**:
   ```bash
   php artisan tinker
   >>> App\Models\Permission::where('group_name', 'نقل الورديات')->get();
   ```

2. **التحقق من صلاحيات المستخدم**:
   ```bash
   php artisan tinker
   >>> $user = App\Models\User::find(1);
   >>> $user->hasPermission('SHIFT_HANDOVERS_APPROVE');
   ```

3. **إعادة تعيين الصلاحيات**:
   ```bash
   php artisan migrate:fresh --seed
   ```

---

**آخر تحديث**: 2024
**الحالة**: ✅ مكتمل وجاهز للإنتاج
