<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum TransactionStatus: string
{
    use HasOptions;

    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Canceled = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'در انتظار پرداخت',
            self::Paid => 'موفق',
            self::Failed => 'ناموفق',
            self::Canceled => 'لغوشده',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-accent-soft text-accent-text',
            self::Paid => 'bg-emerald-500/12 text-emerald-700',
            self::Failed => 'bg-brand-soft text-brand-text',
            self::Canceled => 'bg-surface-muted text-muted',
        };
    }
}
