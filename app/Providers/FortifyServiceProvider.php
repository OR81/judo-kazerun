<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use App\Modules\Auth\Responses\LoginResponse;
use App\Modules\Auth\Responses\LogoutResponse;
use App\Support\PersianNumber;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Role-aware redirects instead of Fortify's fixed /home.
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        Fortify::loginView(fn () => view('auth.login'));

        /*
         * Members sign in with a mobile number far more often than an email —
         * many have no email at all — so the single "email" field accepts either.
         * Persian digits are folded to Latin first, since that is what an Iranian
         * keyboard produces.
         */
        Fortify::authenticateUsing(function (Request $request) {
            $login = PersianNumber::toLatin(trim((string) $request->input('email')));

            $user = User::query()
                ->where(fn ($query) => $query
                    ->where('email', $login)
                    ->orWhere('mobile', $login)
                    // Tolerate a mobile entered without its leading zero.
                    ->orWhere('mobile', Str::start($login, '0')))
                ->first();

            if (! $user || ! Hash::check($request->input('password'), $user->password)) {
                return null;
            }

            if (! $user->is_active) {
                return null;
            }

            $user->forceFill(['last_login_at' => now()])->saveQuietly();

            return $user;
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip()
            );
        });
    }
}
