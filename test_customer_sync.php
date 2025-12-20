<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== اختبار مزامنة جدول Customers ===\n\n";

// إنشاء customer جديد
$customer = \App\Models\Customer::create([
    'name' => 'Test Customer Sync ' . time(),
    'company_name' => 'Test Company',
    'phone' => '0123456789',
    'email' => 'test' . time() . '@example.com',
    'address' => 'Test Address',
    'city' => 'Test City',
    'is_active' => true,
    'created_by' => 1
]);

echo "✅ Customer created:\n";
echo "   ID: {$customer->id}\n";
echo "   Name: {$customer->name}\n";
echo "   Sync Status: {$customer->sync_status}\n";
echo "   Is Synced: " . ($customer->is_synced ? 'Yes' : 'No') . "\n";
echo "   Local ID: {$customer->local_id}\n\n";

// فحص pending_syncs
$pendingSyncs = \App\Models\PendingSync::where('entity_type', 'customers')
    ->orWhere('entity_type', 'customer')
    ->latest()
    ->limit(5)
    ->get();

echo "📊 Pending Syncs for Customers (Last 5):\n";
if ($pendingSyncs->isEmpty()) {
    echo "   ⚠️  لا توجد عمليات معلقة!\n\n";
} else {
    foreach ($pendingSyncs as $sync) {
        $error = $sync->last_error ? ' | Error: ' . $sync->last_error : '';
        echo "   - ID: {$sync->id} | Status: {$sync->status} | Action: {$sync->action}{$error}\n";
    }
    echo "\n";
}

// فحص جميع pending_syncs
$allPending = \App\Models\PendingSync::pending()->get();
echo "📋 Total Pending Syncs: " . $allPending->count() . "\n";
if ($allPending->isNotEmpty()) {
    $byType = $allPending->groupBy('entity_type');
    foreach ($byType as $type => $items) {
        echo "   - {$type}: {$items->count()}\n";
    }
}

echo "\n=== انتهى الاختبار ===\n";
