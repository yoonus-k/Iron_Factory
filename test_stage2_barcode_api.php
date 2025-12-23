<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

use Illuminate\Http\Request;

echo "=== اختبار Stage2 getByBarcode API ===\n\n";

// محاكاة request لـ getByBarcode
$request = Request::create('/manufacturing/stage2/get-by-barcode/ST1-2025-007', 'GET');

try {
    // استدعاء الـ controller مباشرة
    $controller = new \Modules\Manufacturing\Http\Controllers\Stage2Controller();
    $response = $controller->getByBarcode('ST1-2025-007');
    
    $statusCode = $response->getStatusCode();
    $content = json_decode($response->getContent(), true);
    
    echo "الباركود: ST1-2025-007\n";
    echo "Status Code: {$statusCode}\n";
    echo "Response:\n";
    echo json_encode($content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo "\n\n";
    
    if ($statusCode === 403 && isset($content['blocked']) && $content['blocked']) {
        echo "✅ الvalidation يعمل بشكل صحيح!\n";
        echo "✅ الرسالة: {$content['message']}\n";
    } else {
        echo "⚠️ الvalidation لم يعمل كما هو متوقع\n";
    }
    
} catch (\Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}
