<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== فحص بنية جدول warehouse_transactions ===\n\n";

$columns = DB::select("SHOW COLUMNS FROM warehouse_transactions");

echo "الأعمدة المتاحة:\n";
echo "================\n\n";

foreach ($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}
