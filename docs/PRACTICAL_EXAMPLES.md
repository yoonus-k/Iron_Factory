<!-- 
أمثلة عملية على استخدام نظام تقرير الورديات
===================================================

هذا الملف يحتوي على أمثلة عملية لاستخدام نظام تقرير الورديات
ويمكنك نسخ الأكواد واستخدامها مباشرة
-->

# أمثلة عملية على استخدام نظام تقرير الورديات

## مثال 1: الحصول على ملخص الوردية

```php
use Modules\Manufacturing\Http\Controllers\ShiftDashboardController;

$controller = app(ShiftDashboardController::class);

// الحصول على ملخص الوردية المسائية ليوم 2025-12-10
$summary = $controller->getShiftSummary('2025-12-10', 'evening');

echo "إجمالي القطع: " . $summary['total_items'];
echo "الإنتاج: " . $summary['total_output_kg'] . " كجم";
echo "الهدر: " . $summary['total_waste_kg'] . " كجم";
echo "الكفاءة: " . $summary['efficiency'] . "%";
```

## مثال 2: الحصول على أفضل أداء

```php
// الحصول على أفضل 5 عمال
$topPerformers = $controller->getTopPerformers('2025-12-10', 'evening', 5);

foreach ($topPerformers as $index => $performer) {
    echo ($index + 1) . ". " . $performer['worker_name'];
    echo " - " . $performer['items'] . " قطعة";
    echo " - كفاءة: " . $performer['efficiency'] . "%";
}
```

## مثال 3: الحصول على حضور العمال

```php
$attendance = $controller->getWorkerAttendance('2025-12-10', 'evening');

// عرض جدول بحضور العمال
foreach ($attendance as $worker) {
    echo "الاسم: " . $worker['worker_name'];
    echo " | الكود: " . $worker['worker_code'];
    echo " | القطع: " . $worker['total_items'];
    echo " | الكفاءة: " . $worker['efficiency'] . "%";
    echo "\n";
}
```

## مثال 4: الحصول على كفاءة المراحل

```php
$stageEfficiency = $controller->getStageEfficiencyDetails('2025-12-10', 'evening');

foreach ($stageEfficiency as $stage) {
    echo "المرحلة: " . $stage['name'];
    echo " | القطع: " . $stage['items'];
    echo " | الكفاءة: " . $stage['efficiency'] . "%";
    echo " | الهدر: " . $stage['waste_pct'] . "%";
    echo "\n";
}
```

## مثال 5: الحصول على تسليم الورديات

```php
$handovers = $controller->getShiftHandovers('2025-12-10', 'evening');

foreach ($handovers as $handover) {
    echo "من: " . $handover['from_user'];
    echo " إلى: " . $handover['to_user'];
    echo " | المرحلة: " . $handover['stage_name'];
    echo " | عدد القطع: " . $handover['items_count'];
    echo "\n";
}
```

## مثال 6: الحصول على الفرق النشطة

```php
$activeTeams = $controller->getActiveTeams('2025-12-10', 'evening');

foreach ($activeTeams as $team) {
    echo "الفريق: " . $team['team_name'];
    echo " | الأعضاء النشطين: " . $team['active_members'];
    echo " | الإنتاج الكلي: " . $team['total_production'];
    echo "\n";
}
```

## مثال 7: الكشف عن المشاكل والتنبيهات

```php
$issues = $controller->getShiftIssues('2025-12-10', 'evening');

if (count($issues) > 0) {
    foreach ($issues as $issue) {
        echo $issue['message'] . "\n";
    }
} else {
    echo "لا توجد مشاكل في هذه الوردية\n";
}
```

## مثال 8: استخدام أمر التحقق

```bash
php artisan shift:verify-data 2025-12-10 evening
```

## مثال 9: استخدام Tinker

```bash
php artisan tinker

# ثم:
$controller = app(\Modules\Manufacturing\Http\Controllers\ShiftDashboardController::class);
$summary = $controller->getShiftSummary('2025-12-10', 'evening');
dd($summary);
```

## مثال 10: في الـ API

```php
Route::get('api/shift-summary', function () {
    $controller = app(ShiftDashboardController::class);
    $summary = $controller->getShiftSummary(
        request('date', now()->format('Y-m-d')),
        request('shift', 'evening')
    );
    return response()->json($summary);
});
```

## مثال 11: معالجة الأخطاء

```php
try {
    $summary = $controller->getShiftSummary('2025-12-10', 'evening');
    
    if ($summary['total_items'] == 0) {
        echo "لا توجد بيانات إنتاج";
    } else {
        echo "الإجمالي: " . $summary['total_items'] . " قطعة";
    }
} catch (Exception $e) {
    echo "حدث خطأ: " . $e->getMessage();
}
```

## مثال 12: تصدير إلى CSV

```php
function exportAttendanceToCSV($date, $shift) {
    $controller = app(ShiftDashboardController::class);
    $attendance = $controller->getWorkerAttendance($date, $shift);
    
    $csv = fopen('attendance.csv', 'w');
    
    // كتابة الرؤوس
    fputcsv($csv, ['الاسم', 'الكود', 'القطع', 'الكفاءة']);
    
    // كتابة البيانات
    foreach ($attendance as $worker) {
        fputcsv($csv, [
            $worker['worker_name'],
            $worker['worker_code'],
            $worker['total_items'],
            $worker['efficiency'],
        ]);
    }
    
    fclose($csv);
}

exportAttendanceToCSV('2025-12-10', 'evening');
```

## ملاحظات مهمة:

✅ جميع الدوال تعيد بيانات آمنة (بدون NULL errors)
✅ القيم الافتراضية موجودة لجميع الحالات  
✅ يمكنك استخدام try-catch لمعالجة الأخطاء
✅ البيانات منسقة وجاهزة للعرض
✅ جميع الأرقام مستديرة إلى عددين عشريين
✅ الأداء محسّن مع eager loading
✅ جميع البيانات قد تحتوي على قيم NULL تم معالجتها بآمان
