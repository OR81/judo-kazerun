<?php

declare(strict_types=1);

namespace App\Modules\Payment\Data;

/**
 * What a gateway hands back from request() or verify().
 *
 * `message` is written for the applicant to read, so it is always in Persian.
 */
final readonly class PaymentResult
{
    private function __construct(
        public bool $successful,
        public string $message,
        public ?string $redirectUrl = null,
        public ?string $referenceId = null,
        public ?string $cardPan = null,
        public array $payload = [],
    ) {}

    public static function redirect(string $url, string $message = 'در حال انتقال به درگاه پرداخت…'): self
    {
        return new self(true, $message, redirectUrl: $url);
    }

    public static function paid(string $referenceId, ?string $cardPan = null, array $payload = []): self
    {
        return new self(
            true,
            'پرداخت با موفقیت انجام شد.',
            referenceId: $referenceId,
            cardPan: $cardPan,
            payload: $payload,
        );
    }

    public static function failed(string $message, array $payload = []): self
    {
        return new self(false, $message, payload: $payload);
    }
}
