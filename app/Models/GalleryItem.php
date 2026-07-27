<?php

namespace App\Models;

use App\Enums\GalleryType;
use App\Models\Concerns\ResolvesMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'gallery_album_id', 'type', 'path', 'thumbnail',
    'caption', 'width', 'height', 'order',
])]
class GalleryItem extends Model
{
    use HasFactory, ResolvesMedia;

    protected function casts(): array
    {
        return [
            'type' => GalleryType::class,
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    /** @return BelongsTo<GalleryAlbum, $this> */
    public function album(): BelongsTo
    {
        return $this->belongsTo(GalleryAlbum::class, 'gallery_album_id');
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->mediaUrl($this->path));
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->mediaUrl($this->thumbnail ?: $this->path));
    }

    /**
     * Intrinsic ratio so the masonry grid can reserve space before the image
     * arrives, which keeps CLS at zero.
     */
    protected function aspectRatio(): Attribute
    {
        return Attribute::get(fn (): string => $this->width && $this->height
            ? "{$this->width} / {$this->height}"
            : '4 / 3');
    }
}
