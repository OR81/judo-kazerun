<?php

declare(strict_types=1);

namespace App\Modules\Payment;

use App\Modules\Payment\Contracts\PaymentGateway;
use App\Modules\Payment\Gateways\FakeGateway;
use App\Modules\Payment\Gateways\ZarinPalGateway;
use InvalidArgumentException;

/**
 * Resolves the configured gateway. Bound as a singleton in AppServiceProvider,
 * so controllers type-hint PaymentGateway and never mention a provider by name.
 */
class PaymentManager
{
    public function resolve(?string $name = null): PaymentGateway
    {
        $name ??= config('payment.default');
        $config = config("payment.gateways.{$name}");

        if (! $config) {
            throw new InvalidArgumentException("Payment gateway [{$name}] is not configured.");
        }

        return match ($config['driver']) {
            'fake' => new FakeGateway,
            'zarinpal' => new ZarinPalGateway(
                merchantId: $config['merchant_id'] ?? null,
                sandbox: (bool) ($config['sandbox'] ?? true),
            ),
            default => throw new InvalidArgumentException("Unsupported payment driver [{$config['driver']}]."),
        };
    }
}
