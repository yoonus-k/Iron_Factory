<?php
/**
 * اختبار عمل SyncService
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\SyncService;
use Illuminate\Support\Facades\DB;

echo "=== اختبار SyncService ===\n\n";

$syncService = new SyncService();

// 1. اختبار getSyncStats
echo "1️⃣ اختبار getSyncStats...\n";
try {
    $stats = $syncService->getSyncStats(1);
    echo "   ✅ نجح! الإحصائيات:\n";
    echo "      - آخر سحب: " . ($stats['last_pull_at'] ?? 'لا يوجد') . "\n";
    echo "      - آخر رفع: " . ($stats['last_push_at'] ?? 'لا يوجد') . "\n";
    echo "      - معلقة: " . $stats['pending_count'] . "\n";
    echo "      - فاشلة: " . $stats['failed_count'] . "\n";
    echo "      - إجمالي المُزامنة: " . $stats['total_synced'] . "\n";
} catch (Exception $e) {
    echo "   ❌ فشل: " . $e->getMessage() . "\n";
}

// 2. اختبار getModelClass (عبر Reflection)
echo "\n2️⃣ اختبار getModelClass...\n";
$reflection = new ReflectionClass($syncService);
$method = $reflection->getMethod('getModelClass');
$method->setAccessible(true);

$testEntities = ['materials', 'users', 'stage1_stands', 'wrappings', 'material_batches'];
foreach ($testEntities as $entity) {
    $modelClass = $method->invoke($syncService, $entity);
    if ($modelClass && class_exists($modelClass)) {
        echo "   ✅ $entity => $modelClass\n";
    } else {
        echo "   ❌ $entity => لم يُعثر عليه!\n";
    }
}

// 3. اختبار pullFromServer
echo "\n3️⃣ اختبار pullFromServer...\n";
try {
    $result = $syncService->pullFromServer(1);
    if ($result['success']) {
        echo "   ✅ نجح!\n";
        echo "      - آخر مزامنة: " . $result['last_sync_time'] . "\n";
        echo "      - إجمالي العناصر: " . $result['total_items'] . "\n";
        if (!empty($result['updates'])) {
            echo "      - الجداول المحدثة:\n";
            foreach ($result['updates'] as $table => $items) {
                echo "         • $table: " . count($items) . " عنصر\n";
            }
        }
    } else {
        echo "   ❌ فشل: " . ($result['error'] ?? 'خطأ غير معروف') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ استثناء: " . $e->getMessage() . "\n";
}

// 4. اختبار إضافة عنصر للقائمة المعلقة
echo "\n4️⃣ اختبار addToPendingQueue...\n";
try {
    $pending = $syncService->addToPendingQueue(
        userId: 1,
        entityType: 'materials',
        action: 'create',
        data: ['name_ar' => 'مادة اختبار', 'name_en' => 'Test Material'],
        priority: 1
    );
    echo "   ✅ تم إضافة عنصر للقائمة المعلقة! ID: " . $pending->id . "\n";
    
    // حذف العنصر الاختباري
    $pending->delete();
    echo "   ✅ تم حذف العنصر الاختباري\n";
} catch (Exception $e) {
    echo "   ❌ فشل: " . $e->getMessage() . "\n";
}

echo "\n=== انتهى الاختبار ===\n";
