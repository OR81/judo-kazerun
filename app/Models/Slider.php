<?php

namespace App\Models;

use App\Models\Concerns\ResolvesMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** اسلایدهای صفحهٔ اصلی */
#[Fillable([
    'title', 'subtitle', 'description', 'image', 'eyebrow',
    'cta_label', 'cta_url', 'secondary_cta_label', 'secondary_cta_url',
    'is_active', 'order',
])]
class Slider extends Model
{
    use HasFactory, ResolvesMedia;

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /** @param Builder<$this> $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** @param Builder<$this> $query */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('order');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->mediaUrl($this->image));
    }
}
