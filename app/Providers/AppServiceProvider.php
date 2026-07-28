<?php

namespace App\Providers;

use App\Modules\Notification\Contracts\SmsGateway;
use App\Modules\Notification\SmsManager;
use App\Modules\Payment\Contracts\PaymentGateway;
use App\Modules\Payment\PaymentManager;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentManager::class);

        // Controllers depend on the interface; the driver comes from config.
        $this->app->bind(
            PaymentGateway::class,
            fn ($app) => $app->make(PaymentManager::class)->resolve(),
        );

        $this->app->singleton(SmsManager::class);

        $this->app->bind(
            SmsGateway::class,
            fn ($app) => $app->make(SmsManager::class)->resolve(),
        );
    }

    public function boot(): void
    {
        // Catches typos and N+1 queries during development, but never breaks production.
        Model::shouldBeStrict($this->app->isLocal());

        // A signed-in visitor who opens /login belongs in their own portal, not on
        // the framework's default '/'.
        RedirectIfAuthenticated::redirectUsing(
            fn (Request $request) => $request->user()?->role->home() ?? '/',
        );

        // RTL pagination with Persian numerals.
        Paginator::defaultView('partials.pagination');
        Paginator::defaultSimpleView('partials.pagination');

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
