<?php

declare(strict_types=1);

namespace App\Modules\Auth\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

/**
 * Sends each role to its own portal rather than Fortify's default /home:
 * athletes to the Blade dashboard, coaches to theirs, admins into Filament.
 */
class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|JsonResponse
    {
        /** @var Request $request */
        $home = $request->user()?->role->home() ?? '/';

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false, 'redirect' => $home])
            // intended() honours a deep link the visitor was bounced from.
            : redirect()->intended($home);
    }
}
