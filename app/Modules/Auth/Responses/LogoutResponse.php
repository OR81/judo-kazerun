<?php

declare(strict_types=1);

namespace App\Modules\Auth\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse|JsonResponse
    {
        return $request->wantsJson()
            ? new JsonResponse(['redirect' => route('home')])
            : redirect()->route('home')->with('status', 'از حساب کاربری خارج شدید.');
    }
}
