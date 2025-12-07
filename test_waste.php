<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== فحص نظام الهدر ===\n\n";

// 1. فحص نسبة الهدر المسموح بها
$allowedPercentage = App\Helpers\SystemSettingsHelper::getProductionWastePercentage();
echo "النسبة المسموح بها: {$allowedPercentage}%\n";
echo "التنبيهات مفعلة: " . (App\Helpers\SystemSettingsHelper::isWasteAlertsEnabled() ? 'نعم' : 'لا') . "\n";
echo "الإيقاف التلقائي مفعل: " . (App\Helpers\SystemSettingsHelper::isAutoSuspendEnabled() ? 'نعم' : 'لا') . "\n\n";

// 2. فحص آخر سجل في stage1_stands
$lastRecord = DB::table('stage1_stands')->orderBy('id', 'desc')->first();

if ($lastRecord) {
    echo "آخر سجل في stage1_stands:\n";
    echo "ID: {$lastRecord->id}\n";
    echo "Parent Barcode: {$lastRecord->parent_barcode}\n";
    echo "Total Weight: {$lastRecord->weight} كجم\n";
    echo "Waste: {$lastRecord->waste} كجم\n";
    echo "Net Weight: {$lastRecord->remaining_weight} كجم\n";
    
    $materialWeight = $lastRecord->remaining_weight + $lastRecord->waste;
    $wastePercent = $materialWeight > 0 ? round(($lastRecord->waste / $materialWeight) * 100, 2) : 0;
    
    echo "الوزن المادي (بدون الاستاند): {$materialWeight} كجم\n";
    echo "نسبة الهدر: {$wastePercent}%\n";
    echo "تجاوز الحد؟ " . ($wastePercent > $allowedPercentage ? 'نعم ❌' : 'لا ✅') . "\n\n";
    
    // 3. فحص الإجمالي لنفس الباركود
    echo "إجمالي البيانات لنفس المادة ({$lastRecord->parent_barcode}):\n";
    $totals = DB::table('stage1_stands')
        ->where('parent_barcode', $lastRecord->parent_barcode)
        ->selectRaw('
            COUNT(*) as total_stands,
            SUM(remaining_weight) as total_net,
            SUM(waste) as total_waste,
            SUM(remaining_weight + waste) as total_material
        ')
        ->first();
    
    echo "عدد الاستاندات: {$totals->total_stands}\n";
    echo "إجمالي الوزن المادي: {$totals->total_material} كجم\n";
    echo "إجمالي الوزن الصافي: {$totals->total_net} كجم\n";
    echo "إجمالي الهدر: {$totals->total_waste} كجم\n";
    
    $totalWastePercent = $totals->total_material > 0 
        ? round(($totals->total_waste / $totals->total_material) * 100, 2) 
        : 0;
    
    echo "نسبة الهدر الإجمالية: {$totalWastePercent}%\n";
    echo "تجاوز الحد الإجمالي؟ " . ($totalWastePercent > $allowedPercentage ? 'نعم ❌' : 'لا ✅') . "\n\n";
    
    // 4. فحص ما إذا كانت المرحلة موقوفة
    $isSuspended = App\Services\WasteCheckService::isStageSuspended($lastRecord->parent_barcode);
    echo "المرحلة موقوفة؟ " . ($isSuspended ? 'نعم' : 'لا') . "\n";
    
    if ($isSuspended) {
        $suspension = App\Services\WasteCheckService::getActiveSuspension($lastRecord->parent_barcode);
        if ($suspension) {
            echo "سبب الإيقاف: {$suspension->suspension_reason}\n";
        }
    }
    
} else {
    echo "لا توجد بيانات في جدول stage1_stands\n";
}

echo "\n=== انتهى الفحص ===\n";
