<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== All Material Types ===" . PHP_EOL;
$types = DB::table('material_types')->get();
foreach($types as $t) {
    echo $t->id . ' | ' . $t->type_code . ' | ' . $t->type_name . PHP_EOL;
}

echo PHP_EOL . "=== All Materials with Types ===" . PHP_EOL;
$materials = DB::table('materials')
    ->leftJoin('material_types', 'materials.material_type_id', '=', 'material_types.id')
    ->select('materials.id', 'materials.name_ar', 'materials.status', 'material_types.type_code', 'materials.material_type_id')
    ->get();
    
foreach($materials as $m) {
    echo $m->id . ' | ' . ($m->name_ar ?? 'NULL') . ' | ' . ($m->status ?? 'NULL') . ' | ' . ($m->type_code ?? 'NULL') . ' | type_id: ' . ($m->material_type_id ?? 'NULL') . PHP_EOL;
}

echo PHP_EOL . "=== Material Details (stock) ===" . PHP_EOL;
$details = DB::table('material_details')
    ->join('materials', 'material_details.material_id', '=', 'materials.id')
    ->select('material_details.material_id', 'materials.name_ar', 'material_details.quantity', 'material_details.remaining_weight')
    ->where('material_details.quantity', '>', 0)
    ->orWhere('material_details.remaining_weight', '>', 0)
    ->get();
    
foreach($details as $d) {
    echo $d->material_id . ' | ' . ($d->name_ar ?? 'NULL') . ' | qty: ' . $d->quantity . ' | remaining: ' . $d->remaining_weight . PHP_EOL;
}
