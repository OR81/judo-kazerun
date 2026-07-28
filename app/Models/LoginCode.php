<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

/** کد یک‌بارمصرف ورود */
#[Fillable(['mobile', 'code_hash', 'expires_at', 'consumed_at', 'attempts', 'ip'])]
#[Hidden(['code_hash'])]
class LoginCode extends Model
{
    use HasFactory, MassPrunable;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    /**
     * Rows older than a day are of no further use.
     *
     * The hourly issue limit only looks back one hour, so nothing beyond that
     * window is load-bearing — but the table would grow without bound otherwise.
     * Scheduled as `model:prune` in routes/console.php.
     *
     * @return Builder<$this>
     */
    public function prunable(): Builder
    {
        return static::query()->where('created_at', '<', now()->subDay());
    }

    /** @param  Builder<$this>  $query */
    public function scopeForMobile(Builder $query, string $mobile): void
    {
        $query->where('mobile', $mobile);
    }

    /** Still issued, still in date, still unused. */
    public function scopeUsable(Builder $query): void
    {
        $query->whereNull('consumed_at')->where('expires_at', '>', now());
    }

    protected function isExpired(): Attribute
    {
        return Attribute::get(fn (): bool => $this->expires_at->isPast());
    }

    protected function isConsumed(): Attribute
    {
        return Attribute::get(fn (): bool => $this->consumed_at !== null);
    }
}
