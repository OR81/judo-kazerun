<?php

declare(strict_types=1);

use App\Models\Setting;
use App\Support\JalaliDate;
use App\Support\PersianNumber;
use Illuminate\Support\Str;

/*
 * Thin global shortcuts for the presentation layer. Blade templates read far
 * better with `{{ jdate($post->published_at) }}` than a fully-qualified static
 * call, and these are used on nearly every page.
 */

if (! function_exists('fa')) {
    /** Render digits in a string as Persian. */
    function fa(string|int|float|null $value): string
    {
        return PersianNumber::toPersian($value);
    }
}

if (! function_exists('fa_number')) {
    /** Thousands-separated Persian number. */
    function fa_number(int|float|null $value, int $decimals = 0): string
    {
        return PersianNumber::format($value, $decimals);
    }
}

if (! function_exists('toman')) {
    /** Format a Toman amount for display. */
    function toman(int|float|null $value): string
    {
        return PersianNumber::toman($value);
    }
}

/*
 * Note: morilog/jalali already registers a global `jdate()` that returns a raw
 * Jalalian object, and its vendor autoload wins over the root package's — so
 * these are named distinctly rather than silently losing to it.
 */

if (! function_exists('shamsi')) {
    /** Shamsi date, e.g. «۵ مرداد ۱۴۰۴». */
    function shamsi(DateTimeInterface|string|null $date, string $style = 'long'): string
    {
        return match ($style) {
            'short' => JalaliDate::short($date),
            'full' => JalaliDate::full($date),
            'datetime' => JalaliDate::dateTime($date),
            'ago' => JalaliDate::ago($date),
            'time' => JalaliDate::time($date),
            'year' => JalaliDate::year($date),
            'day' => JalaliDate::day($date),
            'month' => JalaliDate::month($date),
            default => JalaliDate::long($date),
        };
    }
}

if (! function_exists('shamsi_attr')) {
    /** ISO-8601 value for a <time datetime="…"> attribute. */
    function shamsi_attr(DateTimeInterface|string|null $date): string
    {
        return JalaliDate::machine($date);
    }
}

if (! function_exists('setting')) {
    /** Read a site setting. The whole table is cached as one map. */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('fa_slug')) {
    /**
     * Slug that keeps Persian characters.
     *
     * Str::slug() transliterates through Str::ascii() by default, which strips
     * Persian entirely and leaves an empty string. Passing a null language
     * skips that step, so «محمدرضا دهقانی» becomes «محمدرضا-دهقانی» — which is
     * also the better URL for Persian SEO.
     */
    function fa_slug(string $value): string
    {
        return Str::slug($value, '-', null);
    }
}

if (! function_exists('weekday_fa')) {
    /** Persian weekday name for an index where 0 is Saturday. */
    function weekday_fa(int $index): string
    {
        return PersianNumber::weekday($index);
    }
}
