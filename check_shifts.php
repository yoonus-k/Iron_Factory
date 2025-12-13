<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(new Illuminate\Http\Request);

use Illuminate\Support\Facades\DB;

echo "=== Checking Active Shifts ===\n";
$activeShifts = DB::table('shift_assignments')
    ->where('status', 'active')
    ->latest('created_at')
    ->limit(5)
    ->get(['id', 'shift_code', 'stage_record_id', 'stage_record_barcode', 'status']);

foreach ($activeShifts as $shift) {
    echo "Shift: {$shift->shift_code} | stage_record_id: {$shift->stage_record_id} | barcode: {$shift->stage_record_barcode} | status: {$shift->status}\n";
}

echo "\n=== Checking Stage1 Stands ===\n";
$stage1Stands = DB::table('stage1_stands')
    ->latest('created_at')
    ->limit(5)
    ->get(['id', 'stand_number', 'shift_id', 'supervisor_id', 'status']);

foreach ($stage1Stands as $stand) {
    echo "Stand: {$stand->stand_number} | shift_id: {$stand->shift_id} | supervisor_id: {$stand->supervisor_id} | status: {$stand->status}\n";
}

echo "\n=== Checking Shift Operation Logs ===\n";
$logs = DB::table('shift_operation_logs')
    ->where('operation_type', 'transfer_stage')
    ->latest('created_at')
    ->limit(5)
    ->get(['id', 'shift_id', 'operation_type', 'description', 'created_at']);

foreach ($logs as $log) {
    echo "Log: {$log->description} | shift_id: {$log->shift_id} | created_at: {$log->created_at}\n";
}

echo "\nDone!\n";
