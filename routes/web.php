<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard Route
Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/manager', function () {
    return view('dashboard');
});

// Alias for dashboard
Route::get('/home', function () {
    return redirect('/dashboard');
});

// Language switching route
Route::get('/locale/{lang}', function ($lang) {
    if (in_array($lang, config('app.available_locales', ['ar', 'en']))) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }
    return back();
})->name('locale.switch');
