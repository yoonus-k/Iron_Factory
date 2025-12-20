<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== اختبار الاتصال بالسيرفر الأونلاين ===\n\n";

$centralService = app(\App\Services\CentralServerService::class);

// اختبار الاتصال
echo "1. اختبار الاتصال...\n";
$connectionTest = $centralService->test();
echo "   Connection: " . ($connectionTest['connection'] ? '✅' : '❌') . "\n";
echo "   Authentication: " . ($connectionTest['authentication'] ? '✅' : '❌') . "\n";
echo "   Push: " . ($connectionTest['push'] ? '✅' : '❌') . "\n";
echo "   Pull: " . ($connectionTest['pull'] ? '✅' : '❌') . "\n\n";

// اختبار push customer
echo "2. اختبار push customer...\n";
try {
    $testData = [[
        'entity_type' => 'customers',
        'action' => 'create',
        'data' => [
            'name' => 'Test Customer',
            'company_name' => 'Test Company',
            'phone' => '1234567890',
            'email' => 'test@example.com',
            'address' => 'Test',
            'city' => 'Test',
            'is_active' => true,
        ],
        'local_id' => 'test-' . uniqid(),
    ]];
    
    $result = $centralService->push($testData);
    echo "   Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
