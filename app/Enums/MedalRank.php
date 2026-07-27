<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum MedalRank: string
{
    use HasOptions;

    case Gold = 'gold';
    case Silver = 'silver';
    case Bronze = 'bronze';
    case Participant = 'participant';

    public function label(): string
    {
        return match ($this) {
            self::Gold => 'طلا',
            self::Silver => 'نقره',
            self::Bronze => 'برنز',
            self::Participant => 'حضور',
        };
    }

    public function placeLabel(): string
    {
        return match ($this) {
            self::Gold => 'مقام اول',
            self::Silver => 'مقام دوم',
            self::Bronze => 'مقام سوم',
            self::Participant => 'شرکت‌کننده',
        };
    }

    /** Medal disc colour — deliberately not the brand palette. */
    public function ringClass(): string
    {
        return match ($this) {
            self::Gold => 'bg-gradient-to-br from-amber-300 to-amber-600 text-amber-950',
            self::Silver => 'bg-gradient-to-br from-slate-200 to-slate-400 text-slate-900',
            self::Bronze => 'bg-gradient-to-br from-orange-300 to-orange-700 text-orange-950',
            self::Participant => 'bg-surface-muted text-muted',
        };
    }
}
