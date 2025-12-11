# شرح كيفية جلب بيانات تقرير الورديات

## المشاكل التي تم إصلاحها:

### 1. **جلب بيانات العامل (Worker Attendance)**
   - **المشكلة السابقة**: كانت تحاول الوصول إلى `$user->worker->worker_code` مباشرة بدون التحقق من وجود العلاقة
   - **الحل**: إضافة eager loading للعلاقة `with('worker')` وإضافة التحقق من وجود البيانات

```php
// قبل الإصلاح - سيرمي خطأ إذا لم توجد العلاقة
$user = User::find($workerId);
$code = $user->worker->worker_code; // قد يرمي error

// بعد الإصلاح - آمن وموثوق
$user = User::with('worker')->find($workerId);
if ($user->worker) {
    $code = $user->worker->worker_code ?? 'N/A';
}
```

### 2. **جلب بيانات تسليم الورديات (Shift Handovers)**
   - **المشكلة السابقة**: لم تكن تتعامل مع NULL values بشكل صحيح
   - **الحل**: إضافة التحقق من وجود العلاقات قبل الوصول إليها

```php
// قبل الإصلاح
$handover->fromUser->name ?? 'غير محدد' // قد يرمي error إذا كان fromUser null

// بعد الإصلاح
$handover->fromUser ? $handover->fromUser->name : 'غير محدد'
```

### 3. **جلب بيانات تعيين الوردية (Shift Assignment)**
   - **المشكلة السابقة**: كانت تستدعي `$assignment->workers()` وهي دالة غير موجودة في النموذج
   - **الحل**: قراءة مصفوفة `worker_ids` والحصول على المستخدمين منها

```php
// قبل الإصلاح
'workers' => $assignment->workers() // دالة غير موجودة

// بعد الإصلاح
if ($assignment->worker_ids && is_array($assignment->worker_ids)) {
    $workers = User::whereIn('id', $assignment->worker_ids)->get();
}
```

### 4. **جلب بيانات الفرق النشطة (Active Teams)**
   - **المشكلة السابقة**: كانت تستدعي `$team->workers()` وهي دالة قد تكون غير موثوقة
   - **الحل**: قراءة مصفوفة `worker_ids` مباشرة من النموذج

```php
// قبل الإصلاح
$teamWorkers = $team->workers();
$workerIds = $teamWorkers->pluck('id')->toArray();

// بعد الإصلاح
$workerIds = $team->worker_ids && is_array($team->worker_ids) ? $team->worker_ids : [];
```

## البيانات المرسلة إلى الـ View:

### 1. **Summary** (الملخص الإجمالي):
```php
[
    'total_items' => عدد القطع الإجمالي,
    'total_output_kg' => وزن الإنتاج الإجمالي,
    'total_waste_kg' => وزن الهدر الإجمالي,
    'waste_percentage' => نسبة الهدر,
    'efficiency' => كفاءة الوردية,
    'workers_count' => عدد العمال,
]
```

### 2. **Top Performers** (أفضل أداء):
```php
[
    [
        'worker_id' => رقم العامل,
        'worker_name' => اسم العامل,
        'items' => عدد القطع,
        'output' => الإنتاج بالكيلوجرام,
        'waste' => الهدر,
        'waste_pct' => نسبة الهدر,
        'efficiency' => الكفاءة,
    ]
]
```

### 3. **Attendance** (حضور العمال):
```php
[
    [
        'worker_id' => رقم العامل,
        'worker_name' => اسم العامل,
        'worker_code' => كود العامل,
        'position' => الوظيفة,
        'stages_worked' => المراحل التي عمل فيها [1, 2, 3],
        'total_items' => عدد القطع,
        'total_output' => الإنتاج,
        'total_waste' => الهدر,
        'efficiency' => الكفاءة,
        'hours_worked' => ساعات العمل,
    ]
]
```

### 4. **Handovers** (تسليم الورديات):
```php
[
    [
        'id' => رقم التسليم,
        'from_user' => اسم المسلم,
        'to_user' => اسم المستقبل,
        'stage_number' => رقم المرحلة,
        'stage_name' => اسم المرحلة,
        'items_count' => عدد القطع,
        'handover_time' => وقت التسليم,
        'supervisor_approved' => معتمد من المشرف؟,
        'notes' => الملاحظات,
    ]
]
```

### 5. **Stage Efficiency** (كفاءة المراحل):
```php
[
    [
        'stage' => رقم المرحلة,
        'name' => اسم المرحلة,
        'items' => عدد القطع,
        'output' => الإنتاج,
        'waste' => الهدر,
        'waste_pct' => نسبة الهدر,
        'efficiency' => الكفاءة,
        'workers_count' => عدد العمال,
        'avg_per_worker' => المتوسط للعامل,
        'production_rate' => معدل الإنتاج,
    ]
]
```

### 6. **Active Teams** (الفرق النشطة):
```php
[
    [
        'team_code' => كود الفريق,
        'team_name' => اسم الفريق,
        'total_members' => عدد الأعضاء,
        'active_members' => عدد الأعضاء النشطين,
        'total_production' => الإنتاج الكلي,
        'avg_per_member' => المتوسط للعضو,
    ]
]
```

## التحقق من البيانات:

للتحقق من أن البيانات تجلب بشكل صحيح، يمكنك:

1. **فحص السجلات (Logs)**:
```bash
tail -f storage/logs/laravel.log
```

2. **استخدام Laravel Tinker**:
```bash
php artisan tinker
```

ثم:
```php
$controller = app(\Modules\Manufacturing\Http\Controllers\ShiftDashboardController::class);
$summary = $controller->getShiftSummary('2025-12-10', 'evening');
dd($summary);
```

3. **فحص قاعدة البيانات**:
```bash
php artisan tinker
```

ثم:
```php
DB::table('stage1_stands')->whereBetween('created_at', ['2025-12-10 18:00:00', '2025-12-11 06:00:00'])->count();
```

## نصائح مهمة:

1. **تأكد من وجود بيانات في قاعدة البيانات** للتاريخ والوردية المحددة
2. **استخدم eager loading** (`with()`) لتحسين الأداء وتجنب N+1 queries
3. **تحقق من NULL values** قبل الوصول إلى العلاقات
4. **استخدم null safe operator** (`?->`) عند الحاجة

## مثال على الاستخدام الآمن:

```php
// بدلاً من هذا (قد يرمي error):
$name = $user->worker->position_name;

// استخدم هذا (آمن):
$position = $user->worker ? $user->worker->position_name ?? 'عامل' : 'عامل';

// أو باستخدام null safe operator:
$position = $user->worker?->position_name ?? 'عامل';
```

## الملخص:

✅ تم إصلاح جميع مشاكل جلب البيانات
✅ تم إضافة التحقق من NULL values
✅ تم تحسين الأداء باستخدام eager loading
✅ تم ضمان عدم رمي أي أخطاء عند فقدان البيانات
✅ تم توثيق البيانات المرسلة إلى الـ View بشكل واضح
