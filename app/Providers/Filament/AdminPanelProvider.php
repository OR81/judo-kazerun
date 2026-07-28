<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Admin panel at /admin.
 *
 * Access is limited to UserRole::Admin by User::canAccessPanel(). Athletes and
 * coaches get Blade dashboards that match the public design system instead.
 *
 * Sign-in is not Filament's: the panel has no login page, so an administrator
 * authenticates at /login with a mobile number and a texted code exactly like a
 * member, and there is no second credential path into the admin.
 *
 * This is the only place Livewire and Alpine load — the public bundle stays
 * dependency-free vanilla JS.
 */
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            // No panel login page: administrators sign in at /login with the same
            // mobile-and-code flow as everyone else, and unauthenticated requests
            // here are redirected there by the framework's auth exception.
            ->brandName('هیئت جودو کازرون')
            ->brandLogo(asset('favicon.svg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('favicon.svg'))
            // Filament compiles its own CSS outside the site's Vite bundle, so the
            // face is declared in a standalone stylesheet. provider: null keeps it
            // off Google Fonts / Bunny — the panel must work without a CDN.
            // RTL comes for free: Filament ships fa translations with direction=rtl
            // and APP_LOCALE is fa.
            ->font('Vazirmatn', url: asset('css/vazirmatn.css'), provider: null)
            ->colors([
                'primary' => Color::hex('#1D4ED8'),
                'danger' => Color::Red,
                'warning' => Color::hex('#D97706'),
                'gray' => Color::Stone,
                'success' => Color::Emerald,
                'info' => Color::Sky,
            ])
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('خانهٔ جودو')->icon('heroicon-o-home-modern'),
                NavigationGroup::make('محتوا')->icon('heroicon-o-newspaper'),
                NavigationGroup::make('آموزش و ثبت‌نام')->icon('heroicon-o-academic-cap'),
                NavigationGroup::make('افراد')->icon('heroicon-o-users'),
                NavigationGroup::make('رسانه')->icon('heroicon-o-photo'),
                NavigationGroup::make('ارتباطات')->icon('heroicon-o-inbox'),
                NavigationGroup::make('تنظیمات')->icon('heroicon-o-cog-6-tooth'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
