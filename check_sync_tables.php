<?php
/**
 * سكربت للتحقق من تغطية جداول المزامنة
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== فحص جداول المزامنة ===\n\n";

// جلب جميع الجداول من قاعدة البيانات
$dbTables = collect(DB::select('SHOW TABLES'))
    ->map(fn($table) => array_values((array) $table)[0])
    ->toArray();

echo "📊 عدد الجداول في قاعدة البيانات: " . count($dbTables) . "\n\n";

// جلب الجداول من SyncService
$syncService = new \App\Services\SyncService();
$reflection = new ReflectionClass($syncService);
$method = $reflection->getMethod('getSyncableTables');
$method->setAccessible(true);
$syncTables = array_keys($method->invoke($syncService));

echo "✅ عدد الجداول في SyncService: " . count($syncTables) . "\n\n";

// الجداول المشمولة
$covered = array_intersect($dbTables, $syncTables);
echo "🔗 الجداول المشمولة في المزامنة: " . count($covered) . "\n";

// الجداول غير المشمولة
$notCovered = array_diff($dbTables, $syncTables);
echo "⚠️  الجداول غير المشمولة: " . count($notCovered) . "\n";

if (!empty($notCovered)) {
    echo "\n📋 قائمة الجداول غير المشمولة:\n";
    foreach ($notCovered as $table) {
        echo "   - $table\n";
    }
}

// التحقق من أن Models موجودة
echo "\n\n=== التحقق من صحة Models ===\n";
$errors = [];
foreach ($method->invoke($syncService) as $table => $modelClass) {
    if (!class_exists($modelClass)) {
        $errors[] = "❌ Model غير موجود: $modelClass (للجدول: $table)";
    }
}

if (empty($errors)) {
    echo "✅ جميع Models موجودة وصحيحة!\n";
} else {
    foreach ($errors as $error) {
        echo "$error\n";
    }
}

echo "\n=== انتهى الفحص ===\n";
