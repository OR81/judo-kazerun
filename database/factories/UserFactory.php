<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            // 09 plus nine digits — the only shape the application accepts.
            'mobile' => '09'.fake()->unique()->numerify('#########'),
            'role' => UserRole::Athlete,
            'national_code' => fake()->unique()->numerify('##########'),
            'gender' => Gender::Male,
            'city' => 'کازرون',
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => UserRole::Admin]);
    }

    public function coach(): static
    {
        return $this->state(fn () => ['role' => UserRole::Coach]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
