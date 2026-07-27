<?php

use App\Modules\Registration\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Registration and payment
|--------------------------------------------------------------------------
|
| Open to guests — a parent registering a child does not need an account.
|
*/

Route::controller(RegistrationController::class)->group(function () {
    Route::get('/register', 'create')->name('register');

    Route::post('/register', 'store')
        ->middleware('throttle:10,1')
        ->name('registration.store');

    // The gateway returns the visitor here; it must stay reachable to guests.
    Route::get('/register/callback/{transaction}', 'callback')->name('registration.callback');

    Route::get('/register/success/{enrollment}', 'success')->name('registration.success');

    // Only mounted for the `fake` driver; the controller 404s otherwise.
    Route::get('/register/sandbox/{transaction}', 'sandbox')->name('registration.sandbox');
});
