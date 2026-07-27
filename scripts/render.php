<?php

/**
 * Requests every public route through the HTTP kernel and reports the status.
 * Faster than booting a browser, and it surfaces Blade/controller errors with
 * a real stack trace.
 *
 *   php scripts/render.php
 */

// Imports must precede any code that uses them: PHP applies a `use` alias only
// from its own position onward, so aliases declared lower in the file would
// leave earlier ::class references resolving to a bare, unqualified name.
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\GalleryAlbum;
use App\Models\News;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(ConsoleKernel::class)->bootstrap();

$kernel = $app->make(HttpKernel::class);

$routes = [
    ['GET', '/'],
    ['GET', '/schedule'],
    ['GET', '/register'],
    ['GET', '/coaches'],
    ['GET', '/coaches/'.Coach::value('slug')],
    ['GET', '/board'],
    ['GET', '/athletes'],
    ['GET', '/athletes/national-team'],
    ['GET', '/athletes/'.Athlete::value('slug')],
    ['GET', '/news'],
    ['GET', '/news?q=مسابقات'],
    ['GET', '/news?category='.News::with('category')->first()?->category?->slug],
    ['GET', '/news/'.News::value('slug')],
    ['GET', '/events'],
    ['GET', '/events?type=competition'],
    ['GET', '/events/'.Event::value('slug')],
    ['GET', '/gallery'],
    ['GET', '/gallery/videos'],
    ['GET', '/gallery/'.GalleryAlbum::value('slug')],
    ['GET', '/downloads'],
    ['GET', '/contact'],
    ['GET', '/login'],
    ['GET', '/register/success/'.Enrollment::value('reference')],
];

$failed = 0;

foreach ($routes as [$method, $uri]) {
    try {
        $request = Request::create($uri, $method);
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();

        $ok = $status >= 200 && $status < 400;
        if (! $ok) {
            $failed++;
        }

        printf("  %s  %-3d  %s\n", $ok ? 'OK  ' : 'FAIL', $status, $uri);

        if (! $ok) {
            $html = (string) $response->getContent();

            // Drop <style>/<script> bodies first, or the error page's inlined
            // Tailwind buries the actual message.
            $html = preg_replace('#<(style|script)\b[^>]*>.*?</\1>#is', ' ', $html);
            $body = trim(preg_replace('/\s+/u', ' ', strip_tags($html)));

            printf("        %s\n", mb_substr($body, 0, 300));
        }
    } catch (Throwable $e) {
        $failed++;
        printf("  FAIL  ---  %s\n        %s\n        %s:%d\n", $uri, $e->getMessage(), $e->getFile(), $e->getLine());
    }
}

echo $failed ? "\n{$failed} route(s) failed.\n" : "\nAll routes rendered.\n";
exit($failed ? 1 : 0);
