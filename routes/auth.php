<?php

use App\Modules\Auth\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
|
| Two steps, one credential: a mobile number and a code texted to it. Every
| role signs in here, administrators included — the Filament panel has no login
| page of its own and bounces unauthenticated visitors to this one.
|
| The throttles are deliberately tighter than Laravel's default. Sending is
| capped per number as well as per IP (a single number is also capped per hour in
| LoginCodeService), and verification is capped per session so a stolen code
| cannot be brute-forced from a pool of addresses.
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');

    Route::get('/login/verify', [LoginController::class, 'verify'])->name('login.verify');

    Route::post('/login/verify', [LoginController::class, 'confirm'])
        ->middleware('throttle:10,1')
        ->name('login.confirm');

    Route::post('/login/resend', [LoginController::class, 'resend'])
        ->middleware('throttle:4,1')
        ->name('login.resend');

    Route::post('/login/change', [LoginController::class, 'change'])->name('login.change');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
