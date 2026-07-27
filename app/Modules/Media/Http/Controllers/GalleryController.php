<?php

declare(strict_types=1);

namespace App\Modules\Media\Http\Controllers;

use App\Enums\GalleryType;
use App\Models\GalleryAlbum;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        return $this->render(GalleryType::Photo);
    }

    public function videos(): View
    {
        return $this->render(GalleryType::Video);
    }

    private function render(GalleryType $type): View
    {
        return view('pages.gallery.index', [
            'albums' => GalleryAlbum::query()
                ->active()
                ->where('type', $type)
                ->with('items')
                ->ordered()
                ->get(),
            'activeType' => $type,
            'counts' => [
                GalleryType::Photo->value => GalleryAlbum::query()->active()->where('type', GalleryType::Photo)->count(),
                GalleryType::Video->value => GalleryAlbum::query()->active()->where('type', GalleryType::Video)->count(),
            ],
        ]);
    }

    public function show(GalleryAlbum $album): View
    {
        abort_unless($album->is_active, 404);

        return view('pages.gallery.show', [
            'album' => $album->load('items', 'event'),
            'others' => GalleryAlbum::query()
                ->active()
                ->whereKeyNot($album->getKey())
                ->with('items')
                ->ordered()
                ->limit(3)
                ->get(),
        ]);
    }
}
