<?php
/**
 * ملف اختبار بسيط للتحقق من Routes على السيرفر
 * ارفع هذا الملف إلى مجلد public على السيرفر الأونلاين
 * ثم افتح: https://hitstest.sehoool.com/test-sync.php
 */

header('Content-Type: application/json');

// اختبار 1: التحقق من Laravel
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    echo json_encode([
        'test' => 'Laravel Bootstrap',
        'status' => 'SUCCESS',
        'message' => 'Laravel يعمل بشكل صحيح'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'test' => 'Laravel Bootstrap',
        'status' => 'FAILED',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
