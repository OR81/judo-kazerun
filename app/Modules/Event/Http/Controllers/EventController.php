<?php

declare(strict_types=1);

namespace App\Modules\Event\Http\Controllers;

use App\Enums\EventType;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('type')->toString() ?: 'all';

        $upcoming = Event::query()->ofType($type)->upcoming()->get();
        $past = Event::query()->ofType($type)->past()->limit(12)->get();

        return view('pages.events.index', [
            'upcoming' => $upcoming,
            'past' => $past,
            'activeType' => $type,
            'types' => EventType::options(),
            // Grouped by Shamsi month for the calendar rail.
            'byMonth' => $upcoming->groupBy(fn (Event $event) => shamsi($event->starts_at, 'month')),
        ]);
    }

    public function show(Event $event): View
    {
        return view('pages.events.show', [
            'event' => $event->load('albums'),
            'related' => Event::query()
                ->where('type', $event->type)
                ->whereKeyNot($event->getKey())
                ->upcoming()
                ->limit(3)
                ->get(),
        ]);
    }
}
