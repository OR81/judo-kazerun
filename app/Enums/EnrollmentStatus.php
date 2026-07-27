<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum EnrollmentStatus: string
{
    use HasOptions;

    case Pending = 'pending';
    case AwaitingPayment = 'awaiting_payment';
    case Paid = 'paid';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'در انتظار بررسی',
            self::AwaitingPayment => 'در انتظار پرداخت',
            self::Paid => 'پرداخت‌شده',
            self::Approved => 'تأییدشده',
            self::Rejected => 'ردشده',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-surface-muted text-copy',
            self::AwaitingPayment => 'bg-accent-soft text-accent-text',
            self::Paid => 'bg-sky-500/12 text-sky-700',
            self::Approved => 'bg-emerald-500/12 text-emerald-700',
            self::Rejected => 'bg-brand-soft text-brand-text',
        };
    }

    /** Statuses that count against a class's capacity. */
    public function occupiesSeat(): bool
    {
        return in_array($this, [self::Paid, self::Approved], true);
    }
}
