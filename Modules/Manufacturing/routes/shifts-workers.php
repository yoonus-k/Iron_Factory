<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\ShiftsWorkersController;
use Modules\Manufacturing\Http\Controllers\WorkersController;
use Modules\Manufacturing\Http\Controllers\WorkerTeamsController;
use Modules\Manufacturing\Http\Controllers\ShiftHandoverController;
use Modules\Manufacturing\Http\Controllers\PendingWorkController;

/*
|--------------------------------------------------------------------------
| Shifts and Workers Management Routes
|--------------------------------------------------------------------------
| Routes for managing shifts and workers
| Protected by authentication middleware
*/

Route::middleware(['auth'])->group(function () {

    // Shifts Management
    // Generate code routes MUST be before resource routes
    Route::get('shifts-workers/generate-code', [ShiftsWorkersController::class, 'generateShiftCode'])
        ->middleware('permission:SHIFTS_READ')
        ->name('manufacturing.shifts-workers.generate-code');

    Route::get('shifts-workers/current/view', [ShiftsWorkersController::class, 'current'])
        ->middleware('permission:SHIFTS_CURRENT')
        ->name('manufacturing.shifts-workers.current');

    Route::get('shifts-workers/attendance/log', [ShiftsWorkersController::class, 'attendance'])
        ->middleware('permission:SHIFTS_ATTENDANCE')
        ->name('manufacturing.shifts-workers.attendance');

    Route::get('shifts-workers/{id}/details', [ShiftsWorkersController::class, 'getShiftDetails'])
        ->middleware('permission:SHIFT_WORKERS_VIEW')
        ->name('manufacturing.shifts-workers.details');

    Route::patch('shifts-workers/{id}/activate', [ShiftsWorkersController::class, 'activate'])
        ->middleware('permission:SHIFTS_UPDATE')
        ->name('manufacturing.shifts-workers.activate');

    Route::patch('shifts-workers/{id}/complete', [ShiftsWorkersController::class, 'complete'])
        ->middleware('permission:SHIFTS_UPDATE')
        ->name('manufacturing.shifts-workers.complete');

    Route::patch('shifts-workers/{id}/suspend', [ShiftsWorkersController::class, 'suspend'])
        ->middleware('permission:SHIFTS_UPDATE')
        ->name('manufacturing.shifts-workers.suspend');

    Route::patch('shifts-workers/{id}/resume', [ShiftsWorkersController::class, 'resume'])
        ->middleware('permission:SHIFTS_UPDATE')
        ->name('manufacturing.shifts-workers.resume');

    Route::resource('shifts-workers', ShiftsWorkersController::class)
        ->middleware('permission:SHIFT_WORKERS_READ')
        ->names('manufacturing.shifts-workers');

    // Workers Management
    Route::get('workers/generate-code', [WorkersController::class, 'generateWorkerCode'])
        ->middleware('permission:SHIFT_WORKERS_READ')
        ->name('manufacturing.workers.generate-code');

    Route::get('workers/available/list', [WorkersController::class, 'getAvailableWorkers'])
        ->middleware('permission:SHIFT_WORKERS_READ')
        ->name('manufacturing.workers.available');

    Route::get('workers/permissions/by-role', [WorkersController::class, 'getDefaultPermissions'])
        ->middleware('permission:SHIFT_WORKERS_READ')
        ->name('manufacturing.workers.permissions-by-role');

    Route::patch('workers/{id}/toggle-status', [WorkersController::class, 'toggleStatus'])
        ->middleware('permission:SHIFT_WORKERS_UPDATE')
        ->name('manufacturing.workers.toggle-status');

    Route::resource('workers', WorkersController::class)
        ->middleware('permission:SHIFT_WORKERS_READ')
        ->names('manufacturing.workers');

    // Worker Teams Management
    Route::get('worker-teams/generate-code', [WorkerTeamsController::class, 'generateTeamCode'])
        ->middleware('permission:SHIFT_WORKERS_READ')
        ->name('manufacturing.worker-teams.generate-code');

    Route::get('worker-teams/{id}/workers', [WorkerTeamsController::class, 'getTeamWorkers'])
        ->middleware('permission:SHIFT_WORKERS_VIEW')
        ->name('manufacturing.worker-teams.workers');

    Route::patch('worker-teams/{id}/toggle-status', [WorkerTeamsController::class, 'toggleStatus'])
        ->middleware('permission:SHIFT_WORKERS_UPDATE')
        ->name('manufacturing.worker-teams.toggle-status');

    Route::resource('worker-teams', WorkerTeamsController::class)
        ->middleware('permission:SHIFT_WORKERS_READ')
        ->names('manufacturing.worker-teams');

    // Shift Handover Management
    Route::get('shift-handovers/generate-code', [ShiftHandoverController::class, 'generateHandoverCode'])
        ->middleware('permission:SHIFT_HANDOVERS_READ')
        ->name('manufacturing.shift-handovers.generate-code');

    Route::post('shift-handovers/{id}/approve', [ShiftHandoverController::class, 'approve'])
        ->middleware('permission:SHIFT_HANDOVERS_APPROVE')
        ->name('manufacturing.shift-handovers.approve');

    Route::post('shift-handovers/{id}/reject', [ShiftHandoverController::class, 'reject'])
        ->middleware('permission:SHIFT_HANDOVERS_REJECT')
        ->name('manufacturing.shift-handovers.reject');

    Route::get('shift-handovers/api/user-shifts', [ShiftHandoverController::class, 'getUserActiveShifts'])
        ->middleware('permission:SHIFT_HANDOVERS_READ')
        ->name('manufacturing.shift-handovers.api.user-shifts');

    Route::get('shift-handovers/api/available-workers', [ShiftHandoverController::class, 'getAvailableWorkers'])
        ->middleware('permission:SHIFT_HANDOVERS_READ')
        ->name('manufacturing.shift-handovers.api.available-workers');

    Route::get('shift-handovers/api/history', [ShiftHandoverController::class, 'getHandoverHistory'])
        ->middleware('permission:SHIFT_HANDOVERS_READ')
        ->name('manufacturing.shift-handovers.api.history');

    // New Routes for Pending Work Handover
    Route::get('shift-handovers/end-shift', [ShiftHandoverController::class, 'endShift'])
        ->middleware('permission:SHIFT_HANDOVERS_CREATE')
        ->name('manufacturing.shift-handovers.end-shift');

    Route::post('shift-handovers/store-end-shift', [ShiftHandoverController::class, 'storeEndShift'])
        ->middleware('permission:SHIFT_HANDOVERS_CREATE')
        ->name('manufacturing.shift-handovers.store-end-shift');

    Route::post('shift-handovers/{id}/acknowledge', [ShiftHandoverController::class, 'acknowledge'])
        ->middleware('permission:SHIFT_HANDOVERS_READ')
        ->name('manufacturing.shift-handovers.acknowledge');

    Route::get('shift-handovers/my-pending-work', [ShiftHandoverController::class, 'myPendingWork'])
        ->middleware('permission:SHIFT_HANDOVERS_READ')
        ->name('manufacturing.shift-handovers.my-pending-work');

    Route::post('shift-handovers/collect-pending-work', [ShiftHandoverController::class, 'collectPendingWork'])
        ->middleware('permission:SHIFT_HANDOVERS_READ')
        ->name('manufacturing.shift-handovers.collect-pending-work');

    Route::resource('shift-handovers', ShiftHandoverController::class)
        ->middleware('permission:SHIFT_HANDOVERS_READ')
        ->names('manufacturing.shift-handovers');

    // Pending Work Items Routes
    Route::post('pending-work/{id}/mark-in-progress', [PendingWorkController::class, 'markInProgress'])
        ->middleware('permission:PENDING_WORK_UPDATE')
        ->name('manufacturing.pending-work.mark-in-progress');

    Route::post('pending-work/{id}/mark-completed', [PendingWorkController::class, 'markCompleted'])
        ->middleware('permission:PENDING_WORK_UPDATE')
        ->name('manufacturing.pending-work.mark-completed');

    Route::post('pending-work/{id}/assign', [PendingWorkController::class, 'assign'])
        ->middleware('permission:PENDING_WORK_UPDATE')
        ->name('manufacturing.pending-work.assign');

    Route::get('pending-work/api/dashboard', [PendingWorkController::class, 'getDashboardData'])
        ->middleware('permission:PENDING_WORK_READ')
        ->name('manufacturing.pending-work.api.dashboard');

    Route::resource('pending-work', PendingWorkController::class)
        ->middleware('permission:PENDING_WORK_READ')
        ->names('manufacturing.pending-work');

});
