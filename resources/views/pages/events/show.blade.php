@extends('layouts.app')

@section('title', $event->title.' — رویدادهای هیئت جودو کازرون')
@section('meta_description', $event->summary)
@section('og_image', $event->poster_url)

@section('content')

    <x-ui.page-header
        :eyebrow="$event->type->label()"
        :title="$event->title"
        :description="$event->summary"
        :breadcrumbs="[
            ['label' => 'رویدادها', 'url' => route('events.index')],
            ['label' => \Illuminate\Support\Str::limit($event->title, 40)],
        ]">

        @if ($event->is_registration_open)
            <x-ui.button :href="route('register')" variant="primary" icon="fa-user-plus">ثبت‌نام در رویداد</x-ui.button>
        @else
            <span class="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-white/5 px-5 py-2.5 text-sm font-semibold text-on-ink">
                <i class="fa-solid fa-lock text-xs" aria-hidden="true"></i>
                مهلت ثبت‌نام به پایان رسیده
            </span>
        @endif
    </x-ui.page-header>

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
        <div class="grid gap-10 lg:grid-cols-12">

            <div class="lg:col-span-8">
                <img src="{{ $event->poster_url }}" alt="" fetchpriority="high" decoding="async"
                     class="aspect-16/9 w-full rounded-panel object-cover">

                <div class="prose-article mt-8">
                    {!! $event->description !!}
                </div>

                @if ($event->albums->isNotEmpty())
                    <section class="mt-12">
                        <h2 class="text-xl font-extrabold text-heading">گالری این رویداد</h2>

                        <div class="mt-5 grid gap-4 sm:grid-cols-3">
                            @foreach ($event->albums as $album)
                                <a href="{{ route('gallery.show', $album) }}"
                                   class="group relative aspect-4/3 overflow-hidden rounded-card">
                                    <img src="{{ $album->cover_url }}" alt="" loading="lazy"
                                         class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    <div class="absolute inset-0 bg-gradient-to-t from-ink/80 to-transparent"></div>
                                    <span class="absolute inset-x-0 bottom-0 p-3 text-sm font-semibold text-white">
                                        {{ $album->title }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- ================================================ details --}}
            <aside class="lg:col-span-4">
                <div class="sticky top-24 space-y-6">
                    <div class="surface-card p-6">
                        <h2 class="font-bold text-heading">مشخصات رویداد</h2>

                        <dl class="mt-5 space-y-4 text-sm">
                            @foreach (array_filter([
                                ['fa-calendar-day', 'تاریخ شروع', shamsi($event->starts_at, 'full')],
                                $event->ends_at ? ['fa-calendar-check', 'تاریخ پایان', shamsi($event->ends_at, 'full')] : null,
                                ['fa-clock', 'ساعت شروع', shamsi($event->starts_at, 'time')],
                                ['fa-location-dot', 'محل برگزاری', $event->location],
                                $event->organizer ? ['fa-sitemap', 'برگزارکننده', $event->organizer] : null,
                                $event->age_groups ? ['fa-users', 'ردهٔ سنی', $event->age_groups] : null,
                                $event->capacity ? ['fa-user-group', 'ظرفیت', fa($event->capacity).' نفر'] : null,
                                ['fa-tag', 'هزینهٔ شرکت', $event->fee ? toman($event->fee) : 'رایگان'],
                                $event->registration_deadline ? ['fa-hourglass-end', 'مهلت ثبت‌نام', shamsi($event->registration_deadline)] : null,
                            ]) as [$icon, $label, $value])
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="flex shrink-0 items-center gap-2 text-muted">
                                        <i class="fa-solid {{ $icon }} w-4 text-center text-xs" aria-hidden="true"></i>
                                        {{ $label }}
                                    </dt>
                                    <dd class="text-end font-semibold text-heading">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>

                        @if ($event->is_registration_open)
                            <x-ui.button :href="route('register')" variant="primary" icon="fa-user-plus" class="mt-6 w-full">
                                ثبت‌نام
                            </x-ui.button>
                        @endif

                        <x-ui.button :href="route('contact')" variant="outline" size="sm"
                                     icon="fa-circle-question" class="mt-3 w-full">
                            سؤال دربارهٔ این رویداد
                        </x-ui.button>
                    </div>

                    @if ($related->isNotEmpty())
                        <div class="surface-card p-6">
                            <h2 class="font-bold text-heading">رویدادهای مشابه</h2>

                            <ul class="mt-4 space-y-4">
                                @foreach ($related as $item)
                                    <li>
                                        <a href="{{ route('events.show', $item) }}" class="group flex gap-3">
                                            <span class="flex h-14 w-12 shrink-0 flex-col items-center justify-center rounded-xl bg-surface-muted">
                                                <span class="text-lg leading-none font-extrabold text-heading">
                                                    {{ shamsi($item->starts_at, 'day') }}
                                                </span>
                                                <span class="mt-0.5 text-[0.6rem] text-muted">
                                                    {{ shamsi($item->starts_at, 'month') }}
                                                </span>
                                            </span>

                                            <span class="min-w-0">
                                                <span class="line-clamp-2 text-sm leading-snug font-semibold text-heading transition group-hover:text-brand-text">
                                                    {{ $item->title }}
                                                </span>
                                                <span class="mt-1 block text-xs text-muted">{{ $item->location }}</span>
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </div>

@endsection
