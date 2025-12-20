<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Material;
use App\Models\PendingSync;

// إنشاء material تجريبي
$material = Material::create([
    'material_code' => 'TEST-SYNC-' . time(),
    'name' => 'Test Sync Material',
    'name_en' => 'Test Sync',
    'unit_id' => 1,
    'supplier_id' => 1,
]);

echo "✅ Material created: ID = {$material->id}\n";
echo "   is_synced = " . ($material->is_synced ? 'true' : 'false') . "\n";
echo "   sync_status = {$material->sync_status}\n\n";

// التحقق من pending_syncs
$pendingCount = PendingSync::where('entity_type', 'materials')->count();
echo "📊 Total pending materials: {$pendingCount}\n";

// آخر 3 عمليات معلقة
$recent = PendingSync::latest()->limit(3)->get(['id', 'entity_type', 'action', 'status', 'created_at']);
echo "\n📋 Last 3 pending syncs:\n";
foreach ($recent as $sync) {
    echo "   - [{$sync->id}] {$sync->entity_type} -> {$sync->action} ({$sync->status})\n";
}
