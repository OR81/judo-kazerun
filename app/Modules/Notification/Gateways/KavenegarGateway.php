<?php

declare(strict_types=1);

namespace App\Modules\Notification\Gateways;

use App\Modules\Notification\Contracts\SmsGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Kavenegar (کاوه‌نگار).
 *
 * One-time codes go through `verify/lookup`, not `sms/send`: Iranian operators
 * only deliver OTP traffic on pre-approved templates, and the lookup endpoint is
 * also exempt from the subscriber blacklist — a member who has blocked marketing
 * SMS would otherwise be locked out of their own account.
 *
 * The template is registered in the Kavenegar panel and receives the code as its
 * first token, so the wording lives there rather than in this file.
 */
class KavenegarGateway implements SmsGateway
{
    private const BASE = 'https://api.kavenegar.com/v1';

    public function __construct(
        private readonly ?string $apiKey,
        private readonly ?string $sender = null,
        private readonly string $template = 'judo-login',
    ) {}

    public function send(string $mobile, string $message): bool
    {
        return $this->call('sms/send.json', array_filter([
            'receptor' => $mobile,
            'sender' => $this->sender,
            'message' => $message,
        ]));
    }

    public function sendCode(string $mobile, string $code): bool
    {
        return $this->call('verify/lookup.json', [
            'receptor' => $mobile,
            'token' => $code,
            'template' => $this->template,
        ]);
    }

    /** @param  array<string, string>  $payload */
    private function call(string $path, array $payload): bool
    {
        if (blank($this->apiKey)) {
            Log::warning('SMS gateway is not configured; message dropped.', ['to' => $payload['receptor']]);

            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->post(self::BASE."/{$this->apiKey}/{$path}", $payload);

            // Kavenegar answers 200 with its own status envelope, so the HTTP
            // code alone does not mean the message was accepted.
            $status = (int) $response->json('return.status', 0);

            if ($response->successful() && $status === 200) {
                return true;
            }

            Log::warning('SMS gateway rejected the message.', [
                'to' => $payload['receptor'],
                'status' => $status,
                'message' => $response->json('return.message'),
            ]);

            return false;
        } catch (Throwable $e) {
            Log::error('SMS gateway is unreachable.', [
                'to' => $payload['receptor'],
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
