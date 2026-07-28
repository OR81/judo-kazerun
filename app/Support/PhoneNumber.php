<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Iranian mobile numbers.
 *
 * The same phone gets typed a dozen ways — ۰۹۱۷۱۲۳۴۵۶۷ from a Persian keyboard,
 * +989171234567 from a contacts app, 0917 123 4567 with spaces. Since the number
 * is now the entire identity of an account, every one of those has to land on the
 * same stored value, or a member is simply locked out.
 */
final class PhoneNumber
{
    /** Canonical form: 09xxxxxxxxx, or null when the input is not one. */
    public static function normalize(string|int|null $value): ?string
    {
        $digits = preg_replace('/\D+/', '', PersianNumber::toLatin((string) $value)) ?? '';

        // 0098… and 98… and +98… all mean the same national number.
        $digits = match (true) {
            str_starts_with($digits, '0098') => substr($digits, 4),
            str_starts_with($digits, '98') && strlen($digits) === 12 => substr($digits, 2),
            default => $digits,
        };

        // A bare 9xxxxxxxxx is what you get when the leading zero is dropped.
        if (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            $digits = '0'.$digits;
        }

        return self::isValid($digits) ? $digits : null;
    }

    /** Iranian mobile prefixes are all 09 followed by nine digits. */
    public static function isValid(?string $value): bool
    {
        return is_string($value) && (bool) preg_match('/^09\d{9}$/', $value);
    }

    /** «۰۹۱۷***۴۵۶۷» — enough to recognise your own number, not enough to leak one. */
    public static function mask(?string $mobile): string
    {
        if (! self::isValid($mobile)) {
            return '';
        }

        return PersianNumber::toPersian(substr($mobile, 0, 4).'***'.substr($mobile, -4));
    }
}
