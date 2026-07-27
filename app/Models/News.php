<?php

namespace App\Models;

use App\Models\Concerns\ResolvesMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** اخبار و اطلاعیه‌ها */
#[Fillable([
    'news_category_id', 'author_id', 'title', 'slug', 'excerpt', 'body',
    'cover_image', 'is_featured', 'views', 'read_minutes', 'published_at',
])]
class News extends Model
{
    use HasFactory, ResolvesMedia;

    protected $table = 'news';

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'views' => 'integer',
            'read_minutes' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<NewsCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scheduled posts stay hidden until their moment arrives.
     *
     * @param  Builder<$this>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /** @param Builder<$this> $query */
    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    /** @param Builder<$this> $query */
    public function scopeLatestFirst(Builder $query): void
    {
        $query->orderByDesc('published_at');
    }

    /** @param Builder<$this> $query */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (blank($term)) {
            return;
        }

        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';

        $query->where(fn (Builder $q) => $q
            ->where('title', 'like', $like)
            ->orWhere('excerpt', 'like', $like)
            ->orWhere('body', 'like', $like));
    }

    protected function coverUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->mediaUrl($this->cover_image));
    }

    public function incrementViews(): void
    {
        // No timestamp churn — a view isn't an edit.
        $this->newQuery()->whereKey($this->getKey())->update(['views' => $this->views + 1]);
    }
}
