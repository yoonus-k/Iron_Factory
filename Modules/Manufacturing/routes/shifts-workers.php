<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\ShiftsWorkersController;
use Modules\Manufacturing\Http\Controllers\WorkersController;
use Modules\Manufacturing\Http\Controllers\WorkerTeamsController;
use Modules\Manufacturing\Http\Controllers\ShiftHandoverController;

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
    Route::get('shifts-workers/generate-code', [ShiftsWorkersController::class, 'generateShiftCode'])->name('manufacturing.shifts-workers.generate-code');
    Route::get('shifts-workers/current/view', [ShiftsWorkersController::class, 'current'])->name('manufacturing.shifts-workers.current');
    Route::get('shifts-workers/attendance/log', [ShiftsWorkersController::class, 'attendance'])->name('manufacturing.shifts-workers.attendance');
    Route::get('shifts-workers/{id}/details', [ShiftsWorkersController::class, 'getShiftDetails'])->name('manufacturing.shifts-workers.details');
    Route::patch('shifts-workers/{id}/activate', [ShiftsWorkersController::class, 'activate'])->name('manufacturing.shifts-workers.activate');
    Route::patch('shifts-workers/{id}/complete', [ShiftsWorkersController::class, 'complete'])->name('manufacturing.shifts-workers.complete');
    Route::patch('shifts-workers/{id}/suspend', [ShiftsWorkersController::class, 'suspend'])->name('manufacturing.shifts-workers.suspend');
    Route::patch('shifts-workers/{id}/resume', [ShiftsWorkersController::class, 'resume'])->name('manufacturing.shifts-workers.resume');
    Route::resource('shifts-workers', ShiftsWorkersController::class)->names('manufacturing.shifts-workers');

    // Workers Management
    Route::get('workers/generate-code', [WorkersController::class, 'generateWorkerCode'])->name('manufacturing.workers.generate-code');
    Route::get('workers/available/list', [WorkersController::class, 'getAvailableWorkers'])->name('manufacturing.workers.available');
    Route::get('workers/permissions/by-role', [WorkersController::class, 'getDefaultPermissions'])->name('manufacturing.workers.permissions-by-role');
    Route::patch('workers/{id}/toggle-status', [WorkersController::class, 'toggleStatus'])->name('manufacturing.workers.toggle-status');
    Route::resource('workers', WorkersController::class)->names('manufacturing.workers');

    // Worker Teams Management
    Route::get('worker-teams/generate-code', [WorkerTeamsController::class, 'generateTeamCode'])->name('manufacturing.worker-teams.generate-code');
    Route::get('worker-teams/{id}/workers', [WorkerTeamsController::class, 'getTeamWorkers'])->name('manufacturing.worker-teams.workers');
    Route::patch('worker-teams/{id}/toggle-status', [WorkerTeamsController::class, 'toggleStatus'])->name('manufacturing.worker-teams.toggle-status');
    Route::resource('worker-teams', WorkerTeamsController::class)->names('manufacturing.worker-teams');

    // Shift Handover Management
    Route::get('shift-handovers/generate-code', [ShiftHandoverController::class, 'generateHandoverCode'])->name('manufacturing.shift-handovers.generate-code');
    Route::post('shift-handovers/{id}/approve', [ShiftHandoverController::class, 'approve'])->name('manufacturing.shift-handovers.approve');
    Route::post('shift-handovers/{id}/reject', [ShiftHandoverController::class, 'reject'])->name('manufacturing.shift-handovers.reject');
    Route::get('shift-handovers/api/user-shifts', [ShiftHandoverController::class, 'getUserActiveShifts'])->name('manufacturing.shift-handovers.api.user-shifts');
    Route::get('shift-handovers/api/available-workers', [ShiftHandoverController::class, 'getAvailableWorkers'])->name('manufacturing.shift-handovers.api.available-workers');
    Route::get('shift-handovers/api/history', [ShiftHandoverController::class, 'getHandoverHistory'])->name('manufacturing.shift-handovers.api.history');
    Route::resource('shift-handovers', ShiftHandoverController::class)->names('manufacturing.shift-handovers');

});
