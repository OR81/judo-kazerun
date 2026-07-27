<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** دسته‌بندی اخبار */
#[Fillable(['name', 'slug', 'color', 'description', 'order'])]
class NewsCategory extends Model
{
    use HasFactory;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return HasMany<News, $this> */
    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    /** @param Builder<$this> $query */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('order')->orderBy('name');
    }

    /** Tailwind classes for the category chip. */
    public function badgeClass(): string
    {
        return match ($this->color) {
            'accent' => 'bg-accent-soft text-accent-text',
            'emerald' => 'bg-emerald-500/12 text-emerald-700',
            'sky' => 'bg-sky-500/12 text-sky-700',
            'violet' => 'bg-violet-500/12 text-violet-700',
            default => 'bg-brand-soft text-brand-text',
        };
    }
}
