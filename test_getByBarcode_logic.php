<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\ProductionConfirmation;

echo "=== محاكاة Logic من Stage2Controller::getByBarcode ===\n\n";

$barcode = 'ST1-2025-007';

echo "الباركود المطلوب: {$barcode}\n\n";

// الخطوة 1: البحث عن stage1_stands
echo "1️⃣ البحث في stage1_stands...\n";
$stage1Data = DB::table('stage1_stands')
    ->where('barcode', $barcode)
    ->first();

if (!$stage1Data) {
    echo "   ❌ غير موجود في stage1_stands\n";
    exit;
}

echo "   ✅ موجود - ID: {$stage1Data->id}\n\n";

// الخطوة 2: التحقق من pending_approval
echo "2️⃣ التحقق من حالة الاستاند...\n";
if ($stage1Data->status === 'pending_approval') {
    echo "   ⛔ الحالة: pending_approval\n";
    echo "   📢 الرسالة: هذا الاستاند في انتظار الموافقة ولا يمكن استخدامه\n";
    exit;
}
echo "   ✅ الحالة: {$stage1Data->status}\n\n";

// الخطوة 3: التحقق من ProductionConfirmation المعلق
echo "3️⃣ التحقق من ProductionConfirmation معلق...\n";
$pendingConfirmation = ProductionConfirmation::where('barcode', $stage1Data->barcode)
    ->where('status', 'pending')
    ->first();

if ($pendingConfirmation) {
    echo "   ⛔ يوجد confirmation معلق!\n";
    echo "   📋 Confirmation ID: {$pendingConfirmation->id}\n";
    echo "   👤 المسند إليه: User #{$pendingConfirmation->assigned_to}\n";
    echo "   📢 الرسالة التي ستظهر:\n";
    echo "   ⛔ هذا الباركود معاد إسناده ويحتاج موافقة من العامل المسند إليه أولاً\n\n";
    echo "✅ الvalidation سيعمل بشكل صحيح!\n";
    echo "✅ Status Code: 403\n";
    echo "✅ blocked: true\n";
} else {
    echo "   ✅ لا يوجد confirmation معلق\n";
    echo "   ✅ يمكن استخدام الباركود بشكل طبيعي\n";
}
