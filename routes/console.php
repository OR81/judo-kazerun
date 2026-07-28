<?php

use App\Models\LoginCode;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Spent sign-in codes. They are kept for a day so a replay attempt still finds a
 * consumed row rather than nothing at all, and then swept.
 */
Schedule::command('model:prune', ['--model' => [LoginCode::class]])->daily();
