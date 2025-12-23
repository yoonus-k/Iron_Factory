<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== فحص التوقيت ===\n\n";

echo "1. توقيت PHP:\n";
echo "   - date_default_timezone_get(): " . date_default_timezone_get() . "\n";
echo "   - date('Y-m-d H:i:s'): " . date('Y-m-d H:i:s') . "\n\n";

echo "2. توقيت Laravel:\n";
echo "   - config('app.timezone'): " . config('app.timezone') . "\n";
echo "   - now(): " . now() . "\n";
echo "   - now()->timezone: " . now()->timezone . "\n\n";

echo "3. توقيت الرياض (Asia/Riyadh):\n";
$riyadhTime = now()->timezone('Asia/Riyadh');
echo "   - الوقت: " . $riyadhTime->format('Y-m-d H:i:s') . "\n";
echo "   - الساعة: " . $riyadhTime->format('h:i A') . "\n\n";

echo "4. توقيت UTC:\n";
$utcTime = now()->timezone('UTC');
echo "   - الوقت: " . $utcTime->format('Y-m-d H:i:s') . "\n\n";

echo "5. الفرق بين التوقيتين:\n";
echo "   - الفرق بالساعات: " . now()->diffInHours($riyadhTime, false) . "\n";
