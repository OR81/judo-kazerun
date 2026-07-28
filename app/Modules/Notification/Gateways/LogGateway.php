<?php

declare(strict_types=1);

namespace App\Modules\Notification\Gateways;

use App\Modules\Notification\Contracts\SmsGateway;
use Illuminate\Support\Facades\Log;

/**
 * The development driver: writes the message to the log instead of sending it.
 *
 * Paired with `sms.expose_codes`, which lets the verification page print the code
 * on screen in local development — the same arrangement as the fake payment
 * gateway, so nobody needs a real SIM to sign in while building the site.
 */
class LogGateway implements SmsGateway
{
    public function send(string $mobile, string $message): bool
    {
        Log::channel(config('sms.log_channel'))->info('SMS', [
            'to' => $mobile,
            'message' => $message,
        ]);

        return true;
    }

    public function sendCode(string $mobile, string $code): bool
    {
        return $this->send($mobile, "کد ورود شما: {$code}");
    }
}
