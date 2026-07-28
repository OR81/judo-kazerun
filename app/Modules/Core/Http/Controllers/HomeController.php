<?php

declare(strict_types=1);

namespace App\Modules\Core\Http\Controllers;

use App\Enums\SlotStatus;
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Event;
use App\Models\GalleryAlbum;
use App\Models\News;
use App\Models\Sponsor;
use App\Models\TrainingClass;
use App\Models\TrainingSession;
use App\Models\Venue;
use App\Models\VenueSlot;
use App\Support\PersianNumber;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * The home page is built around the building: what the board owns is a hall, and
 * the first question a visitor arrives with is «کدام سانس آزاد است؟». Everything
 * else — classes, news, honours — sits below that answer.
 *
 * The whole week's slots are fetched once and grouped in PHP rather than run as
 * seven queries: it is a few dozen rows, and the board renders every day into the
 * markup so the tabs work as a pure display toggle (and the page still lists all
 * seven days with JavaScript off).
 */
class HomeController extends Controller
{
    public function __invoke(): View
    {
        $todayIndex = TrainingSession::fromCarbonDay(now()->dayOfWeek);

        $venues = Venue::query()->active()->ordered()->get();

        $slots = VenueSlot::query()
            ->whereIn('venue_id', $venues->modelKeys())
            ->with(['venue', 'trainingClass.coach'])
            ->ordered()
            ->get();

        $todaySlots = $slots->where('day_of_week', $todayIndex);

        return view('pages.home', [
            'hall' => $venues->first(),
            'venues' => $venues,

            'week' => $this->week($slots, $todayIndex),
            'todayIndex' => $todayIndex,
            'todayLabel' => PersianNumber::weekday($todayIndex),

            // The slot on the mat right now, else the next one still to come today.
            'currentSlot' => $todaySlots->first(fn (VenueSlot $slot) => $slot->is_running_now),
            'nextSlot' => $this->nextSlot($todaySlots),
            'openToday' => $todaySlots->where('status', SlotStatus::Open),
            'openThisWeek' => $slots->where('status', SlotStatus::Open),

            'hours' => $this->hallHours($todayIndex),

            'classes' => TrainingClass::query()
                ->active()
                ->with(['coach', 'sessions'])
                ->ordered()
                ->limit(3)
                ->get(),

            'classCount' => TrainingClass::query()->active()->count(),

            'coaches' => Coach::query()->active()->ordered()->limit(6)->get(),

            'latestNews' => News::query()
                ->published()
                ->with('category')
                ->latestFirst()
                ->limit(3)
                ->get(),

            'upcomingEvents' => Event::query()->upcoming()->limit(3)->get(),

            'champions' => Athlete::query()
                ->active()
                ->featured()
                ->with(['belt', 'achievements'])
                ->ordered()
                ->limit(4)
                ->get(),

            'albums' => GalleryAlbum::query()
                ->active()
                ->with('items')
                ->ordered()
                ->limit(3)
                ->get(),

            'sponsors' => Sponsor::query()->active()->ordered()->get(),

            'stats' => [
                ['value' => setting('stat_athletes'), 'label' => 'ورزشکار فعال', 'icon' => 'fa-users'],
                ['value' => setting('stat_coaches'), 'label' => 'مربی رسمی', 'icon' => 'fa-chalkboard-user'],
                ['value' => setting('stat_medals'), 'label' => 'مدال کسب‌شده', 'icon' => 'fa-medal'],
                ['value' => setting('stat_clubs'), 'label' => 'باشگاه تحت پوشش', 'icon' => 'fa-building'],
            ],
        ]);
    }

    /**
     * The seven day panels of the hall board, today first in emphasis but never
     * reordered — a weekly timetable that starts on a different day each visit is
     * harder to read, not easier.
     *
     * @param  Collection<int, VenueSlot>  $slots
     * @return Collection<int, array<string, mixed>>
     */
    private function week(Collection $slots, int $todayIndex): Collection
    {
        return collect(range(0, 6))->map(function (int $day) use ($slots, $todayIndex): array {
            $ofDay = $slots->where('day_of_week', $day)->values();

            return [
                'index' => $day,
                'name' => PersianNumber::weekday($day),
                'isToday' => $day === $todayIndex,
                'slots' => $ofDay,
                'openCount' => $ofDay->where('status', SlotStatus::Open)->count(),
            ];
        });
    }

    /**
     * The next slot to start today, of any kind — what a visitor standing outside
     * the door actually wants to know.
     *
     * @param  Collection<int, VenueSlot>  $todaySlots
     */
    private function nextSlot(Collection $todaySlots): ?VenueSlot
    {
        $now = now()->format('H:i:s');

        return $todaySlots
            ->sortBy('start_time')
            ->first(fn (VenueSlot $slot) => VenueSlot::asTime($slot->start_time)->format('H:i:s') > $now);
    }

    /**
     * Whether the hall is open right now, and until when.
     *
     * Kept on the server rather than in JavaScript so the answer is correct in the
     * board's own timezone (Asia/Tehran) instead of the visitor's device clock.
     *
     * @return array{isOpen: bool, from: string, to: string, closesAt: string, opensAt: string}
     */
    private function hallHours(int $todayIndex): array
    {
        $isFriday = $todayIndex === 6;

        $from = (string) setting($isFriday ? 'hall_friday_open_from' : 'hall_open_from', '08:00');
        $to = (string) setting($isFriday ? 'hall_friday_open_to' : 'hall_open_to', '22:00');

        $now = now()->format('H:i');

        return [
            'isOpen' => $now >= $from && $now < $to,
            'from' => $from,
            'to' => $to,
            'closesAt' => $to,
            'opensAt' => $now < $from ? $from : (string) setting('hall_open_from', '08:00'),
        ];
    }
}
