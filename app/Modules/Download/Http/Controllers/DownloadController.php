<?php

declare(strict_types=1);

namespace App\Modules\Download\Http\Controllers;

use App\Enums\DownloadCategory;
use App\Models\Download;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function index(): View
    {
        return view('pages.downloads', [
            'groups' => Download::query()
                ->active()
                ->ordered()
                ->get()
                ->groupBy(fn (Download $file) => $file->category->value),
            'categories' => DownloadCategory::cases(),
        ]);
    }

    public function file(Download $download): StreamedResponse|RedirectResponse
    {
        abort_unless($download->is_active, 404);

        // Seed rows point at paths with no real binary behind them. Say so
        // plainly rather than throwing a 500 at the visitor.
        if (! Storage::disk('public')->exists($download->file_path)) {
            return back()->with('warning', 'این فایل هنوز بارگذاری نشده است. لطفاً با دفتر هیئت تماس بگیرید.');
        }

        $download->registerDownload();

        return Storage::disk('public')->download($download->file_path, $download->file_name);
    }
}
