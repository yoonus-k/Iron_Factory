<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0');
DB::table('role_permissions')->delete();
DB::table('permissions')->delete();
DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "✅ تم حذف جميع البيانات\n";
