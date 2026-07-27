<?php

namespace App\Models;

use App\Enums\GalleryType;
use App\Models\Concerns\ResolvesMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** آلبوم‌های گالری */
#[Fillable([
    'event_id', 'title', 'slug', 'description', 'cover_image',
    'type', 'taken_on', 'is_featured', 'is_active', 'order',
])]
class GalleryAlbum extends Model
{
    use HasFactory, ResolvesMedia;

    protected function casts(): array
    {
        return [
            'type' => GalleryType::class,
            'taken_on' => 'date',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<Event, $this> */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /** @return HasMany<GalleryItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class)->orderBy('order');
    }

    /** @param Builder<$this> $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** @param Builder<$this> $query */
    public function scopeOfType(Builder $query, ?string $type): void
    {
        if (filled($type) && $type !== 'all') {
            $query->where('type', $type);
        }
    }

    /** @param Builder<$this> $query */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('order')->orderByDesc('taken_on');
    }

    protected function coverUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->mediaUrl(
            $this->cover_image ?: $this->items->first()?->path
        ));
    }
}
