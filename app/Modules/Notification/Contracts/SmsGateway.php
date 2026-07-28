<?php

declare(strict_types=1);

namespace App\Modules\Notification\Contracts;

/**
 * A way of getting a short message onto a phone.
 *
 * Deliberately narrow: the login flow is the only caller, and it only ever needs
 * to know whether the network accepted the message. Anything richer (delivery
 * receipts, campaign sending) belongs to a different contract.
 */
interface SmsGateway
{
    /**
     * @param  string  $mobile  normalised to 09xxxxxxxxx
     * @return bool whether the provider accepted the message for delivery
     */
    public function send(string $mobile, string $message): bool;

    /**
     * Send a one-time code.
     *
     * Iranian providers deliver OTPs through a pre-approved template endpoint
     * rather than as free text, so this is a separate call and not just `send()`
     * with a formatted string.
     */
    public function sendCode(string $mobile, string $code): bool;
}
