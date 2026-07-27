<?php

declare(strict_types=1);

namespace App\Modules\Core\Http\Controllers;

use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Event;
use App\Models\GalleryAlbum;
use App\Models\News;
use App\Models\Slider;
use App\Models\Sponsor;
use App\Models\TrainingSession;
use App\Support\PersianNumber;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $todayIndex = TrainingSession::fromCarbonDay(now()->dayOfWeek);

        return view('pages.home', [
            'slides' => Slider::active()->ordered()->get(),

            'latestNews' => News::query()
                ->published()
                ->with('category')
                ->latestFirst()
                ->limit(4)
                ->get(),

            'featuredNews' => News::query()
                ->published()
                ->featured()
                ->with('category')
                ->latestFirst()
                ->first(),

            // "Today on the mat" — sessions matching the current weekday.
            'todaySessions' => TrainingSession::query()
                ->with('trainingClass.coach')
                ->where('day_of_week', $todayIndex)
                ->whereHas('trainingClass', fn ($q) => $q->where('is_active', true))
                ->orderBy('start_time')
                ->get(),

            'todayLabel' => PersianNumber::weekday($todayIndex),

            'upcomingEvents' => Event::query()->upcoming()->limit(4)->get(),

            'coaches' => Coach::query()->active()->featured()->ordered()->limit(4)->get(),

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
}
