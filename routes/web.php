<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Protected Routes - Require Authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/manager', [DashboardController::class, 'index'])->name('manager');

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
});

// Language switching route
Route::get('/locale/{lang}', function ($lang) {
    if (in_array($lang, config('app.available_locales', ['ar', 'en']))) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }
    return back();
})->name('locale.switch');
