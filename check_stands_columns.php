<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== فحص جدول stands ===\n\n";

$columns = Schema::getColumnListing('stands');
echo "الأعمدة الموجودة:\n";
foreach ($columns as $col) {
    echo "  - $col\n";
}

echo "\n=== التحقق من أعمدة المزامنة ===\n";
$syncColumns = ['local_id', 'is_synced', 'sync_status', 'device_id', 'synced_at'];
foreach ($syncColumns as $col) {
    $exists = Schema::hasColumn('stands', $col);
    echo ($exists ? "✅" : "❌") . " $col: " . ($exists ? "موجود" : "غير موجود") . "\n";
}
