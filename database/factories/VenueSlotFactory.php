<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\SlotStatus;
use App\Models\Venue;
use App\Models\VenueSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<VenueSlot> */
class VenueSlotFactory extends Factory
{
    protected $model = VenueSlot::class;

    public function definition(): array
    {
        return [
            'venue_id' => Venue::query()->inRandomOrder()->value('id'),
            'training_class_id' => null,
            'day_of_week' => fake()->numberBetween(0, 6),
            // Well outside the seeded programme, so a made-up slot never collides
            // with a real one in tests that check for overlaps.
            'start_time' => '05:00',
            'end_time' => '06:30',
            'status' => SlotStatus::Open,
            'gender' => Gender::Mixed,
            'holder' => null,
            'price' => null,
            'note' => null,
        ];
    }

    public function booked(string $holder = 'باشگاه آزمایشی'): static
    {
        return $this->state(fn () => ['status' => SlotStatus::Booked, 'holder' => $holder]);
    }
}
