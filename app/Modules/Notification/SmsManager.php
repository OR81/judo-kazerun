<?php

declare(strict_types=1);

namespace App\Modules\Notification;

use App\Modules\Notification\Contracts\SmsGateway;
use App\Modules\Notification\Gateways\KavenegarGateway;
use App\Modules\Notification\Gateways\LogGateway;
use InvalidArgumentException;

/**
 * Resolves the configured SMS gateway. Bound in AppServiceProvider so callers
 * type-hint SmsGateway and never name a provider — the same arrangement the
 * payment module uses.
 */
class SmsManager
{
    public function resolve(?string $name = null): SmsGateway
    {
        $name ??= config('sms.default');
        $config = config("sms.gateways.{$name}");

        if (! $config) {
            throw new InvalidArgumentException("SMS gateway [{$name}] is not configured.");
        }

        return match ($config['driver']) {
            'log' => new LogGateway,
            'kavenegar' => new KavenegarGateway(
                apiKey: $config['api_key'] ?? null,
                sender: $config['sender'] ?? null,
                template: $config['template'] ?? 'judo-login',
            ),
            default => throw new InvalidArgumentException("Unsupported SMS driver [{$config['driver']}]."),
        };
    }
}
