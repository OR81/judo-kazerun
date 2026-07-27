<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/** رده‌های سنی — the bands the board actually runs classes for. */
enum AgeGroup: string
{
    use HasOptions;

    case Kids = 'kids';
    case Juniors = 'juniors';
    case Cadets = 'cadets';
    case Adults = 'adults';
    case Veterans = 'veterans';

    public function label(): string
    {
        return match ($this) {
            self::Kids => 'کودکان',
            self::Juniors => 'نونهالان',
            self::Cadets => 'نوجوانان',
            self::Adults => 'بزرگسالان',
            self::Veterans => 'پیشکسوتان',
        };
    }

    public function ageRange(): string
    {
        return match ($this) {
            self::Kids => '۶ تا ۹ سال',
            self::Juniors => '۱۰ تا ۱۳ سال',
            self::Cadets => '۱۴ تا ۱۷ سال',
            self::Adults => '۱۸ سال به بالا',
            self::Veterans => '۳۵ سال به بالا',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Kids => 'fa-child-reaching',
            self::Juniors => 'fa-person',
            self::Cadets => 'fa-person-running',
            self::Adults => 'fa-user-ninja',
            self::Veterans => 'fa-medal',
        };
    }
}
