@extends('layouts.app')

@section('title', $article->title.' — هیئت جودو کازرون')
@section('meta_description', $article->excerpt)
@section('og_image', $article->cover_url)

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $article->title,
            'description' => $article->excerpt,
            'image' => $article->cover_url,
            'datePublished' => $article->published_at?->toIso8601String(),
            'author' => ['@type' => 'Organization', 'name' => setting('site_title')],
            'publisher' => ['@type' => 'Organization', 'name' => setting('site_title')],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('content')

    <x-ui.page-header
        :eyebrow="$article->category->name"
        :title="$article->title"
        :breadcrumbs="[
            ['label' => 'اخبار', 'url' => route('news.index')],
            ['label' => $article->category->name, 'url' => route('news.index', ['category' => $article->category->slug])],
            ['label' => \Illuminate\Support\Str::limit($article->title, 40)],
        ]" />

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
        <div class="grid gap-10 lg:grid-cols-12">

            <article class="lg:col-span-8">
                <img src="{{ $article->cover_url }}" alt="" fetchpriority="high" decoding="async"
                     class="aspect-16/9 w-full rounded-panel object-cover">

                <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-2 border-b border-line pb-6 text-sm text-muted">
                    <time datetime="{{ shamsi_attr($article->published_at) }}" class="flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar text-xs" aria-hidden="true"></i>
                        {{ shamsi($article->published_at, 'full') }}
                    </time>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-clock text-xs" aria-hidden="true"></i>
                        {{ fa($article->read_minutes) }} دقیقه مطالعه
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-eye text-xs" aria-hidden="true"></i>
                        {{ fa_number($article->views) }} بازدید
                    </span>
                </div>

                <p class="mt-6 text-lg leading-loose font-medium text-heading">{{ $article->excerpt }}</p>

                {{--
                    Article bodies are authored by board staff through the admin panel,
                    which sanitises input, so the stored HTML is rendered as-is.
                --}}
                <div class="prose-article mt-6">
                    {!! $article->body !!}
                </div>

                {{-- Share --}}
                <div class="mt-10 flex flex-wrap items-center gap-3 border-t border-line pt-6">
                    <span class="text-sm font-semibold text-heading">هم‌رسانی:</span>

                    @php $shareUrl = urlencode(url()->current()); $shareText = urlencode($article->title); @endphp

                    <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareText }}"
                       target="_blank" rel="noopener noreferrer"
                       class="grid h-10 w-10 place-items-center rounded-xl border border-line text-muted transition hover:border-brand hover:text-brand-text"
                       aria-label="هم‌رسانی در تلگرام">
                        <x-icons.telegram class="h-4 w-4" />
                    </a>

                    <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}"
                       target="_blank" rel="noopener noreferrer"
                       class="grid h-10 w-10 place-items-center rounded-xl border border-line text-muted transition hover:border-brand hover:text-brand-text"
                       aria-label="هم‌رسانی در واتس‌اپ">
                        <x-icons.whatsapp class="h-4 w-4" />
                    </a>
                </div>
            </article>

            {{-- ================================================= sidebar --}}
            <aside class="space-y-6 lg:col-span-4">
                <div class="surface-card sticky top-24 p-6">
                    <h2 class="font-bold text-heading">اخبار مرتبط</h2>

                    @if ($related->isEmpty())
                        <p class="mt-4 text-sm text-muted">خبر مرتبط دیگری در این دسته ثبت نشده است.</p>
                    @else
                        <ul class="mt-4 space-y-4">
                            @foreach ($related as $item)
                                <li>
                                    <a href="{{ route('news.show', $item) }}" class="group flex gap-3">
                                        <img src="{{ $item->cover_url }}" alt="" loading="lazy"
                                             class="h-16 w-20 shrink-0 rounded-xl object-cover">
                                        <span class="min-w-0">
                                            <span class="line-clamp-2 text-sm leading-snug font-semibold text-heading transition group-hover:text-brand-text">
                                                {{ $item->title }}
                                            </span>
                                            <span class="mt-1 block text-xs text-muted">{{ shamsi($item->published_at) }}</span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <x-ui.button :href="route('news.index')" variant="outline" size="sm"
                                 icon-end="fa-arrow-left" class="mt-6 w-full">
                        آرشیو کامل اخبار
                    </x-ui.button>
                </div>
            </aside>
        </div>
    </div>

@endsection
