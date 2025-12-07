<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SystemSettingController;

Route::get('/', function () {
    return view('welcome');
});

// Protected Routes - Require Authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard Route - with permission check
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stage-worker/dashboard', [App\Http\Controllers\StageWorkerDashboardController::class, 'index'])->name('stage-worker.dashboard.index');
    Route::get('/stage-worker/dashboard/updates', [App\Http\Controllers\StageWorkerDashboardController::class, 'getUpdates'])->name('stage-worker.dashboard.updates');
    Route::get('/manager', [DashboardController::class, 'index'])->name('manager');
       Route::get('/profile', [UserController::class, 'profile'])->name('profile');

    // Alias for dashboard
    Route::get('/home', function () {
        return redirect('/dashboard');
    });

    // Notifications Routes
    Route::prefix('notifications')->name('notifications.')->controller(NotificationController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/api', 'getNotifications')->name('api');
        Route::get('/unread-count', 'getUnreadCount')->name('unread-count');
        Route::post('/{id}/mark-as-read', 'markAsRead')->name('mark-as-read');
        Route::post('/mark-all-read', 'markAllAsRead')->name('mark-all-read');
        Route::get('/{id}', 'show')->name('show');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::delete('/', 'destroyAll')->name('destroy-all');
    });

    // Test Permissions Page
    Route::get('/test-permissions', function () {
        return view('test-permissions');
    })->name('test-permissions');

    // Customers Routes
    Route::resource('customers', CustomerController::class)->except(['create', 'show', 'edit']);
    Route::post('customers/{id}/activate', [CustomerController::class, 'activate'])->name('customers.activate');
    Route::post('customers/{id}/deactivate', [CustomerController::class, 'deactivate'])->name('customers.deactivate');
    Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');

    // System Settings Routes (Read and Update only - no create/delete)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SystemSettingController::class, 'index'])->name('index');
        Route::get('/edit', [SystemSettingController::class, 'edit'])->name('edit');
        Route::put('/update', [SystemSettingController::class, 'update'])->name('update');
    });

    // Stage Suspensions Routes
    Route::prefix('stage-suspensions')->name('stage-suspensions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\StageSuspensionController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\StageSuspensionController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [\App\Http\Controllers\StageSuspensionController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [\App\Http\Controllers\StageSuspensionController::class, 'reject'])->name('reject');
    });

    // Roles & Permissions Routes (Admin Only)
    Route::middleware(['role:ADMIN'])->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('users', UserController::class);

        // إضافة routes للدوال الإضافية
        Route::post('users/{user}/resend-credentials', [UserController::class, 'resendCredentials'])->name('users.resend-credentials');
        Route::post('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
        Route::post('exit-impersonation', [UserController::class, 'exitImpersonation'])->name('exit-impersonation');
    });
});;

// Language switching route
Route::get('/locale/{lang}', function ($lang) {
    if (in_array($lang, config('app.available_locales', ['ar', 'en']))) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }
    return back();
})->name('locale.switch');
