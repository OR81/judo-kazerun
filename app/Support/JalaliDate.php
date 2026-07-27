<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Morilog\Jalali\Jalalian;

/**
 * Shamsi (Jalali) date rendering.
 *
 * Everything is stored as ordinary Gregorian timestamps; conversion happens only
 * at the presentation edge, so queries, sorting and comparisons stay normal.
 */
final class JalaliDate
{
    private const MONTHS = [
        1 => 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند',
    ];

    public static function of(DateTimeInterface|string|null $date): ?Jalalian
    {
        if ($date === null || $date === '') {
            return null;
        }

        return Jalalian::fromCarbon(
            $date instanceof CarbonInterface ? $date : Carbon::parse($date),
        );
    }

    /** «۱۴۰۴/۰۵/۰۵» */
    public static function short(DateTimeInterface|string|null $date): string
    {
        $jalali = self::of($date);

        return $jalali ? PersianNumber::toPersian($jalali->format('Y/m/d')) : '—';
    }

    /** «۵ مرداد ۱۴۰۴» */
    public static function long(DateTimeInterface|string|null $date): string
    {
        $jalali = self::of($date);

        if (! $jalali) {
            return '—';
        }

        return PersianNumber::toPersian(
            sprintf('%d %s %d', $jalali->getDay(), self::MONTHS[$jalali->getMonth()], $jalali->getYear()),
        );
    }

    /** «شنبه ۵ مرداد ۱۴۰۴» */
    public static function full(DateTimeInterface|string|null $date): string
    {
        $jalali = self::of($date);

        if (! $jalali) {
            return '—';
        }

        return $jalali->format('l').' '.self::long($date);
    }

    /** «۵ مرداد ۱۴۰۴ — ۱۸:۳۰» */
    public static function dateTime(DateTimeInterface|string|null $date): string
    {
        $jalali = self::of($date);

        if (! $jalali) {
            return '—';
        }

        return self::long($date).' — '.PersianNumber::toPersian($jalali->format('H:i'));
    }

    /** «۵» — the day alone, for calendar tiles. */
    public static function day(DateTimeInterface|string|null $date): string
    {
        $jalali = self::of($date);

        return $jalali ? PersianNumber::toPersian((string) $jalali->getDay()) : '—';
    }

    /** «مرداد» — the month name alone, for calendar tiles. */
    public static function month(DateTimeInterface|string|null $date): string
    {
        $jalali = self::of($date);

        return $jalali ? self::MONTHS[$jalali->getMonth()] : '—';
    }

    /** «۱۴۰۵» — for copyright lines and year filters. */
    public static function year(DateTimeInterface|string|null $date): string
    {
        $jalali = self::of($date);

        return $jalali ? PersianNumber::toPersian((string) $jalali->getYear()) : '—';
    }

    /** «۳ روز پیش» */
    public static function ago(DateTimeInterface|string|null $date): string
    {
        $jalali = self::of($date);

        return $jalali ? PersianNumber::toPersian($jalali->ago()) : '—';
    }

    /** «۱۸:۳۰» — for timetable rows, which store plain time strings. */
    public static function time(DateTimeInterface|string|null $time): string
    {
        if ($time === null || $time === '') {
            return '—';
        }

        $value = $time instanceof DateTimeInterface
            ? $time->format('H:i')
            : substr((string) $time, 0, 5);

        return PersianNumber::toPersian($value);
    }

    /**
     * Machine-readable Gregorian value for <time datetime="…">, so assistive
     * tech and crawlers get an unambiguous date alongside the Shamsi label.
     */
    public static function machine(DateTimeInterface|string|null $date): string
    {
        if ($date === null || $date === '') {
            return '';
        }

        return ($date instanceof CarbonInterface ? $date : Carbon::parse($date))->toIso8601String();
    }
}
