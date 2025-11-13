<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Here is where you can register authentication related routes for your 
| application. These routes are loaded by the RouteServiceProvider.
|
*/

// Guest Routes (للمستخدمين غير المسجلين)
Route::middleware('guest')->group(function () {
    
    // Login Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    
    // Register Routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
    
    // Password Reset Routes (اختياري)
    // Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    // Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])->name('password.email');
});

// Authenticated Routes (للمستخدمين المسجلين)
Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    
    // Profile Routes (اختياري)
    // Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    // Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
