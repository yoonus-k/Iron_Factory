<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$conf = \App\Models\ProductionConfirmation::find(3);
$conf->update([
    'status' => 'pending',
    'confirmed_by' => null,
    'confirmed_at' => null
]);

echo "✅ تم إرجاع حالة confirmation إلى pending للاختبار\n";
echo "   ID: {$conf->id}\n";
echo "   Status: {$conf->status}\n";
echo "   Barcode: {$conf->barcode}\n";
