<?php

namespace App\Models;

use App\Models\Concerns\ResolvesMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** سالن‌های خانهٔ جودو — the halls the board owns and rents out */
#[Fillable([
    'name', 'slug', 'tagline', 'description', 'tatami_area', 'capacity',
    'session_rate', 'features', 'image', 'is_active', 'order',
])]
class Venue extends Model
{
    use HasFactory, ResolvesMedia;

    protected function casts(): array
    {
        return [
            'tatami_area' => 'integer',
            'capacity' => 'integer',
            'session_rate' => 'integer',
            'features' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return HasMany<VenueSlot, $this> */
    public function slots(): HasMany
    {
        return $this->hasMany(VenueSlot::class)
            ->orderBy('day_of_week')
            ->orderBy('start_time');
    }

    /** @param Builder<$this> $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** @param Builder<$this> $query */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('order')->orderBy('name');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->mediaUrl($this->image));
    }

    /** «۴۵۰٬۰۰۰ تومان» — the asking price for one session in this hall. */
    protected function rateLabel(): Attribute
    {
        return Attribute::get(fn (): string => $this->session_rate > 0
            ? toman($this->session_rate)
            : 'رایگان');
    }
}
