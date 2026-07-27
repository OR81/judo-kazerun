<?php

declare(strict_types=1);

namespace App\Modules\People\Http\Controllers;

use App\Enums\MedalRank;
use App\Models\Achievement;
use App\Models\Athlete;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class AthleteController extends Controller
{
    public function index(): View
    {
        return $this->list(nationalOnly: false);
    }

    public function national(): View
    {
        return $this->list(nationalOnly: true);
    }

    private function list(bool $nationalOnly): View
    {
        $query = Athlete::query()
            ->active()
            ->with(['belt', 'achievements', 'coach'])
            ->ordered();

        if ($nationalOnly) {
            $query->nationalTeam();
        }

        $athletes = $query->get();

        return view('pages.athletes.index', [
            'athletes' => $athletes,
            // Queried directly rather than flat-mapped off $athletes, so the
            // honour-roll table gets its athlete relation eager-loaded.
            'honourRoll' => Achievement::query()
                ->whereIn('athlete_id', $athletes->modelKeys())
                ->with('athlete')
                ->orderByDesc('year')
                ->limit(12)
                ->get(),
            'nationalOnly' => $nationalOnly,
            'nationalCount' => Athlete::query()->active()->nationalTeam()->count(),
            'totals' => [
                MedalRank::Gold->value => Achievement::query()->where('rank', MedalRank::Gold)->count(),
                MedalRank::Silver->value => Achievement::query()->where('rank', MedalRank::Silver)->count(),
                MedalRank::Bronze->value => Achievement::query()->where('rank', MedalRank::Bronze)->count(),
            ],
        ]);
    }

    public function show(Athlete $athlete): View
    {
        abort_unless($athlete->is_active, 404);

        return view('pages.athletes.show', [
            'athlete' => $athlete->load(['belt', 'coach', 'achievements']),
            'others' => Athlete::query()
                ->active()
                ->whereKeyNot($athlete->getKey())
                ->with(['belt', 'achievements'])
                ->ordered()
                ->limit(4)
                ->get(),
        ]);
    }
}
