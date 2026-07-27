@extends('layouts.portal')

@section('title', 'پرتال مربی — هیئت جودو کازرون')
@section('subtitle', 'پرتال مربی — کلاس‌ها و هنرجویان شما')

@section('portal-actions')
    @if ($coach)
        <x-ui.button :href="route('coaches.show', $coach)" variant="accent" size="md" icon="fa-id-badge">
            پروفایل عمومی
        </x-ui.button>
    @endif
@endsection

@section('portal')

    {{-- ==================================================== summary --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ([
            ['fa-list-check', 'کلاس فعال', $classes->count()],
            ['fa-users', 'هنرجو', $studentCount],
            ['fa-hourglass-half', 'در انتظار تأیید', $pending->count()],
            ['fa-award', 'درجه', $coach?->dan_rank ? 'دان '.fa($coach->dan_rank) : '—'],
        ] as [$icon, $label, $value])
            <div class="surface-card p-5 text-center">
                <span class="mx-auto grid h-11 w-11 place-items-center rounded-xl bg-brand-soft text-brand-text">
                    <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                </span>
                <p class="mt-3 text-2xl font-extrabold text-heading">
                    {{ is_numeric($value) ? fa_number($value) : $value }}
                </p>
                <p class="mt-1 text-xs text-muted">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-8 lg:grid-cols-12">

        {{-- ================================================= classes --}}
        <div class="space-y-8 lg:col-span-8">
            <section>
                <h2 class="text-lg font-extrabold text-heading">کلاس‌های تحت نظر شما</h2>

                @if ($classes->isEmpty())
                    <x-ui.empty-state class="mt-4" icon="fa-chalkboard"
                                      title="کلاسی به شما اختصاص نیافته"
                                      description="برای اختصاص کلاس، با دبیرخانهٔ هیئت هماهنگ کنید.">
                        <x-ui.button :href="route('contact')" variant="outline" icon="fa-headset">تماس با دبیرخانه</x-ui.button>
                    </x-ui.empty-state>
                @else
                    <div class="mt-4 space-y-4">
                        @foreach ($classes as $class)
                            <div class="surface-card p-6">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <h3 class="text-lg font-bold text-heading">{{ $class->title }}</h3>

                                        <div class="mt-2 flex flex-wrap items-center gap-2">
                                            <x-ui.badge variant="neutral">{{ $class->age_group->label() }}</x-ui.badge>
                                            <x-ui.badge variant="neutral">{{ $class->gender->label() }}</x-ui.badge>
                                            <x-ui.badge variant="neutral">{{ $class->level->label() }}</x-ui.badge>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <p class="text-2xl font-extrabold text-heading">
                                            {{ fa($class->enrollments->count()) }}
                                            <span class="text-sm font-normal text-muted">/ {{ fa($class->capacity) }}</span>
                                        </p>
                                        <p class="mt-0.5 text-xs text-muted">هنرجو</p>
                                    </div>
                                </div>

                                <div class="mt-4 h-2 overflow-hidden rounded-full bg-surface-muted"
                                     role="progressbar"
                                     aria-valuenow="{{ $class->enrolled_count }}"
                                     aria-valuemin="0" aria-valuemax="{{ $class->capacity }}"
                                     aria-label="ظرفیت {{ $class->title }}">
                                    <div class="h-full rounded-full {{ $class->capacity_tone }}"
                                         style="width: {{ $class->occupancy_percent }}%"></div>
                                </div>

                                <ul class="mt-4 grid gap-2 sm:grid-cols-3">
                                    @foreach ($class->sessions as $session)
                                        <li class="rounded-xl bg-surface-muted px-3 py-2 text-center text-xs">
                                            <span class="block font-semibold text-heading">{{ $session->day_name }}</span>
                                            <span class="mt-0.5 block text-muted">{{ $session->time_range }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                @if ($class->enrollments->isNotEmpty())
                                    <details class="mt-4 border-t border-line pt-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-brand-text">
                                            مشاهدهٔ فهرست هنرجویان ({{ fa($class->enrollments->count()) }} نفر)
                                        </summary>

                                        <ul class="mt-3 divide-y divide-line">
                                            @foreach ($class->enrollments as $enrollment)
                                                <li class="flex flex-wrap items-center justify-between gap-3 py-2.5 text-sm">
                                                    <span class="font-medium text-heading">{{ $enrollment->full_name }}</span>
                                                    <span class="flex items-center gap-3">
                                                        <a href="tel:{{ $enrollment->mobile }}"
                                                           class="ltr text-xs text-muted transition hover:text-brand-text">
                                                            {{ fa($enrollment->mobile) }}
                                                        </a>
                                                        <x-ui.badge :variant="$enrollment->status->badgeClass()">
                                                            {{ $enrollment->status->label() }}
                                                        </x-ui.badge>
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </details>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- Pending approvals --}}
            @if ($pending->isNotEmpty())
                <section>
                    <h2 class="text-lg font-extrabold text-heading">ثبت‌نام‌های در انتظار بررسی</h2>
                    <p class="mt-1.5 text-sm text-muted">
                        تأیید نهایی توسط دبیرخانهٔ هیئت انجام می‌شود؛ این فهرست جهت اطلاع شماست.
                    </p>

                    <div class="mt-4 space-y-3">
                        @foreach ($pending as $enrollment)
                            <div class="surface-card flex flex-wrap items-center justify-between gap-4 p-4">
                                <div>
                                    <p class="font-semibold text-heading">{{ $enrollment->full_name }}</p>
                                    <p class="mt-1 text-xs text-muted">
                                        {{ $enrollment->trainingClass->title }} · {{ shamsi($enrollment->created_at) }}
                                    </p>
                                </div>

                                <x-ui.badge :variant="$enrollment->status->badgeClass()">
                                    {{ $enrollment->status->label() }}
                                </x-ui.badge>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- ================================================ sidebar --}}
        <aside class="space-y-6 lg:col-span-4">
            @if ($coach)
                <div class="surface-card overflow-hidden">
                    <img src="{{ $coach->photo_url }}" alt="" class="aspect-4/3 w-full object-cover">

                    <div class="p-6">
                        <h2 class="font-bold text-heading">{{ $coach->name }}</h2>
                        <p class="mt-1 text-sm text-muted">{{ $coach->title }}</p>

                        <dl class="mt-4 space-y-3 border-t border-line pt-4 text-sm">
                            @foreach (array_filter([
                                'درجه' => $coach->dan_label,
                                'سابقه' => fa($coach->experience_years).' سال',
                                'کمربند' => $coach->belt?->name,
                            ]) as $label => $value)
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-muted">{{ $label }}</dt>
                                    <dd class="font-semibold text-heading">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
            @endif

            <div class="surface-card p-6">
                <h2 class="font-bold text-heading">دسترسی سریع</h2>

                <div class="mt-4 space-y-2">
                    @foreach ([
                        ['fa-calendar-days', 'برنامهٔ تمرینی هفتگی', route('schedule')],
                        ['fa-file-arrow-down', 'فرم‌ها و آیین‌نامه‌ها', route('downloads')],
                        ['fa-trophy', 'تقویم مسابقات', route('events.index')],
                        ['fa-headset', 'تماس با دبیرخانه', route('contact')],
                    ] as [$icon, $label, $url])
                        <a href="{{ $url }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-copy transition hover:bg-surface-muted">
                            <i class="fa-solid {{ $icon }} w-4 text-center text-xs text-muted" aria-hidden="true"></i>
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>

@endsection
