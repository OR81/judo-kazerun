<?php

declare(strict_types=1);

namespace App\Modules\Training\Http\Controllers;

use App\Enums\AgeGroup;
use App\Enums\Gender;
use App\Models\TrainingClass;
use App\Models\TrainingSession;
use App\Support\PersianNumber;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __invoke(): View
    {
        $classes = TrainingClass::query()
            ->active()
            ->with(['coach', 'sessions'])
            ->ordered()
            ->get();

        // Weekly grid, Saturday first — filtering happens client-side in filters.js
        // so switching a day never costs a round trip.
        $week = collect(range(0, 6))->map(fn (int $day) => [
            'index' => $day,
            'name' => PersianNumber::weekday($day),
            'isToday' => $day === TrainingSession::fromCarbonDay(now()->dayOfWeek),
            'sessions' => $classes
                ->flatMap(fn (TrainingClass $class) => $class->sessions
                    ->where('day_of_week', $day)
                    ->map(fn (TrainingSession $session) => [
                        'session' => $session,
                        'class' => $class,
                    ]))
                ->sortBy(fn (array $row) => $row['session']->start_time)
                ->values(),
        ]);

        return view('pages.schedule', [
            'classes' => $classes,
            'week' => $week,
            'ageGroups' => AgeGroup::options(),
            'genders' => Gender::options(),
            'todayIndex' => TrainingSession::fromCarbonDay(now()->dayOfWeek),
        ]);
    }
}
