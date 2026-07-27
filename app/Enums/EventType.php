<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum EventType: string
{
    use HasOptions;

    case Competition = 'competition';
    case DanExam = 'dan_exam';
    case Camp = 'camp';
    case Seminar = 'seminar';
    case Ceremony = 'ceremony';

    public function label(): string
    {
        return match ($this) {
            self::Competition => 'مسابقه',
            self::DanExam => 'آزمون دان',
            self::Camp => 'اردوی آماده‌سازی',
            self::Seminar => 'سمینار و کارگاه',
            self::Ceremony => 'مراسم',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Competition => 'fa-trophy',
            self::DanExam => 'fa-certificate',
            self::Camp => 'fa-campground',
            self::Seminar => 'fa-chalkboard-user',
            self::Ceremony => 'fa-award',
        };
    }

    /** Tailwind classes for the badge on event cards. */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Competition => 'bg-brand-soft text-brand-text',
            self::DanExam => 'bg-accent-soft text-accent-text',
            self::Camp => 'bg-emerald-500/12 text-emerald-700',
            self::Seminar => 'bg-sky-500/12 text-sky-700',
            self::Ceremony => 'bg-violet-500/12 text-violet-700',
        };
    }
}
