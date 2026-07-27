<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum UserRole: string
{
    use HasOptions;

    case Athlete = 'athlete';
    case Coach = 'coach';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Athlete => 'ورزشکار',
            self::Coach => 'مربی',
            self::Admin => 'مدیر',
        };
    }

    /** Where this role lands after signing in. */
    public function home(): string
    {
        return match ($this) {
            self::Athlete => '/dashboard',
            self::Coach => '/coach',
            self::Admin => '/admin',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Athlete => 'fa-user',
            self::Coach => 'fa-chalkboard-user',
            self::Admin => 'fa-shield-halved',
        };
    }
}
