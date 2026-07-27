<?php

/**
 * Signs in as the seeded administrator and requests every Filament page, so
 * broken resources surface here rather than in the browser.
 *
 *   php scripts/admin-check.php
 */
require __DIR__.'/../vendor/autoload.php';

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$admin = User::where('role', UserRole::Admin)->first();

if (! $admin) {
    echo "No admin user found — run: php artisan migrate:fresh --seed\n";
    exit(1);
}

auth()->login($admin);

$panel = Filament\Facades\Filament::getPanel('admin');
Filament\Facades\Filament::setCurrentPanel($panel);

$uris = ['/admin'];

foreach ($panel->getResources() as $resource) {
    foreach (['index', 'create'] as $page) {
        if (! array_key_exists($page, $resource::getPages())) {
            continue;
        }

        try {
            $uris[] = parse_url($resource::getUrl($page), PHP_URL_PATH);
        } catch (Throwable $e) {
            echo "  WARN  could not build {$page} URL for ".class_basename($resource)."\n";
        }
    }
}

$failed = 0;

foreach (array_unique($uris) as $uri) {
    try {
        $request = Request::create($uri, 'GET');
        $request->setUserResolver(fn () => $admin);

        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        $ok = $status >= 200 && $status < 400;

        if (! $ok) {
            $failed++;
        }

        printf("  %s  %-3d  %s\n", $ok ? 'OK  ' : 'FAIL', $status, $uri);

        if (! $ok) {
            $html = preg_replace('#<(style|script)\b[^>]*>.*?</\1>#is', ' ', (string) $response->getContent());
            printf("        %s\n", mb_substr(trim(preg_replace('/\s+/u', ' ', strip_tags($html))), 0, 300));
        }
    } catch (Throwable $e) {
        $failed++;
        printf("  FAIL  ---  %s\n        %s\n        %s:%d\n", $uri, $e->getMessage(), basename($e->getFile()), $e->getLine());
    }
}

echo $failed ? "\n{$failed} admin page(s) failed.\n" : "\nAll admin pages rendered.\n";
exit($failed ? 1 : 0);
