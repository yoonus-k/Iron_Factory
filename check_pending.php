<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$count = \App\Models\PendingSync::count();
echo "Pending syncs: {$count}\n";

if ($count > 0) {
    $latest = \App\Models\PendingSync::latest()->first();
    echo "Latest: {$latest->entity_type} - {$latest->action} - {$latest->status}\n";
    echo "Local ID: {$latest->local_id}\n";
    echo "Data: " . json_encode($latest->sync_data, JSON_UNESCAPED_UNICODE) . "\n";
}
