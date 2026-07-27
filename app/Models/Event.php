<?php

namespace App\Models;

use App\Enums\EventType;
use App\Models\Concerns\ResolvesMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** رویدادها — مسابقات، آزمون دان، اردوها و سمینارها */
#[Fillable([
    'title', 'slug', 'type', 'summary', 'description', 'poster',
    'location', 'organizer', 'starts_at', 'ends_at', 'registration_deadline',
    'capacity', 'fee', 'age_groups', 'status', 'is_featured',
])]
class Event extends Model
{
    use HasFactory, ResolvesMedia;

    protected function casts(): array
    {
        return [
            'type' => EventType::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'registration_deadline' => 'datetime',
            'capacity' => 'integer',
            'fee' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return HasMany<GalleryAlbum, $this> */
    public function albums(): HasMany
    {
        return $this->hasMany(GalleryAlbum::class);
    }

    /** @param Builder<$this> $query */
    public function scopeUpcoming(Builder $query): void
    {
        $query->where('starts_at', '>=', now())->orderBy('starts_at');
    }

    /** @param Builder<$this> $query */
    public function scopePast(Builder $query): void
    {
        $query->where('starts_at', '<', now())->orderByDesc('starts_at');
    }

    /** @param Builder<$this> $query */
    public function scopeOfType(Builder $query, ?string $type): void
    {
        if (filled($type) && $type !== 'all') {
            $query->where('type', $type);
        }
    }

    protected function posterUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->mediaUrl($this->poster));
    }

    protected function isRegistrationOpen(): Attribute
    {
        return Attribute::get(fn (): bool => $this->registration_deadline === null
            ? $this->starts_at->isFuture()
            : $this->registration_deadline->isFuture());
    }

    /** Whole days until the event — drives the countdown chip. */
    protected function daysUntil(): Attribute
    {
        return Attribute::get(fn (): int => max(0, (int) now()->startOfDay()->diffInDays($this->starts_at->startOfDay(), false)));
    }
}
