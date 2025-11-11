<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard Route
Route::get('/manager', function () {
    return view('dashboard');
});

// Alias for dashboard
Route::get('/home', function () {
    return redirect('/dashboard');
});
