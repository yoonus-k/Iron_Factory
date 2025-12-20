<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$centralService = app(\App\Services\CentralServerService::class);

echo "═══════════════════════════════════════════════════════════\n";
echo "🔄 اختبار الاتصال بالسيرفر الأون لاين\n";
echo "═══════════════════════════════════════════════════════════\n\n";

$result = $centralService->test();

echo "\n═══════════════════════════════════════════════════════════\n";
echo "النتيجة:\n";
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "\n═══════════════════════════════════════════════════════════\n";

if ($result['success']) {
    echo "\n✅ الاتصال نجح! يمكنك الآن البدء بالمزامنة\n";
} else {
    echo "\n❌ فشل الاتصال! تحقق من الـ Token في ملف .env\n";
}
