<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== جميع العمال في جدول workers ===\n\n";

$workers = \App\Models\Worker::all();

if ($workers->isEmpty()) {
    echo "❌ لا يوجد عمال في الجدول!\n";
} else {
    foreach ($workers as $worker) {
        echo "Worker ID: " . $worker->id . "\n";
        echo "  - User ID: " . ($worker->user_id ?? 'NULL') . "\n";
        echo "  - Name: " . $worker->name . "\n";
        if ($worker->user_id) {
            $user = \App\Models\User::find($worker->user_id);
            if ($user) {
                echo "  - User Email: " . $user->email . "\n";
            }
        }
        echo "\n";
    }
}

echo "=== المستخدمون الذين لديهم دور WORKER ===\n\n";
$users = \App\Models\User::whereHas('roleRelation', function($q) {
    $q->where('role_code', 'LIKE', '%WORKER%');
})->get();

foreach ($users as $user) {
    $hasWorkerRecord = \App\Models\Worker::where('user_id', $user->id)->exists();
    echo "User ID: " . $user->id . " - " . $user->name . "\n";
    echo "  - Role: " . ($user->roleRelation ? $user->roleRelation->role_code : 'NULL') . "\n";
    echo "  - موجود في جدول workers: " . ($hasWorkerRecord ? "✅ نعم" : "❌ لا") . "\n\n";
}
