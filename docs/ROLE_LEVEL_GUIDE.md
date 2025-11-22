# المستوى (Level) في نظام الأدوار - الدليل الشامل

## 🎯 ما هو المستوى؟

**المستوى (Level)** هو رقم من **0 إلى 100** يحدد القوة والسلطة الهرمية للدور في النظام.

---

## 📊 المستويات الافتراضية

| الدور | المستوى | الوصف |
|------|---------|-------|
| **Admin** | 100 | أعلى سلطة - يتحكم في كل شيء |
| **Manager** | 80 | إدارة عليا - صلاحيات واسعة |
| **Supervisor** | 60 | مشرف - إشراف على العمليات |
| **Accountant** | 50 | محاسب - إدارة مالية |
| **Warehouse Keeper** | 40 | أمين مخزن - إدارة مخازن |
| **Worker** | 20 | عامل - تنفيذ فقط |

---

## 💡 تأثير المستوى

### 1. **الترتيب الهرمي**
المستوى يحدد من أعلى ومن أقل في السلم الإداري:

```
100 ━━━ Admin (الأعلى)
 80 ━━━ Manager
 60 ━━━ Supervisor
 50 ━━━ Accountant
 40 ━━━ Warehouse Keeper
 20 ━━━ Worker (الأقل)
```

### 2. **التحكم في المستخدمين الآخرين**
يمكن للمستخدم التحكم فقط في من هم **أقل منه مستوى**:

```php
// مثال: المدير (80) يمكنه تعديل المشرف (60)
if (auth()->user()->role->level > $otherUser->role->level) {
    // يمكن التعديل أو الحذف
}
```

### 3. **الموافقات والاعتمادات**
العمليات التي تحتاج موافقة من مستوى أعلى:

```php
// مثال: الموافقة على فاتورة تحتاج مستوى 60 فأعلى
if (auth()->user()->role->level >= 60) {
    // يمكن الموافقة
}
```

---

## 🔥 أمثلة عملية للاستخدام

### مثال 1: منع تعديل المستخدمين الأعلى مستوى

```php
// في UserController.php
public function update(Request $request, User $user)
{
    // تحقق من المستوى
    if ($user->role->level >= auth()->user()->role->level) {
        abort(403, 'لا يمكنك تعديل مستخدم بنفس مستواك أو أعلى');
    }
    
    // تابع التعديل
    $user->update($request->all());
}
```

### مثال 2: إظهار قائمة المستخدمين الأقل مستوى فقط

```php
// في UsersController
public function index()
{
    $currentLevel = auth()->user()->role->level;
    
    $users = User::whereHas('roleRelation', function($query) use ($currentLevel) {
        $query->where('level', '<', $currentLevel);
    })->get();
    
    return view('users.index', compact('users'));
}
```

### مثال 3: شرط الموافقة حسب المستوى

```php
// في InvoiceController
public function approve(Invoice $invoice)
{
    $requiredLevel = 60; // يحتاج مشرف فأعلى
    
    if (auth()->user()->role->level < $requiredLevel) {
        return back()->with('error', 'تحتاج صلاحية مشرف (مستوى 60) للموافقة');
    }
    
    $invoice->update(['approved' => true]);
}
```

### مثال 4: في Blade - إخفاء أزرار حسب المستوى

```blade
{{-- فقط المستوى 80 فأعلى يرى زر الموافقة النهائية --}}
@if(getRoleLevel() >= 80)
<button class="btn btn-success" onclick="finalApprove()">
    موافقة نهائية
</button>
@endif

{{-- فقط المستوى 60 فأعلى يرى التقارير المالية --}}
@if(getRoleLevel() >= 60)
<a href="{{ route('reports.financial') }}" class="btn btn-primary">
    التقارير المالية
</a>
@endif
```

### مثال 5: قيود على الحذف حسب المستوى

```blade
<table class="table">
    @foreach($users as $user)
    <tr>
        <td>{{ $user->name }}</td>
        <td>{{ $user->role->role_name }} ({{ $user->role->level }})</td>
        <td>
            {{-- يمكن تعديل فقط من هم أقل مستوى --}}
            @if(getRoleLevel() > $user->role->level)
            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary">
                تعديل
            </a>
            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
            </form>
            @else
            <span class="badge bg-secondary">مستوى أعلى</span>
            @endif
        </td>
    </tr>
    @endforeach
</table>
```

---

## 🛡️ قواعد الحماية بالمستوى

### القاعدة 1: لا يمكن تعديل من هو بنفس المستوى أو أعلى
```php
if (auth()->user()->role->level <= $targetUser->role->level) {
    abort(403, 'لا يمكنك تعديل هذا المستخدم');
}
```

### القاعدة 2: Admin (100) له صلاحية على الجميع
```php
if (auth()->user()->isAdmin()) {
    // يمكن فعل أي شيء
} elseif (auth()->user()->role->level > $targetLevel) {
    // يمكن التعديل
}
```

### القاعدة 3: لا يمكن منح دور أعلى من مستواك
```php
public function assignRole(User $user, $newRoleId)
{
    $newRole = Role::find($newRoleId);
    
    if ($newRole->level >= auth()->user()->role->level) {
        return back()->with('error', 'لا يمكنك منح دور بمستوى أعلى أو مساوي لك');
    }
    
    $user->update(['role_id' => $newRoleId]);
}
```

---

## 📋 دوال مساعدة للمستوى

### دالة موجودة مسبقاً:
```php
getRoleLevel()  // تُرجع مستوى المستخدم الحالي
```

### دوال إضافية مقترحة:

```php
// في PermissionHelper.php

if (!function_exists('canManageUser')) {
    function canManageUser($userId)
    {
        $targetUser = User::find($userId);
        if (!$targetUser || !$targetUser->roleRelation) {
            return false;
        }
        
        $currentLevel = getRoleLevel();
        return $currentLevel > $targetUser->roleRelation->level;
    }
}

if (!function_exists('hasMinLevel')) {
    function hasMinLevel($minLevel)
    {
        return getRoleLevel() >= $minLevel;
    }
}

if (!function_exists('canAssignRole')) {
    function canAssignRole($roleId)
    {
        $role = Role::find($roleId);
        if (!$role) {
            return false;
        }
        
        return getRoleLevel() > $role->level;
    }
}
```

### استخدام الدوال الجديدة:

```blade
{{-- تعديل مستخدم --}}
@if(canManageUser($user->id))
<button>تعديل</button>
@endif

{{-- عرض قسم للمدراء فقط (مستوى 80+) --}}
@if(hasMinLevel(80))
<div class="management-panel">
    {{-- محتوى إداري --}}
</div>
@endif

{{-- منع منح دور أعلى --}}
@foreach($roles as $role)
    @if(canAssignRole($role->id))
    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
    @endif
@endforeach
```

---

## 🎨 واجهة عرض المستويات

```blade
<div class="card">
    <div class="card-header">
        <h5>الهيكل الهرمي للأدوار</h5>
    </div>
    <div class="card-body">
        @foreach($roles->sortByDesc('level') as $role)
        <div class="d-flex align-items-center mb-3 p-3 border rounded
             @if($role->level >= 80) bg-danger bg-opacity-10
             @elseif($role->level >= 60) bg-warning bg-opacity-10
             @elseif($role->level >= 40) bg-info bg-opacity-10
             @else bg-secondary bg-opacity-10
             @endif">
            
            {{-- مؤشر المستوى --}}
            <div class="me-3">
                <div class="progress" style="width: 100px; height: 20px;">
                    <div class="progress-bar" 
                         role="progressbar" 
                         style="width: {{ $role->level }}%"
                         aria-valuenow="{{ $role->level }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        {{ $role->level }}
                    </div>
                </div>
            </div>
            
            {{-- معلومات الدور --}}
            <div class="flex-grow-1">
                <h6 class="mb-0">{{ $role->role_name }}</h6>
                <small class="text-muted">{{ $role->role_code }}</small>
            </div>
            
            {{-- عدد المستخدمين --}}
            <div class="text-end">
                <span class="badge bg-primary">
                    {{ $role->users->count() }} مستخدم
                </span>
            </div>
            
            {{-- أيقونة التحكم --}}
            <div class="ms-3">
                @if(getRoleLevel() > $role->level)
                <i class="fas fa-unlock text-success" title="يمكنك التحكم"></i>
                @else
                <i class="fas fa-lock text-danger" title="لا يمكنك التحكم"></i>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
```

---

## ⚠️ تحذيرات مهمة

### 1. **لا تعتمد على المستوى وحده**
المستوى للتنظيم الهرمي، لكن الصلاحيات الدقيقة تُدار عبر Permissions:

```php
// ❌ خطأ - الاعتماد على المستوى فقط
if (getRoleLevel() >= 60) {
    // يمكن حذف الفواتير
}

// ✅ صحيح - استخدام الصلاحيات
if (canDelete('PURCHASE_INVOICES')) {
    // يمكن حذف الفواتير
}

// ✅ الأفضل - دمج المستوى والصلاحية
if (hasMinLevel(60) && canDelete('PURCHASE_INVOICES')) {
    // يمكن حذف الفواتير
}
```

### 2. **Admin دائماً استثناء**
```php
if (isAdmin()) {
    // Admin يتخطى جميع قيود المستوى
}
```

### 3. **لا تسمح بتغيير مستوى Admin**
```php
if ($role->role_code === 'ADMIN' && $request->level != 100) {
    return back()->with('error', 'لا يمكن تغيير مستوى Admin');
}
```

---

## 📊 جدول الاستخدامات المقترحة

| المستوى | الاستخدام المقترح |
|---------|-------------------|
| **90-100** | إدارة النظام الكاملة |
| **70-89** | إدارة عليا، موافقات نهائية |
| **50-69** | إشراف، موافقات متوسطة |
| **30-49** | تنفيذ متقدم، صلاحيات محدودة |
| **10-29** | تنفيذ أساسي |
| **0-9** | قراءة فقط أو محظور |

---

## ✅ الخلاصة

**المستوى هو:**
- ✅ تنظيم هرمي من 0-100
- ✅ يحدد من يتحكم في من
- ✅ يُستخدم مع الصلاحيات وليس بديلاً عنها
- ✅ Admin (100) له أعلى سلطة

**استخدمه عندما تحتاج:**
- منع تعديل المستخدمين الأعلى
- تصفية البيانات حسب السلطة
- موافقات متعددة المستويات
- التحكم الهرمي

---

**تم التحديث:** 22 نوفمبر 2025
