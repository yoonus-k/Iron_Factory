# أمثلة عملية - نظام تتبع العمال

## 📝 أمثلة من الكود

### مثال 1: إنشاء وردية جديدة

```php
// في ShiftsWorkersController::store()

// البيانات المدخلة:
$request->validate([
    'shift_code' => 'required|unique:shift_assignments',
    'stage_number' => 'required|integer|between:1,4',
    'worker_ids' => 'array',  // مثل: [1, 2, 3]
]);

// 1. إنشاء الوردية
$shift = ShiftAssignment::create([
    'shift_code' => 'WRD-001',
    'stage_number' => 1,
    'worker_ids' => [1, 2, 3],
    'total_workers' => 3,
]);

// 2. تسجيل تتبع العمال (جديد ✅)
$stageType = 'stage' . $shift->stage_number . '_stands'; // 'stage1_stands'

foreach ([1, 2, 3] as $workerId) {
    WorkerStageHistory::create([
        'stage_type' => 'stage1_stands',
        'stage_record_id' => $shift->id,
        'worker_id' => $workerId,
        'worker_type' => 'individual',
        'started_at' => now(),    // الآن
        'ended_at' => null,       // لم ينتهِ بعد
        'is_active' => true,
        'shift_assignment_id' => $shift->id,
        'assigned_by' => auth()->user()->id,
        'notes' => 'تعيين أولي للعامل في الوردية'
    ]);
}

// النتيجة في قاعدة البيانات:
// ┌─────────────────────────────────────────────────────────┐
// │ worker_stage_history                                   │
// ├─────────────────────────────────────────────────────────┤
// │ id │ worker_id │ started_at      │ ended_at │ is_active│
// ├─────────────────────────────────────────────────────────┤
// │ 1  │ 1         │ 2025-12-13 10:00│ NULL     │ true     │
// │ 2  │ 2         │ 2025-12-13 10:00│ NULL     │ true     │
// │ 3  │ 3         │ 2025-12-13 10:00│ NULL     │ true     │
// └─────────────────────────────────────────────────────────┘
```

---

### مثال 2: نقل الوردية (الحالة الرئيسية ⭐)

```php
// في ShiftsWorkersController::transferStore()

// البيانات المدخلة:
$request->validate([
    'new_supervisor_id' => 'required|exists:users,id',
    'new_workers' => 'array',  // مثل: [3, 4, 5]
]);

$shift = ShiftAssignment::find($id);  // الوردية WRD-001
$newWorkerIds = [3, 4, 5];

// 1. إنهاء تتبع العمال القدامى
WorkerStageHistory::where('stage_type', 'stage1_stands')
    ->where('shift_assignment_id', $shift->id)
    ->whereNull('ended_at')           // النشطين فقط
    ->where('is_active', true)
    ->update([
        'ended_at' => now(),           // 2025-12-13 11:30
        'is_active' => false,
        'notes' => 'تم نقل الوردية'
    ]);

// 2. إضافة العمال الجدد
foreach ([3, 4, 5] as $workerId) {
    WorkerStageHistory::create([
        'stage_type' => 'stage1_stands',
        'stage_record_id' => $shift->id,
        'worker_id' => $workerId,
        'worker_type' => 'individual',
        'started_at' => now(),          // 2025-12-13 11:30
        'ended_at' => null,
        'is_active' => true,
        'shift_assignment_id' => $shift->id,
        'assigned_by' => auth()->user()->id,
        'notes' => 'عامل جديد من نقل الوردية'
    ]);
}

// النتيجة في قاعدة البيانات (بعد النقل):
// ┌────────────────────────────────────────────────────────────────┐
// │ worker_stage_history                                          │
// ├────────────────────────────────────────────────────────────────┤
// │ id │ worker_id │ started_at      │ ended_at         │ is_active│
// ├────────────────────────────────────────────────────────────────┤
// │ 1  │ 1         │ 10:00           │ 11:30           │ false    │
// │ 2  │ 2         │ 10:00           │ 11:30           │ false    │
// │ 3  │ 3         │ 10:00           │ 11:30           │ false    │
// │ 4  │ 3         │ 11:30           │ NULL            │ true     │
// │ 5  │ 4         │ 11:30           │ NULL            │ true     │
// │ 6  │ 5         │ 11:30           │ NULL            │ true     │
// └────────────────────────────────────────────────────────────────┘
```

---

### مثال 3: جلب العمال النشطين

```php
// في stage1/show.blade.php

// الكود الصحيح:
$workersInStage = WorkerStageHistory::where('stage_type', 'stage1_stands')
    ->where('stage_record_id', $stand->id)
    ->where('is_active', true)          // ✅ الصحيح
    ->whereNull('ended_at')             // ✅ الصحيح
    ->orderBy('started_at', 'desc')
    ->get();

// النتيجة:
// جميع العمال الذين:
// 1. في المرحلة الأولى
// 2. وحالتهم نشطة (is_active = true)
// 3. ولم ينتهوا بعد (ended_at IS NULL)

// @foreach($workersInStage as $history)
//   اسم العامل: {{ $history->worker->name }}
//   وقت البدء: {{ $history->started_at->format('H:i') }}
//   المدة: {{ $history->started_at->diffInMinutes(now()) }} دقيقة
// @endforeach
```

---

### مثال 4: جلب سجل العمليات

```php
// في stage1/show.blade.php

// جلب جميع النقلات والتعديلات على المرحلة الأولى
$operations = ShiftOperationLog::where('stage_number', 1)
    ->whereIn('operation_type', ['transfer', 'create', 'update', 'assign_stage'])
    ->orderBy('created_at', 'desc')
    ->limit(20)
    ->get();

// كل سجل يحتوي على:
// - operation_type: مثل 'transfer'
// - old_data: البيانات السابقة (JSON)
//   {
//     "supervisor_name": "أحمد",
//     "workers_count": 3,
//     "worker_ids": [1, 2, 3]
//   }
// - new_data: البيانات الجديدة (JSON)
//   {
//     "supervisor_name": "محمود",
//     "workers_count": 5,
//     "worker_ids": [3, 4, 5, 6, 7]
//   }
// - description: "تم نقل الوردية من أحمد إلى محمود"
// - created_at: وقت العملية
```

---

## 🔍 استعلامات SQL مفيدة

### استعلام 1: عدد العمال النشطين الآن

```sql
SELECT COUNT(DISTINCT worker_id) as active_workers
FROM worker_stage_history
WHERE stage_type = 'stage1_stands'
  AND is_active = true
  AND ended_at IS NULL;
```

### استعلام 2: متوسط مدة عمل العامل

```sql
SELECT 
    worker_id,
    AVG(TIMESTAMPDIFF(MINUTE, started_at, ended_at)) as avg_duration
FROM worker_stage_history
WHERE ended_at IS NOT NULL
GROUP BY worker_id;
```

### استعلام 3: تاريخ نقلات وردية معينة

```sql
SELECT 
    sol.id,
    sol.operation_type,
    sol.description,
    sol.created_at,
    sol.old_data,
    sol.new_data
FROM shift_operation_logs sol
WHERE sol.shift_id = 123
  AND sol.operation_type = 'transfer'
ORDER BY sol.created_at DESC;
```

### استعلام 4: مجموع ساعات عمل العامل

```sql
SELECT 
    worker_id,
    SUM(TIMESTAMPDIFF(HOUR, started_at, COALESCE(ended_at, NOW()))) as total_hours
FROM worker_stage_history
WHERE worker_id = 1
  AND stage_type LIKE 'stage%'
GROUP BY worker_id;
```

---

## 🧪 حالات اختبار

### حالة الاختبار 1: نقل وردية بسيطة

```php
// الترتيب:
// 1. إنشاء وردية بـ 3 عمال
$shift = ShiftAssignment::create(['worker_ids' => [1, 2, 3]]);

// النتيجة المتوقعة:
// ✅ 3 سجلات في worker_stage_history مع is_active=true

// 2. نقل الوردية إلى 2 عامل
$shift->update(['worker_ids' => [4, 5]]);

// النتيجة المتوقعة:
// ✅ 3 سجلات القديمة منتهية (ended_at set)
// ✅ 2 سجل جديد مع is_active=true
// ✅ إجمالي 5 سجلات
```

### حالة الاختبار 2: نقل متكرر

```php
// نقل 1: من عامل إلى عامل
$history1 = WorkerStageHistory::where('worker_id', 1)->first();
// is_active: true, ended_at: NULL

// نقل الوردية (العامل 1 ينتهي)
// is_active: false, ended_at: set ✓

// إضافة عامل 1 مرة أخرى
// سجل جديد يُنشأ
// id: مختلف، started_at: جديد ✓
```

---

## 📊 أمثلة النتائج

### مثال النتيجة 1: عمال نشطين

```php
$workersInStage = WorkerStageHistory::where('is_active', true)
    ->whereNull('ended_at')
    ->get();

// النتيجة:
// [
//   {
//     'worker_id': 3,
//     'started_at': '2025-12-13 11:30:00',
//     'ended_at': null,
//     'is_active': true,
//     'duration_from_now': '45 minutes'
//   },
//   {
//     'worker_id': 4,
//     'started_at': '2025-12-13 11:30:00',
//     'ended_at': null,
//     'is_active': true,
//     'duration_from_now': '45 minutes'
//   }
// ]
```

### مثال النتيجة 2: سجل العمليات

```php
$operations = ShiftOperationLog::where('shift_id', 123)->get();

// النتيجة:
// [
//   {
//     'operation_type': 'transfer',
//     'description': 'تم نقل الوردية من أحمد إلى محمود',
//     'old_data': {
//       'supervisor_name': 'أحمد',
//       'workers_count': 3
//     },
//     'new_data': {
//       'supervisor_name': 'محمود',
//       'workers_count': 5
//     },
//     'created_at': '2025-12-13 11:30:00'
//   },
//   {
//     'operation_type': 'create',
//     'description': 'تم إنشاء وردية جديدة WRD-001',
//     'created_at': '2025-12-13 10:00:00'
//   }
// ]
```

---

## 🛠️ معالجة الأخطاء

### خطأ 1: عدم العثور على العامل

```php
try {
    $worker = Worker::findOrFail($workerId);
} catch (ModelNotFoundException $e) {
    // معالجة: العامل غير موجود
    Log::error("Worker not found: {$workerId}");
    return response()->json(['error' => 'العامل غير موجود'], 404);
}
```

### خطأ 2: وردية غير موجودة

```php
$shift = ShiftAssignment::findOrFail($shiftId);
if (!$shift) {
    return response()->json(['error' => 'الوردية غير موجودة'], 404);
}
```

### خطأ 3: عدم وجود بيانات

```php
$workersInStage = WorkerStageHistory::where('is_active', true)->get();
if ($workersInStage->isEmpty()) {
    return view('stage', ['message' => 'لا يوجد عمال نشطين حالياً']);
}
```

---

## 📚 المراجع السريعة

| الدالة | الوصف | المثال |
|--------|--------|---------|
| `WorkerStageHistory::create()` | إضافة عامل | `create(['worker_id' => 1])` |
| `WorkerStageHistory::where()` | البحث | `where('is_active', true)` |
| `whereNull('ended_at')` | منتهي=NULL | بحث عن نشطين |
| `now()` | الوقت الحالي | `started_at = now()` |
| `ShiftOperationLog::logOperation()` | تسجيل عملية | لكل تعديل |

---

## ✅ قائمة تطوير

عند إضافة ميزة جديدة تتعلق بالعمال:

- [ ] التحقق من إضافة تسجيل في `WorkerStageHistory`
- [ ] التحقق من تسجيل العملية في `ShiftOperationLog`
- [ ] استخدام `is_active` و `whereNull('ended_at')` للبحث عن النشطين
- [ ] إضافة `shift_assignment_id` للربط الصحيح
- [ ] إضافة ملاحظات توضيحية للعملية
- [ ] اختبار النقل والتحديث

---

*آخر تحديث: 13 ديسمبر 2025*
