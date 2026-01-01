<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Stage3 Coils Table Columns ===\n\n";

$columns = Schema::getColumnListing('stage3_coils');
print_r($columns);

echo "\n\n=== Checking Transfer Columns ===\n";
$transferColumns = ['transferred_from', 'transferred_to', 'transfer_status', 'transfer_reason', 'transfer_notes'];

foreach ($transferColumns as $col) {
    $exists = Schema::hasColumn('stage3_coils', $col);
    echo "$col: " . ($exists ? "✅ EXISTS" : "❌ MISSING") . "\n";
}
