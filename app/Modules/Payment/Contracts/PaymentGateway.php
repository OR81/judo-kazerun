<?php

declare(strict_types=1);

namespace App\Modules\Payment\Contracts;

use App\Models\Transaction;
use App\Modules\Payment\Data\PaymentResult;

/**
 * The contract every payment provider implements.
 *
 * Keeping this narrow — start a payment, verify one — means adding Zibal,
 * IDPay or Mellat later is a new driver class and a config key, with no
 * changes anywhere in the registration flow.
 */
interface PaymentGateway
{
    /**
     * Begin a payment and return the URL the visitor should be sent to.
     *
     * Implementations persist whatever reference the provider hands back
     * (an "authority" token, usually) onto the transaction.
     */
    public function request(Transaction $transaction, string $callbackUrl): PaymentResult;

    /**
     * Confirm a payment after the provider redirects back.
     *
     * Must be safe to call more than once for the same transaction: providers
     * retry callbacks, and visitors refresh the return page.
     */
    public function verify(Transaction $transaction, array $payload): PaymentResult;

    public function name(): string;
}
