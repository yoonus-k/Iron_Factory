<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\ProductionConfirmation;

echo "=== اختبار Validation في Stage2 ===\n\n";

// محاكاة ما يحدث في Stage2Controller
$stage1Barcode = 'ST1-2025-007';

echo "1️⃣ البحث عن stage1_stands بالباركود: {$stage1Barcode}\n";
$stage1Data = DB::table('stage1_stands')
    ->where('barcode', $stage1Barcode)
    ->first();

if (!$stage1Data) {
    echo "   ❌ الباركود غير موجود\n";
    exit;
}

echo "   ✅ موجود - ID: {$stage1Data->id}, Barcode: {$stage1Data->barcode}\n\n";

echo "2️⃣ البحث عن ProductionConfirmation pending لهذا الباركود:\n";
echo "   SQL: SELECT * FROM production_confirmations \n";
echo "        WHERE barcode = '{$stage1Data->barcode}' \n";
echo "        AND status = 'pending'\n\n";

$pendingConfirmation = ProductionConfirmation::where('barcode', $stage1Data->barcode)
    ->where('status', 'pending')
    ->first();

if ($pendingConfirmation) {
    echo "   ✅ يوجد confirmation معلق!\n";
    echo "      - ID: {$pendingConfirmation->id}\n";
    echo "      - Status: {$pendingConfirmation->status}\n";
    echo "      - Barcode: {$pendingConfirmation->barcode}\n";
    echo "      - Assigned To: User #{$pendingConfirmation->assigned_to}\n";
    echo "\n   ❌ يجب أن يُمنع الاستخدام!\n";
} else {
    echo "   ⚠️ لا يوجد confirmation معلق\n";
    echo "   ✅ يمكن استخدام الباركود\n";
}

echo "\n";
echo "3️⃣ فحص جميع الconfirmations لهذا الباركود:\n";
$allConfs = ProductionConfirmation::where('barcode', $stage1Data->barcode)->get();
foreach ($allConfs as $conf) {
    echo "   - Confirmation ID {$conf->id}: status = {$conf->status}, barcode = " . ($conf->barcode ?? 'NULL') . "\n";
}
