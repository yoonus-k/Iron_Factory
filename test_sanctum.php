<?php
// ارفع هذا الملف على السيرفر الأون لاين في المجلد الرئيسي
// افتحه عبر: https://hitstest.sehoool.com/test_sanctum.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "<h1>اختبار نظام المزامنة على السيرفر الأون لاين</h1>";
echo "<hr>";

// 1. التحقق من Sanctum
echo "<h2>1. Laravel Sanctum</h2>";
if (class_exists('Laravel\Sanctum\Sanctum')) {
    echo "✅ Sanctum مثبت<br>";
} else {
    echo "❌ Sanctum غير مثبت! نفذ: composer require laravel/sanctum<br>";
}

// 2. التحقق من جدول personal_access_tokens
echo "<h2>2. جدول personal_access_tokens</h2>";
try {
    $tokensCount = DB::table('personal_access_tokens')->count();
    echo "✅ الجدول موجود - عدد الـ tokens: {$tokensCount}<br>";
    
    $token = DB::table('personal_access_tokens')->where('id', 3)->first();
    if ($token) {
        echo "✅ Token ID 3 موجود: {$token->name}<br>";
        echo "Token length: " . strlen($token->token) . "<br>";
    }
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "<br>";
}

// 3. التحقق من جداول المزامنة
echo "<h2>3. جداول المزامنة</h2>";
$tables = ['sync_logs', 'sync_histories', 'pending_syncs', 'user_last_syncs'];
foreach ($tables as $table) {
    try {
        $exists = DB::getSchemaBuilder()->hasTable($table);
        if ($exists) {
            echo "✅ {$table}<br>";
        } else {
            echo "❌ {$table} غير موجود<br>";
        }
    } catch (Exception $e) {
        echo "❌ {$table}: {$e->getMessage()}<br>";
    }
}

// 4. التحقق من API Routes
echo "<h2>4. API Routes</h2>";
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$syncRoutes = 0;
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'api/sync')) {
        $syncRoutes++;
        echo "✅ {$route->uri()}<br>";
    }
}
if ($syncRoutes === 0) {
    echo "❌ لا توجد API sync routes!<br>";
    echo "💡 تحقق من ملف routes/api.php و bootstrap/app.php<br>";
}

// 5. التحقق من guard sanctum
echo "<h2>5. Sanctum Guard</h2>";
try {
    $guards = config('auth.guards');
    if (isset($guards['sanctum'])) {
        echo "✅ Sanctum guard موجود<br>";
    } else {
        echo "❌ Sanctum guard غير موجود في config/auth.php<br>";
    }
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><strong>بعد حل المشاكل، احذف هذا الملف!</strong></p>";
