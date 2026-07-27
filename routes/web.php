<?php

use App\Modules\Contact\Http\Controllers\ContactController;
use App\Modules\Contact\Http\Controllers\NewsletterController;
use App\Modules\Content\Http\Controllers\NewsController;
use App\Modules\Core\Http\Controllers\HomeController;
use App\Modules\Download\Http\Controllers\DownloadController;
use App\Modules\Event\Http\Controllers\EventController;
use App\Modules\Media\Http\Controllers\GalleryController;
use App\Modules\People\Http\Controllers\AthleteController;
use App\Modules\People\Http\Controllers\BoardController;
use App\Modules\People\Http\Controllers\CoachController;
use App\Modules\Training\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
|
| Registration and payment live in routes/registration.php, and the athlete
| and coach dashboards in routes/portal.php. Fortify owns the auth routes,
| and Filament owns /admin.
|
*/

Route::get('/', HomeController::class)->name('home');

Route::get('/schedule', ScheduleController::class)->name('schedule');

Route::controller(CoachController::class)->prefix('coaches')->name('coaches.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{coach}', 'show')->name('show');
});

Route::get('/board', BoardController::class)->name('board');

Route::controller(AthleteController::class)->prefix('athletes')->name('athletes.')->group(function () {
    Route::get('/', 'index')->name('index');
    // Declared before /{athlete} so the literal segment isn't swallowed by the wildcard.
    Route::get('/national-team', 'national')->name('national');
    Route::get('/{athlete}', 'show')->name('show');
});

Route::controller(NewsController::class)->prefix('news')->name('news.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{news}', 'show')->name('show');
});

Route::controller(EventController::class)->prefix('events')->name('events.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{event}', 'show')->name('show');
});

Route::controller(GalleryController::class)->prefix('gallery')->name('gallery')->group(function () {
    Route::get('/', 'index')->name('');
    Route::get('/videos', 'videos')->name('.videos');
    Route::get('/{album}', 'show')->name('.show');
});

Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads');
Route::get('/downloads/{download}/file', [DownloadController::class, 'file'])->name('downloads.file');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('contact.store');

Route::post('/newsletter', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:6,1')
    ->name('newsletter.subscribe');
Route::get('/newsletter/{subscriber}/unsubscribe', [NewsletterController::class, 'unsubscribe'])
    ->name('newsletter.unsubscribe');

require __DIR__.'/registration.php';
require __DIR__.'/portal.php';
