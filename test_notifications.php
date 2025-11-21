<?php
require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test notifications
use App\Models\Notification;

try {
    $count = Notification::count();
    echo "Total notifications: " . $count . "\n";
    
    if ($count > 0) {
        $notifications = Notification::take(5)->get();
        echo "Recent notifications:\n";
        foreach ($notifications as $notification) {
            echo "- " . $notification->title . ": " . $notification->message . "\n";
        }
    } else {
        echo "No notifications found in the database.\n";
    }
    
    // Test statistics
    $stats = Notification::getStatistics();
    echo "\nStatistics:\n";
    print_r($stats);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}