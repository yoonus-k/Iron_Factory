<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Stage Suspensions ===\n\n";

// Check latest suspension
$suspension = \App\Models\StageSuspension::latest()->first();

if ($suspension) {
    echo "✅ Latest Suspension Found:\n";
    echo "   ID: " . $suspension->id . "\n";
    echo "   Stage: " . $suspension->stage_number . "\n";
    echo "   Batch: " . $suspension->batch_barcode . "\n";
    echo "   Waste: " . $suspension->waste_percentage . "%\n";
    echo "   Allowed: " . $suspension->allowed_percentage . "%\n";
    echo "   Status: " . $suspension->status . "\n";
    echo "   Created: " . $suspension->created_at . "\n\n";
} else {
    echo "❌ No suspensions found in database\n\n";
}

// Check system settings
echo "=== System Settings ===\n";
$wastePercentage = \App\Helpers\SystemSettingsHelper::getProductionWastePercentage();
$autoSuspend = \App\Helpers\SystemSettingsHelper::isAutoSuspendEnabled();
$alerts = \App\Helpers\SystemSettingsHelper::isWasteAlertsEnabled();

echo "   Waste Limit: " . $wastePercentage . "%\n";
echo "   Auto Suspend: " . ($autoSuspend ? 'Enabled' : 'Disabled') . "\n";
echo "   Alerts: " . ($alerts ? 'Enabled' : 'Disabled') . "\n\n";

// Check recent stage1 stands with high waste
echo "=== Recent Stage1 Records with High Waste ===\n";
$highWaste = \DB::table('stage1_stands')
    ->selectRaw('id, barcode, parent_barcode, waste, remaining_weight, status, created_at')
    ->selectRaw('CASE WHEN (remaining_weight + waste) > 0 THEN (waste / (remaining_weight + waste) * 100) ELSE 0 END as waste_pct')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($highWaste as $record) {
    $color = $record->waste_pct > $wastePercentage ? '🔴' : '✅';
    echo $color . " ID: {$record->id} | Barcode: {$record->barcode} | Waste: " . round($record->waste_pct, 2) . "% | Status: {$record->status}\n";
}

echo "\n=== Done ===\n";
