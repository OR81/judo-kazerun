<?php

declare(strict_types=1);

namespace App\Modules\People\Http\Controllers;

use App\Models\BoardMember;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class BoardController extends Controller
{
    public function __invoke(): View
    {
        $members = BoardMember::query()->active()->ordered()->get();

        return view('pages.board', [
            // The four officers head the page; the rest group under their committee.
            'officers' => $members->filter(fn (BoardMember $m) => $m->position->isOfficer()),
            'committees' => $members
                ->reject(fn (BoardMember $m) => $m->position->isOfficer())
                ->groupBy(fn (BoardMember $m) => $m->committee ?? 'سایر اعضا'),
        ]);
    }
}
