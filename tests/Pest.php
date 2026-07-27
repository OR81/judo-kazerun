<?php

use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Feature tests hit real routes against a migrated in-memory SQLite database
| (see phpunit.xml), so they exercise controllers, Blade and the schema together.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extend(TestCase::class)->in('Unit');

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/** Seed the minimum needed for pages that read site settings. */
function seedSite(): void
{
    test()->seed(SettingSeeder::class);
}

/** Full demo content — for tests that walk real pages. */
function seedAll(): void
{
    test()->seed();
}
