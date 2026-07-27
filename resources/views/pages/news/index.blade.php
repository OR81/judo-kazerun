@extends('layouts.app')

@section('title', 'اخبار و اطلاعیه‌ها — هیئت جودو کازرون')
@section('meta_description', 'آخرین اخبار، گزارش مسابقات، اطلاعیه‌های ثبت‌نام و مطالب آموزشی هیئت جودو شهرستان کازرون.')

@section('content')

    <x-ui.page-header
        eyebrow="رسانه"
        title="اخبار و اطلاعیه‌ها"
        description="گزارش مسابقات، اطلاعیه‌های ثبت‌نام، مطالب فنی و آموزشی هیئت جودو کازرون."
        :breadcrumbs="[['label' => 'اخبار']]" />

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">

        {{-- ================================================ search + filters --}}
        <div class="surface-card p-5">
            <form action="{{ route('news.index') }}" method="GET" role="search"
                  class="flex flex-col gap-3 sm:flex-row">
                <div class="flex-1">
                    <label for="news-search" class="sr-only">جستجو در اخبار</label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute inset-inline-start-4 top-1/2 -translate-y-1/2 text-sm text-muted"
                           aria-hidden="true"></i>
                        <input id="news-search" type="search" name="q" value="{{ $term }}"
                               placeholder="جستجوی عنوان یا متن خبر…"
                               class="w-full rounded-xl border border-line bg-surface py-3 ps-11 pe-4 text-sm text-heading transition placeholder:text-muted focus:border-brand">
                    </div>
                </div>

                @if ($activeCategory)
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif

                <x-ui.button type="submit" variant="primary" icon="fa-magnifying-glass">جستجو</x-ui.button>

                @if ($term || $activeCategory)
                    <x-ui.button :href="route('news.index')" variant="outline" icon="fa-xmark">حذف فیلتر</x-ui.button>
                @endif
            </form>

            <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('news.index', array_filter(['q' => $term])) }}"
                   class="rounded-xl border px-4 py-2 text-xs font-semibold transition
                          {{ ! $activeCategory ? 'border-brand bg-brand text-on-brand' : 'border-line bg-surface text-copy hover:border-line-strong' }}">
                    همهٔ دسته‌ها
                </a>

                @foreach ($categories as $category)
                    <a href="{{ route('news.index', array_filter(['category' => $category->slug, 'q' => $term])) }}"
                       class="rounded-xl border px-4 py-2 text-xs font-semibold transition
                              {{ $activeCategory === $category->slug ? 'border-brand bg-brand text-on-brand' : 'border-line bg-surface text-copy hover:border-line-strong' }}">
                        {{ $category->name }}
                        <span class="ms-1 opacity-70">({{ fa($category->news_count) }})</span>
                    </a>
                @endforeach
            </div>
        </div>

        @if ($term)
            <p class="mt-6 text-sm text-muted" role="status">
                نتایج جستجو برای «<span class="font-semibold text-heading">{{ $term }}</span>» —
                {{ fa_number($articles->total()) }} مورد یافت شد.
            </p>
        @endif

        {{-- ==================================================== featured --}}
        @if ($featured)
            <article class="reveal group surface-card relative mt-8 grid overflow-hidden lg:grid-cols-2">
                <div class="relative aspect-16/10 overflow-hidden lg:aspect-auto">
                    <img src="{{ $featured->cover_url }}" alt="" loading="eager" decoding="async"
                         class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                </div>

                <div class="flex flex-col justify-center p-8 lg:p-12">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-ui.badge variant="accent" icon="fa-star">مطلب ویژه</x-ui.badge>
                        <x-ui.badge :variant="$featured->category->badgeClass()">{{ $featured->category->name }}</x-ui.badge>
                    </div>

                    <h2 class="mt-4 text-2xl leading-snug font-extrabold text-balance text-heading lg:text-3xl">
                        <a href="{{ route('news.show', $featured) }}"
                           class="transition after:absolute after:inset-0 hover:text-brand-text">
                            {{ $featured->title }}
                        </a>
                    </h2>

                    <p class="mt-4 leading-relaxed text-muted">{{ $featured->excerpt }}</p>

                    <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-muted">
                        <time datetime="{{ shamsi_attr($featured->published_at) }}" class="flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar text-[0.7rem]" aria-hidden="true"></i>
                            {{ shamsi($featured->published_at) }}
                        </time>
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-clock text-[0.7rem]" aria-hidden="true"></i>
                            {{ fa($featured->read_minutes) }} دقیقه مطالعه
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-eye text-[0.7rem]" aria-hidden="true"></i>
                            {{ fa_number($featured->views) }} بازدید
                        </span>
                    </div>
                </div>
            </article>
        @endif

        {{-- ====================================================== list --}}
        @if ($articles->isEmpty())
            <x-ui.empty-state class="mt-10" icon="fa-newspaper"
                              title="خبری یافت نشد"
                              description="عبارت دیگری را جستجو کنید یا فیلتر دسته‌بندی را بردارید.">
                <x-ui.button :href="route('news.index')" variant="primary">مشاهدهٔ همهٔ اخبار</x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $item)
                    <x-cards.news :item="$item" data-reveal-delay="{{ ($loop->index % 3) * 90 }}" />
                @endforeach
            </div>

            {{ $articles->links() }}
        @endif
    </div>

@endsection
