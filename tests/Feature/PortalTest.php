<?php

use App\Enums\UserRole;
use App\Models\User;
use App\Support\PersianNumber;

beforeEach(fn () => seedAll());

function userWithRole(UserRole $role): User
{
    return User::query()->where('role', $role)->firstOrFail();
}

it('sends guests to the login page', function (string $uri) {
    $this->get($uri)->assertRedirect('/login');
})->with(['/dashboard', '/coach']);

it('signs in with a mobile number', function () {
    $athlete = userWithRole(UserRole::Athlete);

    $this->post('/login', ['email' => $athlete->mobile, 'password' => 'password'])
        ->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($athlete);
});

it('signs in with an email address', function () {
    $athlete = userWithRole(UserRole::Athlete);

    $this->post('/login', ['email' => $athlete->email, 'password' => 'password'])
        ->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($athlete);
});

it('accepts a mobile typed with Persian digits', function () {
    $athlete = userWithRole(UserRole::Athlete);

    $this->post('/login', [
        'email' => PersianNumber::toPersian($athlete->mobile),
        'password' => 'password',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($athlete);
});

it('lands each role on its own portal', function (string $role, string $home) {
    $user = userWithRole(UserRole::from($role));

    $this->post('/login', ['email' => $user->mobile, 'password' => 'password'])
        ->assertRedirect($home);
})->with([
    ['athlete', '/dashboard'],
    ['coach', '/coach'],
    ['admin', '/admin'],
]);

it('rejects a wrong password', function () {
    $athlete = userWithRole(UserRole::Athlete);

    $this->post('/login', ['email' => $athlete->mobile, 'password' => 'wrong'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('refuses a deactivated account', function () {
    $athlete = userWithRole(UserRole::Athlete);
    $athlete->update(['is_active' => false]);

    $this->post('/login', ['email' => $athlete->mobile, 'password' => 'password'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('records the sign-in time', function () {
    $athlete = userWithRole(UserRole::Athlete);
    expect($athlete->last_login_at)->toBeNull();

    $this->post('/login', ['email' => $athlete->mobile, 'password' => 'password']);

    expect($athlete->fresh()->last_login_at)->not->toBeNull();
});

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
