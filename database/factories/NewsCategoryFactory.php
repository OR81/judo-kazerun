<?php

namespace Database\Factories;

use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<NewsCategory> */
class NewsCategoryFactory extends Factory
{
    protected $model = NewsCategory::class;

    public function definition(): array
    {
        $name = 'دستهٔ '.Str::random(5);

        return [
            'name' => $name,
            'slug' => fa_slug($name),
            'color' => 'brand',
            'order' => 0,
        ];
    }
}
