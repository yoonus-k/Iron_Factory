<?php

/**
 * أمثلة على استخدام نظام تتبع العمال
 * Worker Tracking System Usage Examples
 */

use App\Services\WorkerTrackingService;
use App\Models\WorkerStageHistory;

// ============================================
// 1. تعيين عامل لمرحلة (عند بدء العمل)
// ============================================

$trackingService = app(WorkerTrackingService::class);

// مثال: عامل بدأ العمل على stage1_stand
$history = $trackingService->assignWorkerToStage(
    stageType: WorkerStageHistory::STAGE_1_STANDS,
    stageRecordId: 123, // ID السجل في جدول stage1_stands
    workerId: 5, // ID العامل
    barcode: 'ST-2025-001', // الباركود
    statusBefore: 'created', // الحالة قبل العمل
    assignedBy: auth()->id(), // من قام بالتعيين
    shiftAssignmentId: 10 // ربط بالشيفت (اختياري)
);

// ============================================
// 2. تعيين فريق كامل لمرحلة
// ============================================

$history = $trackingService->assignTeamToStage(
    stageType: WorkerStageHistory::STAGE_2_PROCESSED,
    stageRecordId: 456,
    workerTeamId: 3, // ID الفريق
    barcode: 'PR-2025-002',
    statusBefore: 'in_process',
    assignedBy: auth()->id()
);

// ============================================
// 3. معرفة من يعمل حالياً على مرحلة معينة
// ============================================

$currentWorker = $trackingService->getCurrentWorkerForStage(
    stageType: WorkerStageHistory::STAGE_3_COILS,
    stageRecordId: 789
);

if ($currentWorker) {
    echo "العامل الحالي: " . $currentWorker->worker_name;
    echo "بدأ العمل: " . $currentWorker->started_at->format('Y-m-d H:i');
    echo "المدة: " . $currentWorker->formatted_duration;
}

// ============================================
// 4. عرض تاريخ كامل لمرحلة معينة
// ============================================

$history = $trackingService->getStageHistory(
    stageType: WorkerStageHistory::STAGE_4_BOXES,
    stageRecordId: 999
);

foreach ($history as $record) {
    echo "{$record->worker_name} - من {$record->started_at->format('H:i')} ";
    if ($record->ended_at) {
        echo "إلى {$record->ended_at->format('H:i')}";
    } else {
        echo "(لا يزال يعمل)";
    }
    echo " - المدة: {$record->formatted_duration}\n";
}

// ============================================
// 5. إنهاء جلسة عمل عامل
// ============================================

$trackingService->endWorkerSession(
    historyId: $history->id,
    statusAfter: 'completed', // الحالة بعد الانتهاء
    notes: 'تم إنهاء العمل بنجاح' // ملاحظات
);

// ============================================
// 6. نقل العمل من عامل لآخر
// ============================================

$newHistory = $trackingService->transferWork(
    stageType: WorkerStageHistory::STAGE_1_STANDS,
    stageRecordId: 123,
    newWorkerId: 8, // العامل الجديد
    notes: 'نقل بسبب نهاية الشيفت',
    assignedBy: auth()->id()
);

// ============================================
// 7. عرض تاريخ عامل معين
// ============================================

$workerHistory = $trackingService->getWorkerHistory(
    workerId: 5,
    stageType: WorkerStageHistory::STAGE_1_STANDS // اختياري: لتصفية حسب مرحلة معينة
);

foreach ($workerHistory as $record) {
    echo "{$record->stage_name} - {$record->barcode}\n";
    echo "من {$record->started_at} إلى {$record->ended_at}\n";
    echo "المدة: {$record->formatted_duration}\n\n";
}

// ============================================
// 8. إحصائيات عامل
// ============================================

$stats = $trackingService->getWorkerStatistics(workerId: 5);

echo "إجمالي الجلسات: {$stats['total_sessions']}\n";
echo "الجلسات النشطة: {$stats['active_sessions']}\n";
echo "الجلسات المكتملة: {$stats['completed_sessions']}\n";
echo "إجمالي الساعات: {$stats['total_hours']}\n";
echo "المراحل التي عمل بها: " . implode(', ', $stats['stages_worked']) . "\n";

// ============================================
// 9. إحصائيات مرحلة
// ============================================

$stageStats = $trackingService->getStageStatistics(
    stageType: WorkerStageHistory::STAGE_2_PROCESSED,
    stageRecordId: 456
);

echo "إجمالي الجلسات: {$stageStats['total_sessions']}\n";
echo "عدد العمال: {$stageStats['total_workers']}\n";
echo "عدد الفرق: {$stageStats['total_teams']}\n";
echo "إجمالي الساعات: {$stageStats['total_hours']}\n";

// ============================================
// 10. البحث بالباركود
// ============================================

$barcodeHistory = $trackingService->getHistoryByBarcode('ST-2025-001');

foreach ($barcodeHistory as $record) {
    echo "{$record->worker_name} - {$record->stage_name}\n";
    echo "من {$record->started_at} إلى {$record->ended_at}\n";
}

// ============================================
// 11. الحصول على العمال المتاحين (غير مشغولين)
// ============================================

$availableWorkers = $trackingService->getAvailableWorkers();

echo "العمال المتاحين:\n";
foreach ($availableWorkers as $worker) {
    echo "- {$worker->name} ({$worker->email})\n";
}

// ============================================
// 12. إنهاء جميع الجلسات النشطة لمرحلة
// ============================================

$endedCount = $trackingService->endAllActiveSessionsForStage(
    stageType: WorkerStageHistory::STAGE_3_COILS,
    stageRecordId: 789,
    statusAfter: 'paused'
);

echo "تم إنهاء {$endedCount} جلسة نشطة\n";

// ============================================
// 13. استخدام مباشر في Controller
// ============================================

// في Controller مرحلة Stage1
public function assignWorker(Request $request, $standId)
{
    $trackingService = app(WorkerTrackingService::class);

    $stand = Stage1Stand::findOrFail($standId);

    $history = $trackingService->assignWorkerToStage(
        stageType: WorkerStageHistory::STAGE_1_STANDS,
        stageRecordId: $stand->id,
        workerId: $request->worker_id,
        barcode: $stand->barcode,
        statusBefore: $stand->status,
        assignedBy: auth()->id()
    );

    return redirect()->back()->with('success', 'تم تعيين العامل بنجاح');
}

// ============================================
// 14. عرض التاريخ في صفحة تفاصيل المرحلة
// ============================================

// في Blade view لتفاصيل Stage
$currentWorker = WorkerStageHistory::getCurrentWorkerForStage(
    WorkerStageHistory::STAGE_1_STANDS,
    $stand->id
);

$history = WorkerStageHistory::getStageHistory(
    WorkerStageHistory::STAGE_1_STANDS,
    $stand->id
);

// ============================================
// 15. ربط مع نظام تسليم الورديات
// ============================================

// عند إنشاء تسليم وردية جديد
$handover = ShiftHandover::create([...]);

// تعيين العامل المستلم للعمل على العناصر المعلقة
foreach ($handover->pending_items as $item) {
    $trackingService->assignWorkerToStage(
        stageType: $item['stage_type'],
        stageRecordId: $item['stage_record_id'],
        workerId: $handover->to_worker_id,
        barcode: $item['barcode'],
        statusBefore: $item['status'],
        assignedBy: $handover->from_worker_id,
        shiftAssignmentId: $handover->shift_assignment_id
    );
}
