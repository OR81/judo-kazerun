<?php

namespace App\Providers;

use App\Modules\Payment\Contracts\PaymentGateway;
use App\Modules\Payment\PaymentManager;
use Illuminate\Database\Eloquent\Model;
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
    }

    public function boot(): void
    {
        // Catches typos and N+1 queries during development, but never breaks production.
        Model::shouldBeStrict($this->app->isLocal());

        // RTL pagination with Persian numerals.
        Paginator::defaultView('partials.pagination');
        Paginator::defaultSimpleView('partials.pagination');

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
