<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates the portals by role.
 *
 * A signed-in user who lands on the wrong portal is sent to their own rather
 * than shown a 403 — that's their mistake to recover from, not an error.
 */
class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            auth()->logout();

            return redirect()->route('login')->with('error', 'حساب کاربری شما غیرفعال است.');
        }

        if (! in_array($user->role->value, $roles, true)) {
            return redirect($user->role->home());
        }

        return $next($request);
    }
}
