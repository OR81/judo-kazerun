<?php

declare(strict_types=1);

namespace App\Modules\Payment\Gateways;

use App\Models\Transaction;
use App\Modules\Payment\Contracts\PaymentGateway;
use App\Modules\Payment\Data\PaymentResult;
use Illuminate\Support\Str;

/**
 * Development and test driver.
 *
 * Sends the visitor to an in-app page that mimics a bank's confirm/cancel
 * screen, so the whole registration flow can be exercised end to end without
 * a merchant account or any network access.
 */
class FakeGateway implements PaymentGateway
{
    public function request(Transaction $transaction, string $callbackUrl): PaymentResult
    {
        $transaction->update([
            'gateway' => $this->name(),
            'authority' => 'FAKE-'.Str::upper(Str::random(24)),
        ]);

        return PaymentResult::redirect(
            route('registration.sandbox', [
                'transaction' => $transaction->id,
                'callback' => $callbackUrl,
            ]),
        );
    }

    public function verify(Transaction $transaction, array $payload): PaymentResult
    {
        if (($payload['status'] ?? null) !== 'OK') {
            return PaymentResult::failed('پرداخت توسط شما لغو شد.');
        }

        return PaymentResult::paid(
            referenceId: (string) random_int(100_000_000, 999_999_999),
            cardPan: '6037-****-****-'.random_int(1000, 9999),
            payload: $payload,
        );
    }

    public function name(): string
    {
        return 'fake';
    }
}
