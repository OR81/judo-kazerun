<?php

declare(strict_types=1);

namespace App\Modules\People\Http\Controllers;

use App\Models\Coach;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class CoachController extends Controller
{
    public function index(): View
    {
        return view('pages.coaches.index', [
            'coaches' => Coach::query()->active()->with('belt')->ordered()->get(),
        ]);
    }

    public function show(Coach $coach): View
    {
        abort_unless($coach->is_active, 404);

        return view('pages.coaches.show', [
            'coach' => $coach->load(['belt', 'trainingClasses.sessions', 'athletes']),
            'others' => Coach::query()
                ->active()
                ->whereKeyNot($coach->getKey())
                ->ordered()
                ->limit(3)
                ->get(),
        ]);
    }
}
