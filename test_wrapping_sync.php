<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// تسجيل دخول مستخدم للاختبار
$user = \App\Models\User::first();
auth()->login($user);

$wrapping = new \App\Models\Wrapping();
$wrapping->wrapping_number = 'TEST-' . time();
$wrapping->weight = 25.5;
$wrapping->description = 'Test sync wrapping - created at ' . now();
$wrapping->is_active = true;
$wrapping->save();

echo "✅ Wrapping created: {$wrapping->wrapping_number}\n";
echo "ID: {$wrapping->id}\n";
