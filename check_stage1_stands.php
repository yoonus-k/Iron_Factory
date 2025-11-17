<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== فحص جدول stage1_stands ===\n\n";

try {
    $columns = DB::select("SHOW COLUMNS FROM stage1_stands");

    echo "الأعمدة المتاحة:\n";
    echo "================\n\n";

    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n\nعدد السجلات: " . DB::table('stage1_stands')->count() . "\n";
} catch (\Exception $e) {
    echo "خطأ: " . $e->getMessage() . "\n";
}
