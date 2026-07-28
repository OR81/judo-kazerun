<?php

use App\Enums\UserRole;
use App\Models\LoginCode;
use App\Models\User;
use App\Support\PersianNumber;
use Illuminate\Support\Facades\Hash;

beforeEach(fn () => seedAll());

/** Ask for a code and read it back — the log driver has nowhere else to put it. */
function requestCode(string $mobile, array $extra = []): string
{
    test()->post('/login', ['mobile' => $mobile, ...$extra])->assertRedirect('/login/verify');

    return session('login_code');
}

function accountFor(UserRole $role): User
{
    return User::query()->where('role', $role)->firstOrFail();
}

it('asks for a mobile number and nothing else', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('شمارهٔ موبایل')
        ->assertSee('ارسال کد ورود')
        // The page says «رمز عبوری در کار نیست» in prose, so look for the field
        // rather than the words.
        ->assertDontSee('type="password"', false)
        ->assertDontSee('name="password"', false);
});

it('texts a code to a known number', function () {
    $athlete = accountFor(UserRole::Athlete);

    $code = requestCode($athlete->mobile);

    expect($code)->toHaveLength((int) config('sms.code.length'));

    $record = LoginCode::query()->forMobile($athlete->mobile)->latest('id')->firstOrFail();

    // Never stored in the clear.
    expect($record->code_hash)->not->toBe($code)
        ->and(Hash::check($code, $record->code_hash))->toBeTrue();
});

it('signs in with a mobile number and a code', function () {
    $athlete = accountFor(UserRole::Athlete);

    $code = requestCode($athlete->mobile);

    $this->post('/login/verify', ['code' => $code])->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($athlete);
});

it('accepts a number however it was typed', function (callable $rewrite) {
    $athlete = accountFor(UserRole::Athlete);

    $code = requestCode($rewrite($athlete->mobile));
    $this->post('/login/verify', ['code' => $code])->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($athlete);
})->with([
    'as stored' => [fn (string $m) => $m],
    'Persian digits' => [fn (string $m) => PersianNumber::toPersian($m)],
    'country code' => [fn (string $m) => '+98'.substr($m, 1)],
    'spaced' => [fn (string $m) => substr($m, 0, 4).' '.substr($m, 4, 3).' '.substr($m, 7)],
    'no leading zero' => [fn (string $m) => substr($m, 1)],
]);

it('accepts a code typed with Persian digits', function () {
    $athlete = accountFor(UserRole::Athlete);
    $code = requestCode($athlete->mobile);

    $this->post('/login/verify', ['code' => PersianNumber::toPersian($code)])
        ->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($athlete);
});

it('lands each role on its own portal', function (string $role, string $home) {
    $user = accountFor(UserRole::from($role));

    $code = requestCode($user->mobile);
    $this->post('/login/verify', ['code' => $code])->assertRedirect($home);
})->with([
    ['athlete', '/dashboard'],
    ['coach', '/coach'],
    ['admin', '/admin'],
]);

it('rejects a wrong code', function () {
    $athlete = accountFor(UserRole::Athlete);
    $code = requestCode($athlete->mobile);

    $this->post('/login/verify', ['code' => str_pad((string) ((int) $code + 1), 6, '0', STR_PAD_LEFT)])
        ->assertSessionHasErrors('code');

    $this->assertGuest();
});

it('rejects an expired code', function () {
    $athlete = accountFor(UserRole::Athlete);
    $code = requestCode($athlete->mobile);

    $this->travel(config('sms.code.ttl') + 5)->seconds();

    $this->post('/login/verify', ['code' => $code])->assertSessionHasErrors('code');

    $this->assertGuest();
});

it('burns a code once it has been used', function () {
    $athlete = accountFor(UserRole::Athlete);
    $code = requestCode($athlete->mobile);

    $this->post('/login/verify', ['code' => $code])->assertRedirect('/dashboard');
    $this->post('/logout');

    // Replaying the same code must not open a second session.
    $this->post('/login', ['mobile' => $athlete->mobile]);
    $this->post('/login/verify', ['code' => $code])->assertSessionHasErrors('code');

    $this->assertGuest();
});

it('stops guessing after the attempt limit', function () {
    $athlete = accountFor(UserRole::Athlete);
    $code = requestCode($athlete->mobile);

    foreach (range(1, (int) config('sms.code.max_attempts')) as $ignored) {
        $this->post('/login/verify', ['code' => '000000']);
    }

    // Even the right code is refused now: the record is spent.
    $this->post('/login/verify', ['code' => $code])->assertSessionHasErrors('code');

    $this->assertGuest();
});

it('only keeps the newest code alive', function () {
    $athlete = accountFor(UserRole::Athlete);

    $first = requestCode($athlete->mobile);
    $this->travel(config('sms.code.resend_after') + 5)->seconds();
    $second = requestCode($athlete->mobile);

    expect($first)->not->toBe($second);

    $this->post('/login/verify', ['code' => $first])->assertSessionHasErrors('code');
    $this->post('/login/verify', ['code' => $second])->assertRedirect('/dashboard');
});

it('refuses an unknown number', function () {
    $this->post('/login', ['mobile' => '09000000000'])->assertSessionHasErrors('mobile');

    expect(LoginCode::query()->count())->toBe(0);
});

it('refuses a malformed number', function (string $bad) {
    $this->post('/login', ['mobile' => $bad])->assertSessionHasErrors('mobile');
})->with(['0912', '12345678901', 'سلام', '08123456789']);

it('refuses a deactivated account', function () {
    $athlete = accountFor(UserRole::Athlete);
    $athlete->update(['is_active' => false]);

    $this->post('/login', ['mobile' => $athlete->mobile])->assertSessionHasErrors('mobile');

    expect(LoginCode::query()->count())->toBe(0);
    $this->assertGuest();
});

it('holds the pending number in the session, not the request', function () {
    $athlete = accountFor(UserRole::Athlete);
    $admin = accountFor(UserRole::Admin);

    $code = requestCode($athlete->mobile);

    // A code issued for the athlete cannot be redirected at the admin account by
    // adding a mobile field to the second step.
    $this->post('/login/verify', ['code' => $code, 'mobile' => $admin->mobile])
        ->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($athlete);
});

it('sends the verification page away when no code is pending', function () {
    $this->get('/login/verify')->assertRedirect('/login');
});

it('sends an already signed-in visitor to their own portal', function () {
    $this->actingAs(accountFor(UserRole::Coach))
        ->get('/login')
        ->assertRedirect('/coach');
});

it('makes the resend button wait', function () {
    $athlete = accountFor(UserRole::Athlete);
    requestCode($athlete->mobile);

    $this->post('/login/resend')->assertSessionHasErrors('code');

    $this->travel(config('sms.code.resend_after') + 5)->seconds();

    $this->post('/login/resend')->assertSessionHasNoErrors();
});

it('caps how many codes one number can be sent in an hour', function () {
    $athlete = accountFor(UserRole::Athlete);
    $max = (int) config('sms.code.max_per_hour');

    foreach (range(1, $max) as $ignored) {
        $this->post('/login', ['mobile' => $athlete->mobile]);
        $this->travel(config('sms.code.resend_after') + 1)->seconds();
    }

    $this->post('/login', ['mobile' => $athlete->mobile])->assertSessionHasErrors('mobile');

    expect(LoginCode::query()->forMobile($athlete->mobile)->count())->toBe($max);
});

it('records the sign-in time', function () {
    $athlete = accountFor(UserRole::Athlete);
    expect($athlete->last_login_at)->toBeNull();

    $code = requestCode($athlete->mobile);
    $this->post('/login/verify', ['code' => $code]);

    expect($athlete->fresh()->last_login_at)->not->toBeNull();
});

it('sends an administrator to the panel through the same door', function () {
    $admin = accountFor(UserRole::Admin);

    // Filament has no login page of its own any more.
    $this->get('/admin/login')->assertNotFound();

    $code = requestCode($admin->mobile);
    $this->post('/login/verify', ['code' => $code])->assertRedirect('/admin');

    $this->assertAuthenticatedAs($admin);
});

it('keeps no password or email column on users', function () {
    $columns = Schema::getColumnListing('users');

    expect($columns)->not->toContain('password')
        ->and($columns)->not->toContain('email')
        ->and($columns)->not->toContain('email_verified_at');
});

it('stores a mobile number in one shape however it was written', function (string $typed) {
    $user = User::factory()->create(['mobile' => $typed]);

    expect($user->fresh()->mobile)->toBe('09121110000');
})->with([
    '09121110000',
    '+989121110000',
    '00989121110000',
    '0912 111 0000',
    '9121110000',
    '۰۹۱۲۱۱۱۰۰۰۰',
]);
