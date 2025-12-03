<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== المراحل الموقوفة (stage_suspensions) ===\n\n";

$suspensions = \App\Models\StageSuspension::orderBy('id', 'desc')->get();

if ($suspensions->isEmpty()) {
    echo "❌ لا توجد سجلات\n";
} else {
    foreach ($suspensions as $s) {
        $statusIcon = $s->status === 'suspended' ? '🔴' : ($s->status === 'approved' ? '✅' : '⚪');
        echo "{$statusIcon} ID: {$s->id} | Stage: {$s->stage_number} | Batch: {$s->batch_barcode} | Waste: {$s->waste_percentage}% | Status: {$s->status} | Created: {$s->created_at}\n";
    }
}

echo "\n=== السجلات في stage1_stands مع status=pending_approval ===\n\n";

$pendingStands = \DB::table('stage1_stands')
    ->where('status', 'pending_approval')
    ->orderBy('id', 'desc')
    ->get();

if ($pendingStands->isEmpty()) {
    echo "❌ لا توجد سجلات بحالة pending_approval\n";
} else {
    foreach ($pendingStands as $stand) {
        echo "⏸️ ID: {$stand->id} | Barcode: {$stand->barcode} | Parent: {$stand->parent_barcode} | Status: {$stand->status}\n";
    }
}

echo "\n";
