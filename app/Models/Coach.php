<?php

namespace App\Models;

use App\Models\Concerns\ResolvesMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** مربیان هیئت */
#[Fillable([
    'user_id', 'belt_id', 'name', 'slug', 'photo', 'title', 'dan_rank',
    'summary', 'bio', 'experience_years', 'specialties', 'certificates',
    'phone', 'email', 'instagram', 'is_featured', 'is_active', 'order',
])]
class Coach extends Model
{
    use HasFactory, ResolvesMedia;

    protected function casts(): array
    {
        return [
            'specialties' => 'array',
            'certificates' => 'array',
            'dan_rank' => 'integer',
            'experience_years' => 'integer',
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

    /** @return HasMany<TrainingClass, $this> */
    public function trainingClasses(): HasMany
    {
        return $this->hasMany(TrainingClass::class);
    }

    /** @return HasMany<Athlete, $this> */
    public function athletes(): HasMany
    {
        return $this->hasMany(Athlete::class);
    }

    /** @param Builder<$this> $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
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

    /** «دان ۴» */
    protected function danLabel(): Attribute
    {
        return Attribute::get(
            fn (): string => $this->dan_rank ? 'دان '.fa($this->dan_rank) : ($this->belt?->name ?? '—')
        );
    }
}
