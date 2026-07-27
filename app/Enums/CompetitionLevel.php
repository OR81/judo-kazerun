<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum CompetitionLevel: string
{
    use HasOptions;

    case City = 'city';
    case Provincial = 'provincial';
    case National = 'national';
    case International = 'international';

    public function label(): string
    {
        return match ($this) {
            self::City => 'شهرستانی',
            self::Provincial => 'استانی',
            self::National => 'کشوری',
            self::International => 'بین‌المللی',
        };
    }
}
