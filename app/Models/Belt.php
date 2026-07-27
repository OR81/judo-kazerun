<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** کمربندها — سفید تا مشکی و درجات دان */
#[Fillable(['name', 'slug', 'color', 'dan_level', 'order'])]
class Belt extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['dan_level' => 'integer', 'order' => 'integer'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return HasMany<Athlete, $this> */
    public function athletes(): HasMany
    {
        return $this->hasMany(Athlete::class);
    }

    /** @return HasMany<Coach, $this> */
    public function coaches(): HasMany
    {
        return $this->hasMany(Coach::class);
    }

    /** @param Builder<$this> $query */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('order');
    }

    public function fullName(): string
    {
        return $this->dan_level
            ? "{$this->name} (دان {$this->dan_level})"
            : $this->name;
    }
}
