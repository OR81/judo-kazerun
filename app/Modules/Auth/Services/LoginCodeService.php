<?php

declare(strict_types=1);

namespace App\Modules\Auth\Services;

use App\Models\LoginCode;
use App\Modules\Notification\Contracts\SmsGateway;
use Illuminate\Support\Facades\Hash;

/**
 * Issuing and checking the one-time codes that are now the only credential.
 *
 * Codes are stored hashed and each mobile has at most one live code: issuing a
 * new one consumes whatever came before, so a member who taps «ارسال دوباره»
 * cannot end up with two working codes in their inbox.
 */
class LoginCodeService
{
    public function __construct(private readonly SmsGateway $sms) {}

    /**
     * Issue a code for a number and text it over.
     *
     * Returns the plain code so the caller can show it in local development; it
     * is never persisted or logged anywhere else.
     */
    public function issue(string $mobile, ?string $ip = null): string
    {
        // Only the newest code may work.
        LoginCode::query()->forMobile($mobile)->usable()->update(['consumed_at' => now()]);

        $code = $this->generate();

        LoginCode::create([
            'mobile' => $mobile,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addSeconds((int) config('sms.code.ttl')),
            'ip' => $ip,
        ]);

        $this->sms->sendCode($mobile, $code);

        return $code;
    }

    /**
     * Check a code, burning an attempt whether or not it matches.
     *
     * A wrong guess counts even when the code has expired, so a stale code cannot
     * be used as a free oracle.
     */
    public function verify(string $mobile, string $code): bool
    {
        $record = LoginCode::query()
            ->forMobile($mobile)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (! $record || $record->is_expired) {
            return false;
        }

        if ($record->attempts >= (int) config('sms.code.max_attempts')) {
            // Burn it: past the attempt ceiling the code is spent, right or wrong.
            $record->update(['consumed_at' => now()]);

            return false;
        }

        $record->increment('attempts');

        if (! Hash::check($code, $record->code_hash)) {
            return false;
        }

        $record->update(['consumed_at' => now()]);

        return true;
    }

    /** Seconds left before another code may be requested; zero when it may be now. */
    public function cooldown(string $mobile): int
    {
        $last = LoginCode::query()->forMobile($mobile)->latest('id')->first();

        if (! $last) {
            return 0;
        }

        $ready = $last->created_at->addSeconds((int) config('sms.code.resend_after'));

        return max(0, (int) ceil(now()->diffInSeconds($ready, false)));
    }

    /** How many codes this number has already been sent in the past hour. */
    public function issuedLastHour(string $mobile): int
    {
        return LoginCode::query()
            ->forMobile($mobile)
            ->where('created_at', '>=', now()->subHour())
            ->count();
    }

    public function hasReachedHourlyLimit(string $mobile): bool
    {
        return $this->issuedLastHour($mobile) >= (int) config('sms.code.max_per_hour');
    }

    private function generate(): string
    {
        $length = (int) config('sms.code.length', 6);

        return (string) random_int((int) str_pad('1', $length, '0'), (int) str_repeat('9', $length));
    }
}
