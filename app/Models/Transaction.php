<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** تراکنش‌های پرداخت — مبالغ به تومان ذخیره می‌شوند */
#[Fillable([
    'enrollment_id', 'gateway', 'amount', 'authority', 'ref_id',
    'card_pan', 'status', 'message', 'payload', 'paid_at',
])]
class Transaction extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => TransactionStatus::class,
            'amount' => 'integer',
            'payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Enrollment, $this> */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /** @param Builder<$this> $query */
    public function scopePaid(Builder $query): void
    {
        $query->where('status', TransactionStatus::Paid);
    }

    protected function isPaid(): Attribute
    {
        return Attribute::get(fn (): bool => $this->status === TransactionStatus::Paid);
    }

    /** Card numbers are already masked by the gateway; shown RTL-safe. */
    protected function maskedPan(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->card_pan ? fa($this->card_pan) : null);
    }
}
