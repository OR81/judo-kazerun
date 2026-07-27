<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\MedalRank;
use App\Models\Concerns\ResolvesMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** ورزشکاران، ملی‌پوشان و مدال‌آوران */
#[Fillable([
    'user_id', 'belt_id', 'coach_id', 'name', 'slug', 'photo', 'birth_date',
    'gender', 'weight_class', 'club', 'city', 'bio',
    'is_national_team', 'is_featured', 'is_active', 'order',
])]
class Athlete extends Model
{
    use HasFactory, ResolvesMedia;

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'gender' => Gender::class,
            'is_national_team' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Belt, $this> */
    public function belt(): BelongsTo
    {
        return $this->belongsTo(Belt::class);
    }

    /** @return BelongsTo<Coach, $this> */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    /** @return HasMany<Achievement, $this> */
    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class)->orderByDesc('year');
    }

    /** @param Builder<$this> $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** @param Builder<$this> $query */
    public function scopeNationalTeam(Builder $query): void
    {
        $query->where('is_national_team', true);
    }

    /** @param Builder<$this> $query */
    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    /** @param Builder<$this> $query */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('order')->orderBy('name');
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->mediaUrl($this->photo));
    }

    protected function age(): Attribute
    {
        return Attribute::get(fn (): ?int => $this->birth_date?->age);
    }

    /** Medal tally keyed by rank, for the profile header. */
    protected function medalCounts(): Attribute
    {
        return Attribute::get(function (): array {
            $counts = $this->achievements
                ->groupBy(fn (Achievement $a) => $a->rank->value)
                ->map->count();

            return [
                MedalRank::Gold->value => $counts[MedalRank::Gold->value] ?? 0,
                MedalRank::Silver->value => $counts[MedalRank::Silver->value] ?? 0,
                MedalRank::Bronze->value => $counts[MedalRank::Bronze->value] ?? 0,
            ];
        });
    }
}
