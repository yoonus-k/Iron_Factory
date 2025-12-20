<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== فحص العمليات الفاشلة ===\n\n";

$failedSyncs = \App\Models\PendingSync::where('status', 'failed')
    ->latest()
    ->limit(5)
    ->get();

if ($failedSyncs->isEmpty()) {
    echo "✅ لا توجد عمليات فاشلة\n";
} else {
    foreach ($failedSyncs as $sync) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "ID: {$sync->id}\n";
        echo "Entity: {$sync->entity_type}\n";
        echo "Action: {$sync->action}\n";
        echo "Error: " . ($sync->error_message ?? 'No error message') . "\n";
        echo "Created: {$sync->created_at}\n";
        echo "Data: " . json_encode($sync->data, JSON_PRETTY_PRINT) . "\n";
    }
}

echo "\n=== اختبار معالجة المزامنة ===\n\n";

// تشغيل المزامنة
$syncService = app(\App\Services\SyncService::class);
$result = $syncService->processPendingSyncs(null, 10);

echo "Processed: {$result['processed']}\n";
echo "Failed: {$result['failed']}\n";
echo "Total: {$result['total']}\n";
