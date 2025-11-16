<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\ShiftsWorkersController;
use Modules\Manufacturing\Http\Controllers\WorkersController;
use Modules\Manufacturing\Http\Controllers\WorkerTeamsController;

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
    Route::patch('shifts-workers/{id}/activate', [ShiftsWorkersController::class, 'activate'])->name('manufacturing.shifts-workers.activate');
    Route::patch('shifts-workers/{id}/complete', [ShiftsWorkersController::class, 'complete'])->name('manufacturing.shifts-workers.complete');
    Route::resource('shifts-workers', ShiftsWorkersController::class)->names('manufacturing.shifts-workers');
    
    // Workers Management
    Route::get('workers/generate-code', [WorkersController::class, 'generateWorkerCode'])->name('manufacturing.workers.generate-code');
    Route::get('workers/available/list', [WorkersController::class, 'getAvailableWorkers'])->name('manufacturing.workers.available');
    Route::patch('workers/{id}/toggle-status', [WorkersController::class, 'toggleStatus'])->name('manufacturing.workers.toggle-status');
    Route::resource('workers', WorkersController::class)->names('manufacturing.workers');

    // Worker Teams Management
    Route::get('worker-teams/generate-code', [WorkerTeamsController::class, 'generateTeamCode'])->name('manufacturing.worker-teams.generate-code');
    Route::get('worker-teams/{id}/workers', [WorkerTeamsController::class, 'getTeamWorkers'])->name('manufacturing.worker-teams.workers');
    Route::patch('worker-teams/{id}/toggle-status', [WorkerTeamsController::class, 'toggleStatus'])->name('manufacturing.worker-teams.toggle-status');
    Route::resource('worker-teams', WorkerTeamsController::class)->names('manufacturing.worker-teams');

});
