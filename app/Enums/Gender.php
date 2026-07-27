<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum Gender: string
{
    use HasOptions;

    case Male = 'male';
    case Female = 'female';
    case Mixed = 'mixed';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'آقایان',
            self::Female => 'بانوان',
            self::Mixed => 'مختلط',
        };
    }

    /** Wording for an individual person rather than a class. */
    public function personLabel(): string
    {
        return match ($this) {
            self::Male => 'مرد',
            self::Female => 'زن',
            self::Mixed => '—',
        };
    }
}
