<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * وضعیت سانس — what is happening in a hall during one weekly slot.
 *
 * The colours are deliberate and exclusive: green only ever means «آزاد», the
 * brand blue only ever means «کلاس هیئت», grey only ever means «رزرو شده». Since
 * neither green nor grey is a brand colour, a visitor scanning the hall board
 * reads availability from colour alone — and every state also carries an icon
 * and a label, so the board still works without colour vision.
 */
enum SlotStatus: string
{
    use HasOptions;

    case Open = 'open';
    case BoardClass = 'class';
    case Booked = 'booked';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'آزاد برای اجاره',
            self::BoardClass => 'کلاس هیئت',
            self::Booked => 'رزرو شده',
            self::Closed => 'تعطیل',
        };
    }

    /** Fits inside a slot chip where the full label would wrap. */
    public function shortLabel(): string
    {
        return match ($this) {
            self::Open => 'آزاد',
            self::BoardClass => 'کلاس هیئت',
            self::Booked => 'رزرو شده',
            self::Closed => 'تعطیل',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Open => 'fa-lock-open',
            self::BoardClass => 'fa-graduation-cap',
            self::Booked => 'fa-lock',
            self::Closed => 'fa-screwdriver-wrench',
        };
    }

    /** Only free slots can be requested. */
    public function isBookable(): bool
    {
        return $this === self::Open;
    }

    /** Chip inside a slot card. */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Open => 'bg-open-soft text-open-text',
            self::BoardClass => 'bg-brand-soft text-brand-text',
            self::Booked => 'bg-taken-soft text-taken-text',
            self::Closed => 'bg-taken-soft text-taken-text',
        };
    }

    /** The slot card itself — a tinted surface with a coloured leading edge. */
    public function cardClass(): string
    {
        return match ($this) {
            self::Open => 'border-open/30 bg-open-soft/60',
            self::BoardClass => 'border-brand/25 bg-brand-soft/60',
            self::Booked => 'border-line bg-surface',
            self::Closed => 'border-dashed border-line-strong bg-surface-muted/50',
        };
    }

    /** The 3px rail down the leading edge of a slot card. */
    public function railClass(): string
    {
        return match ($this) {
            self::Open => 'bg-open',
            self::BoardClass => 'bg-brand',
            self::Booked => 'bg-taken',
            self::Closed => 'bg-line-strong',
        };
    }
}
