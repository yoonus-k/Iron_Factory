<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$perms = DB::table('permissions')->select('id', 'permission_code', 'permission_name_en')->orderBy('permission_name_en')->get();
echo "Current permissions in database:\n";
echo "================================\n";
foreach($perms as $p) {
    echo $p->permission_code . ' => ' . $p->permission_name_en . "\n";
}
echo "\nTotal: " . count($perms) . " permissions\n";
