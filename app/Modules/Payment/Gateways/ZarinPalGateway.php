<?php

declare(strict_types=1);

namespace App\Modules\Payment\Gateways;

use App\Models\Transaction;
use App\Modules\Payment\Contracts\PaymentGateway;
use App\Modules\Payment\Data\PaymentResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * ZarinPal, REST v4.
 *
 * Amounts are stored in Toman application-wide; ZarinPal's v4 API takes Rial,
 * so the ×10 conversion happens here and nowhere else.
 */
class ZarinPalGateway implements PaymentGateway
{
    private const LIVE = 'https://payment.zarinpal.com';

    private const SANDBOX = 'https://sandbox.zarinpal.com';

    public function __construct(
        private readonly ?string $merchantId,
        private readonly bool $sandbox = true,
    ) {}

    public function request(Transaction $transaction, string $callbackUrl): PaymentResult
    {
        if (blank($this->merchantId)) {
            return PaymentResult::failed('درگاه پرداخت پیکربندی نشده است. لطفاً با دفتر هیئت تماس بگیرید.');
        }

        try {
            $response = Http::acceptJson()
                ->timeout(20)
                ->post($this->base().'/pg/v4/payment/request.json', [
                    'merchant_id' => $this->merchantId,
                    'amount' => $transaction->amount * 10,
                    'callback_url' => $callbackUrl,
                    'description' => "ثبت‌نام کلاس جودو — کد پیگیری {$transaction->enrollment->reference}",
                    'metadata' => array_filter([
                        'mobile' => $transaction->enrollment->mobile,
                        'email' => $transaction->enrollment->email,
                    ]),
                ]);

            $body = $response->json() ?? [];
            $authority = data_get($body, 'data.authority');

            if (! $response->successful() || blank($authority)) {
                Log::warning('ZarinPal request failed', ['body' => $body, 'transaction' => $transaction->id]);

                return PaymentResult::failed(
                    'ایجاد تراکنش در درگاه پرداخت ناموفق بود. لطفاً دوباره تلاش کنید.',
                    $body,
                );
            }

            $transaction->update(['gateway' => $this->name(), 'authority' => $authority]);

            return PaymentResult::redirect($this->base()."/pg/StartPay/{$authority}");
        } catch (Throwable $e) {
            Log::error('ZarinPal request threw', ['message' => $e->getMessage(), 'transaction' => $transaction->id]);

            return PaymentResult::failed('ارتباط با درگاه پرداخت برقرار نشد. لطفاً دوباره تلاش کنید.');
        }
    }

    public function verify(Transaction $transaction, array $payload): PaymentResult
    {
        if (($payload['Status'] ?? $payload['status'] ?? null) !== 'OK') {
            return PaymentResult::failed('پرداخت توسط شما لغو شد.');
        }

        try {
            $response = Http::acceptJson()
                ->timeout(20)
                ->post($this->base().'/pg/v4/payment/verify.json', [
                    'merchant_id' => $this->merchantId,
                    'amount' => $transaction->amount * 10,
                    'authority' => $transaction->authority,
                ]);

            $body = $response->json() ?? [];
            $code = (int) data_get($body, 'data.code');

            // 100 = verified now, 101 = already verified on an earlier call.
            // Both mean the money is settled, so both count as success.
            if (! in_array($code, [100, 101], true)) {
                return PaymentResult::failed(
                    'تأیید پرداخت ناموفق بود. در صورت کسر وجه، مبلغ تا ۷۲ ساعت آینده بازگردانده می‌شود.',
                    $body,
                );
            }

            return PaymentResult::paid(
                referenceId: (string) data_get($body, 'data.ref_id'),
                cardPan: data_get($body, 'data.card_pan'),
                payload: $body,
            );
        } catch (Throwable $e) {
            Log::error('ZarinPal verify threw', ['message' => $e->getMessage(), 'transaction' => $transaction->id]);

            return PaymentResult::failed('بررسی وضعیت پرداخت ممکن نشد. لطفاً با دفتر هیئت تماس بگیرید.');
        }
    }

    public function name(): string
    {
        return 'zarinpal';
    }

    private function base(): string
    {
        return $this->sandbox ? self::SANDBOX : self::LIVE;
    }
}
