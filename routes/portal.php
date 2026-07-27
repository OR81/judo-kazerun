<?php

use App\Enums\UserRole;
use App\Modules\Auth\Http\Controllers\PortalController;
use App\Modules\Auth\Http\Middleware\EnsureUserRole;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Member portals
|--------------------------------------------------------------------------
|
| Athletes and coaches get Blade dashboards that match the public design
| system. Administrators go to the Filament panel at /admin instead.
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PortalController::class, 'athlete'])
        ->middleware(EnsureUserRole::class.':'.UserRole::Athlete->value)
        ->name('dashboard');

    Route::get('/coach', [PortalController::class, 'coach'])
        ->middleware(EnsureUserRole::class.':'.UserRole::Coach->value)
        ->name('portal.coach');
});
