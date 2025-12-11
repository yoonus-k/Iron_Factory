# نظام تتبع العمال - Worker Tracking System

## نظرة عامة

نظام تتبع العمال يسمح لك بمعرفة:
- ✅ **من يعمل حالياً** على أي مرحلة إنتاج
- ✅ **من كان يعمل سابقاً** على المرحلة
- ✅ **متى بدأ ومتى انتهى** كل عامل
- ✅ **كم استغرق** من الوقت
- ✅ **تاريخ كامل** لكل مرحلة وكل عامل

---

## المكونات الرئيسية

### 1. الجدول: `worker_stage_history`
يحفظ كل تغيير في العمال للمراحل الأربعة

**الأعمدة المهمة:**
- `stage_type`: نوع المرحلة (stage1_stands, stage2_processed, stage3_coils, stage4_boxes)
- `stage_record_id`: رقم السجل في جدول المرحلة
- `barcode`: الباركود للوصول السريع
- `worker_id`: العامل الفردي
- `worker_team_id`: أو الفريق
- `started_at`: متى بدأ العمل
- `ended_at`: متى انتهى (null = لا يزال يعمل)
- `duration_minutes`: المدة بالدقائق
- `is_active`: هل لا يزال نشط

---

## كيفية الاستخدام

### سيناريو 1: عامل بدأ العمل على ستاند
```php
use App\Services\WorkerTrackingService;
use App\Models\WorkerStageHistory;

$service = app(WorkerTrackingService::class);

// تعيين العامل
$history = $service->assignWorkerToStage(
    stageType: WorkerStageHistory::STAGE_1_STANDS,
    stageRecordId: $stand->id,
    workerId: auth()->id(),
    barcode: $stand->barcode,
    statusBefore: $stand->status
);
```

---

### سيناريو 2: معرفة من يعمل حالياً
```php
$currentWorker = $service->getCurrentWorkerForStage(
    WorkerStageHistory::STAGE_1_STANDS,
    $stand->id
);

if ($currentWorker) {
    echo "العامل: " . $currentWorker->worker_name;
    echo "بدأ: " . $currentWorker->started_at->format('H:i');
    echo "المدة: " . $currentWorker->formatted_duration;
}
```

---

### سيناريو 3: عرض تاريخ المرحلة
```php
$history = $service->getStageHistory(
    WorkerStageHistory::STAGE_2_PROCESSED,
    $processed->id
);

// $history = مجموعة من السجلات مرتبة من الأحدث للأقدم
foreach ($history as $record) {
    echo $record->worker_name;
    echo $record->started_at;
    echo $record->ended_at;
    echo $record->formatted_duration;
}
```

---

### سيناريو 4: نقل العمل من عامل لآخر
```php
// مثال: نهاية الشيفت ونقل العمل للشيفت التالي
$service->transferWork(
    stageType: WorkerStageHistory::STAGE_3_COILS,
    stageRecordId: $coil->id,
    newWorkerId: $nextWorker->id,
    notes: 'نقل بسبب نهاية الشيفت',
    assignedBy: auth()->id()
);

// النظام تلقائياً:
// 1. ينهي جلسة العامل الحالي
// 2. يحسب المدة
// 3. يبدأ جلسة جديدة للعامل الجديد
```

---

### سيناريو 5: إنهاء عمل العامل
```php
// عند إتمام العمل أو إيقافه
$service->endWorkerSession(
    historyId: $history->id,
    statusAfter: 'completed',
    notes: 'تم إتمام العمل بنجاح'
);
```

---

### سيناريو 6: عرض تاريخ عامل معين
```php
// كل ما عمل عليه العامل
$workerHistory = $service->getWorkerHistory(
    workerId: 5
);

// أو لمرحلة معينة فقط
$workerHistory = $service->getWorkerHistory(
    workerId: 5,
    stageType: WorkerStageHistory::STAGE_1_STANDS
);
```

---

### سيناريو 7: إحصائيات عامل
```php
$stats = $service->getWorkerStatistics(workerId: 5);

// النتيجة:
[
    'total_sessions' => 45,           // إجمالي الجلسات
    'active_sessions' => 2,           // نشط حالياً
    'completed_sessions' => 43,       // مكتمل
    'total_minutes' => 2850,          // إجمالي الدقائق
    'total_hours' => 47.5,            // إجمالي الساعات
    'stages_worked' => ['stage1_stands', 'stage2_processed'],
    'average_session_minutes' => 63   // متوسط الجلسة
]
```

---

### سيناريو 8: إحصائيات مرحلة
```php
$stats = $service->getStageStatistics(
    stageType: WorkerStageHistory::STAGE_2_PROCESSED,
    stageRecordId: 456
);

// النتيجة:
[
    'total_sessions' => 12,
    'total_workers' => 5,
    'total_teams' => 2,
    'total_hours' => 18.5,
    'current_worker' => WorkerStageHistory object,
    'average_session_minutes' => 92
]
```

---

### سيناريو 9: البحث بالباركود
```php
// معرفة كل من عمل على باركود معين
$history = $service->getHistoryByBarcode('ST-2025-001');

foreach ($history as $record) {
    echo $record->worker_name;
    echo $record->stage_name;
    echo $record->started_at . ' → ' . $record->ended_at;
}
```

---

### سيناريو 10: العمال المتاحين
```php
// من هم العمال الذين ليسوا مشغولين حالياً؟
$availableWorkers = $service->getAvailableWorkers();

foreach ($availableWorkers as $worker) {
    echo $worker->name;
}
```

---

## الربط مع المراحل الحالية

### في Controller الخاص بالمراحل

#### Stage1 - عند بدء العمل على الستاند
```php
public function startWork($standId)
{
    $stand = Stage1Stand::findOrFail($standId);
    $service = app(WorkerTrackingService::class);
    
    // تسجيل أن العامل بدأ العمل
    $service->assignWorkerToStage(
        stageType: WorkerStageHistory::STAGE_1_STANDS,
        stageRecordId: $stand->id,
        workerId: auth()->id(),
        barcode: $stand->barcode,
        statusBefore: $stand->status
    );
    
    // تحديث حالة الستاند
    $stand->update(['status' => 'in_process']);
    
    return redirect()->back();
}
```

#### Stage1 - عند إتمام العمل
```php
public function completeWork($standId)
{
    $stand = Stage1Stand::findOrFail($standId);
    $service = app(WorkerTrackingService::class);
    
    // إنهاء الجلسة
    $currentSession = $service->getCurrentWorkerForStage(
        WorkerStageHistory::STAGE_1_STANDS,
        $stand->id
    );
    
    if ($currentSession) {
        $service->endWorkerSession(
            historyId: $currentSession->id,
            statusAfter: 'completed',
            notes: 'تم إكمال العمل'
        );
    }
    
    // تحديث حالة الستاند
    $stand->update(['status' => 'completed']);
    
    return redirect()->back();
}
```

---

### في صفحة تفاصيل المرحلة (Blade)

```blade
{{-- عرض العامل الحالي --}}
@php
$currentWorker = \App\Models\WorkerStageHistory::getCurrentWorkerForStage(
    \App\Models\WorkerStageHistory::STAGE_1_STANDS,
    $stand->id
);
@endphp

@if($currentWorker)
<div class="alert alert-info">
    <strong>العامل الحالي:</strong> {{ $currentWorker->worker_name }}
    <br>
    <strong>بدأ:</strong> {{ $currentWorker->started_at->format('H:i') }}
    <br>
    <strong>المدة:</strong> {{ $currentWorker->formatted_duration }}
</div>
@endif

{{-- عرض التاريخ --}}
<a href="{{ route('worker-tracking.stage-history', [
    'stageType' => \App\Models\WorkerStageHistory::STAGE_1_STANDS,
    'stageRecordId' => $stand->id
]) }}" class="btn btn-info">
    <i class="fas fa-history"></i>
    عرض تاريخ العمال
</a>
```

---

## الربط مع نظام تسليم الورديات

عند تسليم الوردية وانتقال العمل المعلق:

```php
// في ShiftHandoverService عند collectPendingWork
foreach ($pendingItems as $item) {
    // نقل العمل للعامل المستلم
    $trackingService->assignWorkerToStage(
        stageType: $item['stage_type'],
        stageRecordId: $item['id'],
        workerId: $handover->to_worker_id,
        barcode: $item['barcode'],
        statusBefore: $item['status'],
        assignedBy: $handover->from_worker_id,
        shiftAssignmentId: $handover->shift_assignment_id
    );
}
```

---

## Routes المتاحة

```php
// لوحة التحكم
GET /worker-tracking/dashboard

// تاريخ مرحلة
GET /worker-tracking/stage/{stageType}/{stageRecordId}

// تاريخ عامل
GET /worker-tracking/worker/{workerId}

// البحث بالباركود
GET /worker-tracking/search?barcode=ST-2025-001

// نقل العمل
POST /worker-tracking/transfer

// إنهاء جلسة
POST /worker-tracking/end-session/{historyId}

// العمال المتاحين (AJAX)
GET /worker-tracking/available-workers
```

---

## أمثلة في Blade Views

### زر عرض التاريخ في قائمة Stands
```blade
<a href="{{ route('worker-tracking.stage-history', [
    'stageType' => 'stage1_stands',
    'stageRecordId' => $stand->id
]) }}" class="btn btn-sm btn-info">
    <i class="fas fa-history"></i>
    التاريخ
</a>
```

### عرض العامل الحالي في Card
```blade
@php
$current = \App\Models\WorkerStageHistory::getCurrentWorkerForStage(
    'stage2_processed',
    $processed->id
);
@endphp

@if($current)
<div class="badge bg-success">
    {{ $current->worker_name }} - {{ $current->formatted_duration }}
</div>
@endif
```

---

## دعم الفرق (Teams)

النظام يدعم **عامل فردي** أو **فريق كامل**:

```php
// تعيين فريق
$service->assignTeamToStage(
    stageType: WorkerStageHistory::STAGE_3_COILS,
    stageRecordId: $coil->id,
    workerTeamId: 3,
    barcode: $coil->barcode
);

// عرض التاريخ للفريق
$teamHistory = $service->getTeamHistory(teamId: 3);
```

---

## الخلاصة

✅ **بسيط**: تعيين عامل → بداية تلقائية → إنهاء يدوي أو تلقائي  
✅ **شامل**: يحفظ كل التفاصيل (وقت، مدة، ملاحظات)  
✅ **مرن**: يدعم عامل فردي أو فريق  
✅ **متكامل**: يربط مع الشيفتات والمراحل  
✅ **سهل الاستخدام**: Service جاهز + Views جاهزة  

---

## للمزيد

راجع ملف الأمثلة الكامل:
`docs/WORKER_TRACKING_USAGE_EXAMPLES.php`
