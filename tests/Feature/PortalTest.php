<?php

use App\Enums\UserRole;
use App\Models\User;

beforeEach(fn () => seedAll());

function userWithRole(UserRole $role): User
{
    return User::query()->where('role', $role)->firstOrFail();
}

it('sends guests to the login page', function (string $uri) {
    $this->get($uri)->assertRedirect('/login');
})->with(['/dashboard', '/coach']);

it('shows the athlete dashboard', function () {
    $this->actingAs(userWithRole(UserRole::Athlete))
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('پرتال ورزشکار');
});

it('shows the coach dashboard with their classes', function () {
    $coach = userWithRole(UserRole::Coach);

    $this->actingAs($coach)
        ->get('/coach')
        ->assertOk()
        ->assertSee('کلاس‌های تحت نظر شما');
});

it('bounces a user who lands on the wrong portal', function () {
    $this->actingAs(userWithRole(UserRole::Athlete))
        ->get('/coach')
        ->assertRedirect('/dashboard');

    $this->actingAs(userWithRole(UserRole::Coach))
        ->get('/dashboard')
        ->assertRedirect('/coach');
});

it('lets only administrators reach the Filament panel', function () {
    $panel = Filament\Facades\Filament::getPanel('admin');

    expect(userWithRole(UserRole::Admin)->canAccessPanel($panel))->toBeTrue()
        ->and(userWithRole(UserRole::Coach)->canAccessPanel($panel))->toBeFalse()
        ->and(userWithRole(UserRole::Athlete)->canAccessPanel($panel))->toBeFalse();
});

it('signs out back to the home page', function () {
    $this->actingAs(userWithRole(UserRole::Athlete))
        ->post('/logout')
        ->assertRedirect('/');

    $this->assertGuest();
});
