<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum BoardPosition: string
{
    use HasOptions;

    case President = 'president';
    case VicePresident = 'vice_president';
    case Secretary = 'secretary';
    case Treasurer = 'treasurer';
    case CommitteeHead = 'committee_head';
    case Member = 'member';

    public function label(): string
    {
        return match ($this) {
            self::President => 'رئیس هیئت',
            self::VicePresident => 'نایب‌رئیس',
            self::Secretary => 'دبیر',
            self::Treasurer => 'خزانه‌دار',
            self::CommitteeHead => 'رئیس کمیته',
            self::Member => 'عضو هیئت',
        };
    }

    /** The four officers are shown above the committees on the board page. */
    public function isOfficer(): bool
    {
        return in_array($this, [self::President, self::VicePresident, self::Secretary, self::Treasurer], true);
    }
}
