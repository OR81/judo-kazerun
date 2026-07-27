@extends('layouts.app')

@section('title', setting('site_title').' — '.setting('site_tagline'))

@section('content')

    {{-- ===================================================== hero slider --}}
    <section data-slider data-slider-interval="7000"
             class="relative overflow-hidden bg-gray-950"
             aria-roledescription="carousel" aria-label="معرفی هیئت جودو کازرون">

        <div class="relative h-[32rem] sm:h-[36rem] lg:h-[42rem]">
            {{-- RTL: slides stack to the right, so the track shifts by +100% per slide. --}}
            <div data-slider-track
                 class="flex h-full transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]">
                @foreach ($slides as $slide)
                    <div data-slide class="relative h-full w-full shrink-0"
                         role="group" aria-roledescription="اسلاید"
                         aria-label="{{ fa($loop->iteration) }} از {{ fa($slides->count()) }}"
                         @if (! $loop->first) aria-hidden="true" @endif>

                        <img src="{{ $slide->image_url }}" alt=""
                             @class(['h-full w-full object-cover'])
                             @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif
                             decoding="async">

                        {{-- Two-stop scrim: readable text without hiding the photo. --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/70 to-gray-950/25"
                             aria-hidden="true"></div>
                        <div class="absolute inset-0 bg-gradient-to-l from-gray-950/80 to-transparent"
                             aria-hidden="true"></div>

                        <div class="absolute inset-0 flex items-center">
                            <div class="mx-auto w-full max-w-7xl px-4 pb-16 sm:px-6">
                                <div class="max-w-2xl">
                                    @if ($slide->eyebrow)
                                        <p class="flex items-center gap-2 text-sm font-bold text-accent">
                                            <span class="h-px w-8 bg-accent" aria-hidden="true"></span>
                                            {{ $slide->eyebrow }}
                                        </p>
                                    @endif

                                    <h2 class="mt-4 text-3xl leading-tight font-extrabold text-balance text-white sm:text-4xl lg:text-6xl">
                                        {{ $slide->title }}
                                    </h2>

                                    @if ($slide->subtitle)
                                        <p class="mt-4 text-lg font-medium text-gray-200 sm:text-xl">{{ $slide->subtitle }}</p>
                                    @endif

                                    @if ($slide->description)
                                        <p class="mt-3 max-w-xl leading-relaxed text-gray-400">{{ $slide->description }}</p>
                                    @endif

                                    <div class="mt-8 flex flex-wrap items-center gap-3">
                                        @if ($slide->cta_label)
                                            <x-ui.button :href="$slide->cta_url" variant="primary" size="lg" icon-end="fa-arrow-left">
                                                {{ $slide->cta_label }}
                                            </x-ui.button>
                                        @endif

                                        @if ($slide->secondary_cta_label)
                                            <a href="{{ $slide->secondary_cta_url }}"
                                               class="inline-flex items-center gap-2.5 rounded-xl border border-white/25 bg-white/5 px-7 py-3.5
                                                      text-base font-bold text-white backdrop-blur-sm transition hover:bg-white/15">
                                                {{ $slide->secondary_cta_label }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Controls --}}
            <div class="absolute inset-x-0 bottom-6 z-10">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 sm:px-6">
                    <div class="flex items-center gap-2" role="group" aria-label="انتخاب اسلاید">
                        @foreach ($slides as $slide)
                            <button type="button" data-slide-dot
                                    class="h-2.5 rounded-full transition-all duration-300 {{ $loop->first ? 'w-8 bg-accent' : 'w-2.5 bg-white/45' }}"
                                    aria-label="رفتن به اسلاید {{ fa($loop->iteration) }}"
                                    aria-current="{{ $loop->first ? 'true' : 'false' }}"></button>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" data-slide-prev
                                class="grid h-11 w-11 place-items-center rounded-full border border-white/20 bg-white/10 text-white backdrop-blur-sm transition hover:bg-white/20"
                                aria-label="اسلاید قبلی">
                            <i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                        </button>
                        <button type="button" data-slide-next
                                class="grid h-11 w-11 place-items-center rounded-full border border-white/20 bg-white/10 text-white backdrop-blur-sm transition hover:bg-white/20"
                                aria-label="اسلاید بعدی">
                            <i class="fa-solid fa-chevron-left text-sm" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <p data-slider-status class="sr-only" role="status" aria-live="polite"></p>
    </section>

    {{-- ========================================================== stats --}}
    <section class="relative z-10 -mt-14" aria-label="آمار هیئت">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach ($stats as $stat)
                    <x-ui.stat :value="$stat['value']" :label="$stat['label']" :icon="$stat['icon']"
                               :animate="false"
                               data-reveal-delay="{{ $loop->index * 90 }}" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- =================================================== today's training --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-ui.section-heading
                eyebrow="امروز روی تاتامی"
                :title="'برنامهٔ تمرین '.$todayLabel"
                description="کلاس‌هایی که امروز در خانهٔ جودو و مجموعهٔ تختی برگزار می‌شوند." />

            <x-ui.button :href="route('schedule')" variant="outline" icon-end="fa-arrow-left" class="reveal">
                برنامهٔ کامل هفته
            </x-ui.button>
        </div>

        @if ($todaySessions->isEmpty())
            <x-ui.empty-state class="mt-10" icon="fa-mug-hot"
                              title="امروز تمرینی برگزار نمی‌شود"
                              description="روزهای تمرین را در برنامهٔ هفتگی ببینید و کلاس مناسب خود را انتخاب کنید.">
                <x-ui.button :href="route('schedule')" variant="primary">مشاهدهٔ برنامهٔ هفتگی</x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="mt-10 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($todaySessions as $row)
                    @php $class = $row->trainingClass; @endphp

                    <article class="reveal surface-card flex items-center gap-4 p-4"
                             data-reveal-delay="{{ $loop->index * 70 }}">
                        <div class="flex h-16 w-20 shrink-0 flex-col items-center justify-center rounded-xl bg-brand-soft">
                            <span class="text-sm font-extrabold text-brand-text">{{ shamsi($row->start_time, 'time') }}</span>
                            <span class="mt-0.5 text-[0.65rem] text-muted">تا {{ shamsi($row->end_time, 'time') }}</span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="truncate font-bold text-heading">{{ $class->title }}</h3>
                            <p class="mt-1 flex items-center gap-2 text-xs text-muted">
                                <i class="fa-solid fa-user-tie text-[0.7rem]" aria-hidden="true"></i>
                                {{ $class->coach?->name ?? 'کادر فنی هیئت' }}
                            </p>
                            <p class="mt-1 flex items-center gap-2 text-xs text-muted">
                                <i class="fa-solid fa-location-dot text-[0.7rem]" aria-hidden="true"></i>
                                {{ $row->venue ?? $class->venue }}
                            </p>
                        </div>

                        <x-ui.badge variant="neutral">{{ $class->age_group->label() }}</x-ui.badge>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ========================================================== news --}}
    <section class="bg-surface-muted/60 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <x-ui.section-heading
                    eyebrow="تازه‌ها"
                    title="آخرین اخبار هیئت"
                    description="گزارش مسابقات، اطلاعیه‌های ثبت‌نام و مطالب آموزشی." />

                <x-ui.button :href="route('news.index')" variant="outline" icon-end="fa-arrow-left" class="reveal">
                    آرشیو اخبار
                </x-ui.button>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-12">
                @if ($featuredNews)
                    <div class="lg:col-span-7">
                        <x-cards.news :item="$featuredNews" :featured="true" class="h-full" />
                    </div>
                @endif

                <div class="grid gap-6 sm:grid-cols-2 {{ $featuredNews ? 'lg:col-span-5 lg:grid-cols-1' : 'lg:col-span-12 lg:grid-cols-4' }}">
                    @foreach ($latestNews->take($featuredNews ? 3 : 4) as $item)
                        <x-cards.news :item="$item" data-reveal-delay="{{ $loop->index * 80 }}" />
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ======================================================== events --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <x-ui.section-heading
                    eyebrow="تقویم پیش‌رو"
                    title="مسابقات و رویدادهای آینده"
                    description="مسابقات قهرمانی، آزمون‌های دان و اردوهای آماده‌سازی هیئت." />

                <div class="reveal mt-8 space-y-3">
                    <x-ui.button :href="route('events.index')" variant="dark" icon="fa-calendar-days" class="w-full sm:w-auto">
                        تقویم کامل رویدادها
                    </x-ui.button>
                </div>
            </div>

            <div class="space-y-4 lg:col-span-8">
                @forelse ($upcomingEvents as $event)
                    <x-cards.event :event="$event" data-reveal-delay="{{ $loop->index * 70 }}" />
                @empty
                    <x-ui.empty-state icon="fa-calendar-xmark"
                                      title="رویداد پیش‌رویی ثبت نشده"
                                      description="به‌زودی برنامهٔ مسابقات و آزمون‌های جدید اعلام می‌شود." />
                @endforelse
            </div>
        </div>
    </section>

    {{-- ======================================================= coaches --}}
    <section class="bg-surface-muted/60 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <x-ui.section-heading
                    eyebrow="کادر فنی"
                    title="مربیان هیئت جودو کازرون"
                    description="مربیان رسمی فدراسیون با سال‌ها تجربهٔ آموزش و قهرمان‌پروری." />

                <x-ui.button :href="route('coaches.index')" variant="outline" icon-end="fa-arrow-left" class="reveal">
                    همهٔ مربیان
                </x-ui.button>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($coaches as $coach)
                    <x-cards.coach :coach="$coach" data-reveal-delay="{{ $loop->index * 80 }}" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================================================== champions --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-ui.section-heading
                eyebrow="افتخارات"
                title="قهرمانان کازرون"
                description="ورزشکارانی که نام شهرستان را بر سکوهای استانی، کشوری و بین‌المللی نشانده‌اند." />

            <x-ui.button :href="route('athletes.index')" variant="outline" icon-end="fa-arrow-left" class="reveal">
                همهٔ ورزشکاران
            </x-ui.button>
        </div>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($champions as $athlete)
                <x-cards.athlete :athlete="$athlete" data-reveal-delay="{{ $loop->index * 80 }}" />
            @endforeach
        </div>
    </section>

    {{-- =========================================================== CTA --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6">
        <div class="reveal relative overflow-hidden rounded-panel bg-gray-900 px-6 py-14 sm:px-12 lg:py-20">
            <div class="pointer-events-none absolute inset-0 opacity-[0.06]" aria-hidden="true"
                 style="background-image:linear-gradient(#fff 1px,transparent 1px),linear-gradient(90deg,#fff 1px,transparent 1px);background-size:48px 48px"></div>
            <div class="pointer-events-none absolute -top-20 inset-inline-end-10 h-64 w-64 rounded-full bg-brand/30 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-24 inset-inline-start-10 h-64 w-64 rounded-full bg-accent/20 blur-3xl" aria-hidden="true"></div>

            <div class="relative grid items-center gap-8 lg:grid-cols-12">
                <div class="lg:col-span-8">
                    <p class="flex items-center gap-2 text-sm font-bold text-accent">
                        <span class="h-px w-8 bg-accent" aria-hidden="true"></span>
                        ثبت‌نام دورهٔ جدید
                    </p>

                    <h2 class="mt-4 text-2xl font-extrabold text-balance text-white sm:text-3xl lg:text-4xl">
                        همین امروز راه پهلوانی را آغاز کنید
                    </h2>

                    <p class="mt-4 max-w-2xl leading-relaxed text-gray-300">
                        ثبت‌نام آنلاین در کمتر از پنج دقیقه: کلاس مناسب را انتخاب کنید، مدارک را بارگذاری کنید
                        و شهریه را اینترنتی بپردازید. ظرفیت کلاس‌ها محدود است.
                    </p>

                    <ul class="mt-6 flex flex-wrap gap-x-6 gap-y-3 text-sm text-gray-300">
                        @foreach ([
                            'بیمهٔ ورزشی',
                            'مربیان رسمی فدراسیون',
                            'تاتامی استاندارد',
                            'سالن مجزای بانوان',
                        ] as $label)
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-accent" aria-hidden="true"></i>
                                {{ $label }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="flex flex-col gap-3 lg:col-span-4">
                    <x-ui.button :href="route('register')" variant="primary" size="lg" icon="fa-user-plus" class="w-full">
                        شروع ثبت‌نام آنلاین
                    </x-ui.button>

                    <x-ui.button :href="route('schedule')" variant="accent" size="lg" icon="fa-calendar-days" class="w-full">
                        مشاهدهٔ کلاس‌ها
                    </x-ui.button>

                    <a href="tel:{{ setting('phone') }}"
                       class="mt-1 flex items-center justify-center gap-2 text-sm text-gray-400 transition hover:text-accent">
                        <i class="fa-solid fa-phone text-xs" aria-hidden="true"></i>
                        مشاورهٔ تلفنی: <span class="ltr">{{ fa(setting('phone')) }}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================================================= gallery --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-ui.section-heading
                eyebrow="گالری"
                title="لحظه‌های ماندگار"
                description="تصاویر مسابقات، اردوها و تمرینات روزانهٔ جودوکاران کازرون." />

            <x-ui.button :href="route('gallery')" variant="outline" icon-end="fa-arrow-left" class="reveal">
                گالری کامل
            </x-ui.button>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($albums as $album)
                <a href="{{ route('gallery.show', $album) }}"
                   class="reveal group relative aspect-4/3 overflow-hidden rounded-panel"
                   data-reveal-delay="{{ $loop->index * 80 }}">
                    <img src="{{ $album->cover_url }}" alt="" loading="lazy" decoding="async"
                         class="h-full w-full object-cover transition duration-500 group-hover:scale-105">

                    <div class="absolute inset-0 bg-gradient-to-t from-gray-950/85 to-transparent" aria-hidden="true"></div>

                    <div class="absolute inset-x-0 bottom-0 p-5">
                        <x-ui.badge variant="dark" :icon="$album->type->icon()">
                            {{ fa($album->items->count()) }} {{ $album->type->label() }}
                        </x-ui.badge>
                        <h3 class="mt-3 font-bold text-white">{{ $album->title }}</h3>
                        <p class="mt-1 text-xs text-gray-300">{{ shamsi($album->taken_on) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ====================================================== sponsors --}}
    <section class="border-t border-line py-14" aria-label="حامیان هیئت">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <p class="reveal text-center text-sm font-semibold text-muted">با حمایت و همکاری</p>

            <ul class="mt-8 flex flex-wrap items-center justify-center gap-x-10 gap-y-8">
                @foreach ($sponsors as $sponsor)
                    <li class="reveal" data-reveal-delay="{{ $loop->index * 60 }}">
                        @if ($sponsor->url)
                            <a href="{{ $sponsor->url }}" target="_blank" rel="noopener noreferrer"
                               class="flex flex-col items-center gap-2 opacity-70 grayscale transition hover:opacity-100 hover:grayscale-0">
                                <img src="{{ $sponsor->logo_url }}" alt="{{ $sponsor->name }}"
                                     loading="lazy" class="h-14 w-14 rounded-xl object-contain">
                                <span class="max-w-32 text-center text-xs text-muted">{{ $sponsor->name }}</span>
                            </a>
                        @else
                            <div class="flex flex-col items-center gap-2 opacity-70 grayscale transition hover:opacity-100 hover:grayscale-0">
                                <img src="{{ $sponsor->logo_url }}" alt="{{ $sponsor->name }}"
                                     loading="lazy" class="h-14 w-14 rounded-xl object-contain">
                                <span class="max-w-32 text-center text-xs text-muted">{{ $sponsor->name }}</span>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

@endsection
