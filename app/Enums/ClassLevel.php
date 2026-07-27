<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum ClassLevel: string
{
    use HasOptions;

    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';
    case Elite = 'elite';

    public function label(): string
    {
        return match ($this) {
            self::Beginner => 'مقدماتی',
            self::Intermediate => 'میانی',
            self::Advanced => 'پیشرفته',
            self::Elite => 'قهرمانی',
        };
    }
}
