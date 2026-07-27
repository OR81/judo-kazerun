<?php

namespace App\Models;

use App\Enums\BoardPosition;
use App\Models\Concerns\ResolvesMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** اعضای هیئت‌رئیسه و کمیته‌ها */
#[Fillable([
    'name', 'slug', 'position', 'committee', 'photo', 'summary',
    'bio', 'phone', 'email', 'is_active', 'order',
])]
class BoardMember extends Model
{
    use HasFactory, ResolvesMedia;

    protected function casts(): array
    {
        return [
            'position' => BoardPosition::class,
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
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

    /** The four officers, who head the board page. */
    public function scopeOfficers(Builder $query): void
    {
        $query->whereIn('position', [
            BoardPosition::President->value,
            BoardPosition::VicePresident->value,
            BoardPosition::Secretary->value,
            BoardPosition::Treasurer->value,
        ]);
    }

    /** @param Builder<$this> $query */
    public function scopeCommittees(Builder $query): void
    {
        $query->whereIn('position', [
            BoardPosition::CommitteeHead->value,
            BoardPosition::Member->value,
        ]);
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->mediaUrl($this->photo));
    }
}
