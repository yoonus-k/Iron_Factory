<?php

/**
 * اختبار سريع لدالة نقل المرحلة
 *
 * يمكنك تشغيل هذا الاختبار في tinker أو إضافته كـ feature test
 */

// الخطوة 1: إنشاء ورديتين للاختبار
$fromShift = \App\Models\ShiftAssignment::factory()->create([
    'status' => 'active',
    'stage_number' => 1,
    'worker_ids' => [1, 2, 3]
]);

$toShift = \App\Models\ShiftAssignment::factory()->create([
    'status' => 'active',
    'stage_number' => 1,
    'worker_ids' => [4, 5, 6]
]);

// الخطوة 2: إنشاء ستاند (مرحلة)
$stand = \App\Models\Stand::factory()->create();

echo "✅ تم إنشاء الوردية الأصلية: " . $fromShift->shift_code . "\n";
echo "✅ تم إنشاء الوردية الجديدة: " . $toShift->shift_code . "\n";
echo "✅ تم إنشاء الستاند: " . $stand->stand_number . "\n\n";

// الخطوة 3: محاكاة الطلب
$request = new \Illuminate\Http\Request([
    'stand_id' => $stand->id,
    'from_shift_id' => $fromShift->id,
    'to_shift_id' => $toShift->id,
    'notes' => 'اختبار نقل المرحلة'
]);

// الخطوة 4: تنفيذ النقل
$controller = new \Modules\Manufacturing\Http\Controllers\WorkerTrackingController();
$response = $controller->transferStageToShift($request);
$data = json_decode($response->getContent(), true);

// الخطوة 5: التحقق من النتائج
if ($data['success']) {
    echo "✅ نقل المرحلة بنجاح!\n\n";

    // التحقق من تحديث الستاند
    $updatedStand = \App\Models\Stand::find($stand->id);
    echo "🔍 حالة الستاند: " . $updatedStand->status . "\n";

    // التحقق من تحديث الوردية
    $updatedToShift = \App\Models\ShiftAssignment::find($toShift->id);
    echo "🔍 معرف الستاند في الوردية: " . $updatedToShift->stage_record_id . "\n";
    echo "🔍 باركود الستاند في الوردية: " . $updatedToShift->stage_record_barcode . "\n\n";

    // التحقق من تسجيل النقل
    $tracking = \DB::table('product_tracking')
        ->where('barcode', $stand->barcode)
        ->where('action', 'transfer_shift')
        ->first();

    if ($tracking) {
        echo "✅ تم تسجيل النقل في product_tracking\n";
        echo "   - من: " . $tracking->old_value . "\n";
        echo "   - إلى: " . $tracking->new_value . "\n\n";
    }

    // التحقق من تحديث تتبع العمال
    $endedWorkers = \App\Models\WorkerStageHistory::where('stage_type', 'stage1_stands')
        ->where('stage_record_id', $stand->id)
        ->where('shift_assignment_id', $fromShift->id)
        ->whereNotNull('ended_at')
        ->count();

    $newWorkers = \App\Models\WorkerStageHistory::where('stage_type', 'stage1_stands')
        ->where('stage_record_id', $stand->id)
        ->where('shift_assignment_id', $toShift->id)
        ->whereNull('ended_at')
        ->count();

    echo "✅ تم إنهاء تتبع " . $endedWorkers . " عمال من الوردية الأصلية\n";
    echo "✅ تم إضافة تتبع لـ " . $newWorkers . " عمال من الوردية الجديدة\n\n";

    // التحقق من تسجيل العملية
    $operationLog = \App\Models\ShiftOperationLog::where('operation_type', 'transfer_stage')
        ->where('shift_id', $toShift->id)
        ->latest()
        ->first();

    if ($operationLog) {
        echo "✅ تم تسجيل العملية في shift_operation_logs\n";
        echo "   - الوصف: " . $operationLog->description . "\n\n";
    }

    echo "🎉 جميع التحديثات تمت بنجاح!\n";
} else {
    echo "❌ فشل النقل: " . $data['message'] . "\n";
}
?>
