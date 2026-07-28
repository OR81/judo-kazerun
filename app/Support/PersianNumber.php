<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Persian digit and number formatting.
 *
 * Persian uses U+06F0..U+06F9 (۰۱۲۳۴۵۶۷۸۹), which are distinct from the
 * Arabic-Indic digits U+0660..U+0669 (٠١٢٣٤٥٦٧٨٩). Input from users may contain
 * either, so normalisation accepts both but output is always Persian.
 */
final class PersianNumber
{
    private const PERSIAN = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    private const ARABIC = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    private const LATIN = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    /** Render any digits in the string as Persian. */
    public static function toPersian(string|int|float|null $value): string
    {
        return str_replace(self::LATIN, self::PERSIAN, (string) $value);
    }

    /** Fold Persian and Arabic-Indic digits back to Latin, for storage and validation. */
    public static function toLatin(string|int|float|null $value): string
    {
        return str_replace(
            [...self::PERSIAN, ...self::ARABIC],
            [...self::LATIN, ...self::LATIN],
            (string) $value,
        );
    }

    /** Thousands-separated Persian number: 12500 → «۱۲٬۵۰۰». */
    public static function format(int|float|null $value, int $decimals = 0): string
    {
        // U+066C is the Persian thousands separator; a plain comma reads as Latin.
        return self::toPersian(number_format((float) ($value ?? 0), $decimals, '.', '٬'));
    }

    /** Amounts are stored in Toman, which is what Iranian users actually think in. */
    public static function toman(int|float|null $value): string
    {
        return self::format($value).' تومان';
    }

    /**
     * Persian ordinal-ish label for a weekday index, where 0 is Saturday to
     * match how the training timetable is stored.
     */
    public static function weekday(int $index): string
    {
        return self::weekdays()[$index % 7];
    }

    /** [0 => 'شنبه', … 6 => 'جمعه'] — ready for a <select> or a Filament field. */
    public static function weekdays(): array
    {
        return ['شنبه', 'یک‌شنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه'];
    }
}
