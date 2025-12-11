<?php

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * أمثلة عملية على استخدام نظام تقرير الورديات
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * هذا الملف يحتوي على أمثلة عملية لاستخدام نظام تقرير الورديات
 * ويمكنك نسخ الأكواد واستخدامها مباشرة
 */

// ═══════════════════════════════════════════════════════════════════════════
// مثال 1: الحصول على ملخص الوردية
// ═══════════════════════════════════════════════════════════════════════════

use Modules\Manufacturing\Http\Controllers\ShiftDashboardController;

$controller = app(ShiftDashboardController::class);

// الحصول على ملخص الوردية المسائية ليوم 2025-12-10
$summary = $controller->getShiftSummary('2025-12-10', 'evening');

// الآن يمكنك الوصول إلى البيانات:
echo "إجمالي القطع: " . $summary['total_items'];
echo "الإنتاج: " . $summary['total_output_kg'] . " كجم";
echo "الهدر: " . $summary['total_waste_kg'] . " كجم";
echo "الكفاءة: " . $summary['efficiency'] . "%";

// ═══════════════════════════════════════════════════════════════════════════
// مثال 2: الحصول على أفضل أداء
// ═══════════════════════════════════════════════════════════════════════════

// الحصول على أفضل 5 عمال
$topPerformers = $controller->getTopPerformers('2025-12-10', 'evening', 5);

foreach ($topPerformers as $index => $performer) {
    echo ($index + 1) . ". " . $performer['worker_name'];
    echo " - " . $performer['items'] . " قطعة";
    echo " - كفاءة: " . $performer['efficiency'] . "%";
}

// ═══════════════════════════════════════════════════════════════════════════
// مثال 3: الحصول على حضور العمال
// ═══════════════════════════════════════════════════════════════════════════

$attendance = $controller->getWorkerAttendance('2025-12-10', 'evening');

// عرض جدول بحضور العمال
foreach ($attendance as $worker) {
    echo "الاسم: " . $worker['worker_name'];
    echo " | الكود: " . $worker['worker_code'];
    echo " | القطع: " . $worker['total_items'];
    echo " | الكفاءة: " . $worker['efficiency'] . "%";
    echo "\n";
}

// ═══════════════════════════════════════════════════════════════════════════
// مثال 4: الحصول على كفاءة المراحل
// ═══════════════════════════════════════════════════════════════════════════

$stageEfficiency = $controller->getStageEfficiencyDetails('2025-12-10', 'evening');

foreach ($stageEfficiency as $stage) {
    echo "المرحلة: " . $stage['name'];
    echo " | القطع: " . $stage['items'];
    echo " | الكفاءة: " . $stage['efficiency'] . "%";
    echo " | الهدر: " . $stage['waste_pct'] . "%";
    echo "\n";
}

// ═══════════════════════════════════════════════════════════════════════════
// مثال 5: الحصول على تسليم الورديات
// ═══════════════════════════════════════════════════════════════════════════

$handovers = $controller->getShiftHandovers('2025-12-10', 'evening');

foreach ($handovers as $handover) {
    echo "من: " . $handover['from_user'];
    echo " → إلى: " . $handover['to_user'];
    echo " | المرحلة: " . $handover['stage_name'];
    echo " | عدد القطع: " . $handover['items_count'];
    echo " | الوقت: " . $handover['handover_time']->format('H:i');
    echo " | معتمد: " . ($handover['supervisor_approved'] ? 'نعم' : 'لا');
    echo "\n";
}

// ═══════════════════════════════════════════════════════════════════════════
// مثال 6: الحصول على الفرق النشطة
// ═══════════════════════════════════════════════════════════════════════════

$activeTeams = $controller->getActiveTeams('2025-12-10', 'evening');

foreach ($activeTeams as $team) {
    echo "الفريق: " . $team['team_name'];
    echo " | الأعضاء النشطين: " . $team['active_members'] . "/" . $team['total_members'];
    echo " | الإنتاج الكلي: " . $team['total_production'] . " قطعة";
    echo "\n";
}

// ═══════════════════════════════════════════════════════════════════════════
// مثال 7: الكشف عن المشاكل والتنبيهات
// ═══════════════════════════════════════════════════════════════════════════

$issues = $controller->getShiftIssues('2025-12-10', 'evening');

if (count($issues) > 0) {
    echo "تم العثور على " . count($issues) . " مشكلة/تنبيه:\n";

    foreach ($issues as $issue) {
        $severity = $issue['severity'];
        $icon = ($severity == 'warning') ? '⚠️' : 'ℹ️';
        echo $icon . " " . $issue['message'] . "\n";
    }
} else {
    echo "✓ لا توجد مشاكل في هذه الوردية\n";
}

// ═══════════════════════════════════════════════════════════════════════════
// مثال 8: المقارنة مع الوردية السابقة
// ═══════════════════════════════════════════════════════════════════════════

$comparison = $controller->getShiftComparison('2025-12-10', 'evening');

echo "القطع: ";
echo $comparison['items_change']['value'] . "%";
echo " (" . $comparison['items_change']['direction'] . ")";
echo "\n";

echo "الإنتاج: ";
echo $comparison['output_change']['value'] . "%";
echo " (" . $comparison['output_change']['direction'] . ")";
echo "\n";

echo "الهدر: ";
echo $comparison['waste_change']['value'] . "%";
echo " (" . $comparison['waste_change']['direction'] . ")";
echo "\n";

echo "الكفاءة: ";
echo $comparison['efficiency_change']['value'] . "%";
echo " (" . $comparison['efficiency_change']['direction'] . ")";
echo "\n";

// ═══════════════════════════════════════════════════════════════════════════
// مثال 9: استخدام الأمر في الـ CLI
// ═══════════════════════════════════════════════════════════════════════════

/*
 * قم بتشغيل الأمر في Terminal:
 *
 * php artisan shift:verify-data 2025-12-10 evening
 *
 * سيعرض لك:
 * - ملخص الوردية
 * - أفضل الأداء
 * - حضور العمال
 * - كفاءة المراحل
 * - والمزيد...
 */

// ═══════════════════════════════════════════════════════════════════════════
// مثال 10: في Route API
// ═══════════════════════════════════════════════════════════════════════════

/*
 * في ملف الـ routes:
 *
 * Route::get('api/shift-summary', function () {
 *     $controller = app(ShiftDashboardController::class);
 *     $summary = $controller->getShiftSummary(
 *         request('date', now()->format('Y-m-d')),
 *         request('shift', 'evening')
 *     );
 *     return response()->json($summary);
 * });
 *
 * ثم يمكنك استدعاء:
 * http://localhost/api/shift-summary?date=2025-12-10&shift=evening
 */

// ═══════════════════════════════════════════════════════════════════════════
// مثال 11: معالجة الأخطاء الآمنة
// ═══════════════════════════════════════════════════════════════════════════

try {
    $summary = $controller->getShiftSummary('2025-12-10', 'evening');

    // التحقق من وجود البيانات
    if ($summary['total_items'] == 0) {
        echo "لا توجد بيانات إنتاج لهذه الوردية";
    } else {
        echo "الإجمالي: " . $summary['total_items'] . " قطعة";
    }

} catch (Exception $e) {
    echo "حدث خطأ: " . $e->getMessage();
}

// ═══════════════════════════════════════════════════════════════════════════
// مثال 12: تحويل البيانات إلى CSV
// ═══════════════════════════════════════════════════════════════════════════

function exportAttendanceToCSV($date, $shift, $filename = 'attendance.csv') {
    $controller = app(ShiftDashboardController::class);
    $attendance = $controller->getWorkerAttendance($date, $shift);

    $csv = fopen($filename, 'w');

    // كتابة الرؤوس
    fputcsv($csv, ['الاسم', 'الكود', 'الوظيفة', 'القطع', 'الإنتاج', 'الهدر', 'الكفاءة']);

    // كتابة البيانات
    foreach ($attendance as $worker) {
        fputcsv($csv, [
            $worker['worker_name'],
            $worker['worker_code'],
            $worker['position'],
            $worker['total_items'],
            $worker['total_output'],
            $worker['total_waste'],
            $worker['efficiency'],
        ]);
    }

    fclose($csv);
    return $filename;
}

// استخدام الدالة:
// exportAttendanceToCSV('2025-12-10', 'evening');

// ═══════════════════════════════════════════════════════════════════════════
// مثال 13: عرض الإحصائيات في Dashboard
// ═══════════════════════════════════════════════════════════════════════════

function getQuickStats($date = null, $shift = null) {
    $controller = app(ShiftDashboardController::class);
    $date = $date ?? now()->format('Y-m-d');
    $shift = $shift ?? 'evening';

    return [
        'summary' => $controller->getShiftSummary($date, $shift),
        'top_performers' => $controller->getTopPerformers($date, $shift, 3),
        'wip_count' => $controller->getWIPCount(),
        'active_teams_count' => count($controller->getActiveTeams($date, $shift)),
        'issues_count' => count($controller->getShiftIssues($date, $shift)),
    ];
}

$stats = getQuickStats();
echo json_encode($stats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// ═══════════════════════════════════════════════════════════════════════════
// مثال 14: التحقق من جودة البيانات
// ═══════════════════════════════════════════════════════════════════════════

function validateShiftData($date, $shift) {
    $controller = app(ShiftDashboardController::class);
    $summary = $controller->getShiftSummary($date, $shift);

    $validation = [
        'has_data' => $summary['total_items'] > 0,
        'efficiency_ok' => $summary['efficiency'] > 80,
        'waste_acceptable' => $summary['waste_percentage'] < 10,
        'workers_active' => $summary['workers_count'] > 0,
        'quality_score' => calculateQualityScore($summary),
    ];

    return $validation;
}

function calculateQualityScore($summary) {
    $score = 100;

    // تقليل النقاط بناءً على الهدر
    if ($summary['waste_percentage'] > 5) {
        $score -= 10;
    }

    // تقليل النقاط بناءً على الكفاءة
    if ($summary['efficiency'] < 90) {
        $score -= 15;
    }

    return max(0, $score);
}

$validation = validateShiftData('2025-12-10', 'evening');
echo "جودة البيانات: ";
echo $validation['quality_score'] . "/100";

// ═══════════════════════════════════════════════════════════════════════════
// مثال 15: مراقبة الأداء في الوقت الفعلي
// ═══════════════════════════════════════════════════════════════════════════

/*
 * يمكنك استخدام AJAX لتحديث البيانات كل دقيقة:
 *
 * <script>
 *     setInterval(function() {
 *         $.get('/manufacturing/reports/shift-dashboard/live-stats', {
 *             date: '2025-12-10',
 *             shift_type: 'evening'
 *         }, function(response) {
 *             // تحديث الصفحة بالبيانات الجديدة
 *             updateDashboard(response.data);
 *         });
 *     }, 60000); // كل دقيقة
 * </script>
 */

// ═══════════════════════════════════════════════════════════════════════════
// ملاحظات مهمة:
// ═══════════════════════════════════════════════════════════════════════════

/*
 * 1. جميع الدوال تعيد بيانات آمنة (بدون NULL errors)
 * 2. القيم الافتراضية موجودة لجميع الحالات
 * 3. يمكنك استخدام try-catch لمعالجة الأخطاء
 * 4. البيانات منسقة وجاهزة للعرض
 * 5. جميع الأرقام مستديرة إلى عددين عشريين
 * 6. النصوص الطويلة قد تكون مختصرة
 * 7. يمكنك إضافة filters إضافية حسب الحاجة
 * 8. الأداء محسّن مع eager loading
 */

// ═══════════════════════════════════════════════════════════════════════════
