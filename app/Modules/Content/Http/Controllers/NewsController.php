<?php

declare(strict_types=1);

namespace App\Modules\Content\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->string('category')->toString();
        $term = $request->string('q')->toString();

        $query = News::query()
            ->published()
            ->with('category')
            ->search($term)
            ->latestFirst();

        if ($category) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $category));
        }

        // The featured hero only makes sense on an unfiltered first page.
        $featured = ! $category && ! $term && $request->integer('page', 1) === 1
            ? News::query()->published()->featured()->with('category')->latestFirst()->first()
            : null;

        if ($featured) {
            $query->whereKeyNot($featured->getKey());
        }

        return view('pages.news.index', [
            'featured' => $featured,
            'articles' => $query->paginate(9)->withQueryString(),
            'categories' => NewsCategory::query()->withCount('news')->ordered()->get(),
            'activeCategory' => $category,
            'term' => $term,
        ]);
    }

    public function show(News $news): View
    {
        abort_if($news->published_at === null || $news->published_at->isFuture(), 404);

        $news->loadMissing(['category', 'author']);
        $news->incrementViews();

        return view('pages.news.show', [
            'article' => $news,
            'related' => News::query()
                ->published()
                ->where('news_category_id', $news->news_category_id)
                ->whereKeyNot($news->getKey())
                ->with('category')
                ->latestFirst()
                ->limit(3)
                ->get(),
        ]);
    }
}
