<?php

use App\Enums\SlotStatus;
use App\Models\TrainingSession;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueSlot;

beforeEach(fn () => seedAll());

it('puts the judo house on the home page', function () {
    $hall = Venue::query()->active()->ordered()->first();

    $this->get('/')
        ->assertOk()
        ->assertSee($hall->name)
        ->assertSee('تابلوی سانس‌ها')
        ->assertSee('اجارهٔ سالن');
});

it('renders all seven days of the hall board so it works without javascript', function () {
    $response = $this->get('/')->assertOk();

    foreach (range(0, 6) as $day) {
        $response->assertSee('data-hall-panel="'.$day.'"', false);
    }
});

it('marks today as the selected day', function () {
    $today = TrainingSession::fromCarbonDay(now()->dayOfWeek);

    $this->get('/')
        ->assertOk()
        ->assertSee('id="hall-tab-'.$today.'"', false)
        ->assertSee('aria-controls="hall-panel-'.$today.'"', false);
});

it('shows a price and a booking link only on free slots', function () {
    $free = VenueSlot::query()->open()->with('venue')->first();

    $this->get('/')
        ->assertOk()
        ->assertSee('درخواست رزرو')
        ->assertSee(fa_number($free->effective_price), false);
});

it('names the slot in the contact form when a booking is requested', function () {
    $free = VenueSlot::query()->open()->with('venue')->first();
    $subject = 'درخواست اجارهٔ سانس — '.$free->day_name.' '.$free->time_range.' — '.$free->venue->name;

    $this->get('/contact?subject='.urlencode($subject))
        ->assertOk()
        ->assertSee($subject, false);
});

it('falls back to the hall rate when a slot has no price of its own', function () {
    $hall = Venue::query()->first();

    $slot = VenueSlot::factory()->make([
        'venue_id' => $hall->id,
        'price' => null,
    ]);
    $slot->save();

    expect($slot->fresh()->load('venue')->effective_price)->toBe($hall->session_rate);
});

it('keeps a hall in one state at a time', function () {
    // The seeder derives class slots from the real timetable and then skips any
    // rental that would overlap one, so no hall is ever double-booked.
    $clashes = VenueSlot::query()
        ->get()
        ->groupBy(fn (VenueSlot $slot) => $slot->venue_id.'-'.$slot->day_of_week)
        ->flatMap(fn ($slots) => $slots
            ->crossJoin($slots)
            ->filter(fn ($pair) => $pair[0]->id < $pair[1]->id)
            ->filter(fn ($pair) => VenueSlot::asTime($pair[0]->start_time) < VenueSlot::asTime($pair[1]->end_time)
                && VenueSlot::asTime($pair[0]->end_time) > VenueSlot::asTime($pair[1]->start_time)));

    expect($clashes)->toBeEmpty();
});

it('mirrors every class session onto the hall board', function () {
    $sessions = TrainingSession::query()->count();

    expect(VenueSlot::query()->where('status', SlotStatus::BoardClass)->count())->toBe($sessions);
});

it('lets the admin manage halls and slots', function () {
    $this->actingAs(User::query()->where('role', 'admin')->first());

    $this->get('/admin/venues')->assertOk();
    $this->get('/admin/venue-slots')->assertOk();
});
