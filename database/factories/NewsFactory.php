<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<News> */
class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition(): array
    {
        $title = 'خبر آزمایشی '.Str::random(6);

        return [
            'news_category_id' => NewsCategory::query()->inRandomOrder()->value('id')
                ?? NewsCategory::factory(),
            'title' => $title,
            'slug' => fa_slug($title),
            'excerpt' => 'خلاصهٔ کوتاه خبر آزمایشی برای بررسی رفتار سامانه.',
            'body' => '<p>متن کامل خبر آزمایشی.</p>',
            'cover_image' => 'https://picsum.photos/seed/test/1200/700',
            'is_featured' => false,
            'views' => 0,
            'read_minutes' => 3,
            'published_at' => now()->subDay(),
        ];
    }

    public function scheduled(): static
    {
        return $this->state(fn () => ['published_at' => now()->addWeek()]);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['published_at' => null]);
    }
}
