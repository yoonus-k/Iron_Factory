<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Stand;
use App\Models\PendingSync;
use Illuminate\Support\Facades\Auth;

echo "=== اختبار مزامنة Stand ===\n\n";

// تسجيل دخول وهمي للاختبار
Auth::loginUsingId(1);

// عدد العناصر المعلقة قبل
$pendingBefore = PendingSync::where('entity_type', 'stands')->count();
echo "العناصر المعلقة قبل الإنشاء: $pendingBefore\n";

// إنشاء استاند جديد
echo "\n📦 إنشاء استاند جديد...\n";
$stand = Stand::create([
    'stand_number' => 'TEST-' . time(),
    'weight' => 25.50,
    'status' => 'unused',
    'notes' => 'استاند تجريبي للمزامنة',
    'is_active' => true,
]);

echo "✅ تم إنشاء الاستاند! ID: {$stand->id}, Stand Number: {$stand->stand_number}\n";
echo "   - local_id: {$stand->local_id}\n";
echo "   - is_synced: " . ($stand->is_synced ? 'نعم' : 'لا') . "\n";
echo "   - sync_status: {$stand->sync_status}\n";

// عدد العناصر المعلقة بعد
$pendingAfter = PendingSync::where('entity_type', 'stands')->count();
echo "\nالعناصر المعلقة بعد الإنشاء: $pendingAfter\n";

if ($pendingAfter > $pendingBefore) {
    echo "\n✅ نجح! تمت إضافة الاستاند لقائمة المزامنة المعلقة!\n";
    
    // عرض العنصر المعلق
    $pending = PendingSync::where('entity_type', 'stands')
        ->where('entity_id', $stand->id)
        ->first();
    
    if ($pending) {
        echo "\n📋 تفاصيل العنصر المعلق:\n";
        echo "   - ID: {$pending->id}\n";
        echo "   - Entity Type: {$pending->entity_type}\n";
        echo "   - Entity ID: {$pending->entity_id}\n";
        echo "   - Action: {$pending->action}\n";
        echo "   - Status: {$pending->status}\n";
        echo "   - Priority: {$pending->priority}\n";
    }
} else {
    echo "\n❌ فشل! لم تتم إضافة الاستاند لقائمة المزامنة.\n";
    
    // البحث عن السبب
    echo "\n🔍 فحص Syncable trait...\n";
    $shouldSync = $stand->shouldSync();
    echo "   - shouldSync(): " . ($shouldSync ? 'نعم' : 'لا') . "\n";
}

// تنظيف - حذف الاستاند التجريبي
echo "\n🧹 حذف الاستاند التجريبي...\n";
$stand->forceDelete();
echo "✅ تم الحذف!\n";
