@extends('layouts.app')

@section('title', $coach->name.' — مربیان هیئت جودو کازرون')
@section('meta_description', $coach->summary)
@section('og_image', $coach->photo_url)

@section('content')

    <x-ui.page-header
        :eyebrow="$coach->title"
        :title="$coach->name"
        :description="$coach->summary"
        :breadcrumbs="[
            ['label' => 'مربیان', 'url' => route('coaches.index')],
            ['label' => $coach->name],
        ]" />

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
        <div class="grid gap-10 lg:grid-cols-12">

            {{-- ================================================= sidebar --}}
            <aside class="lg:col-span-4">
                <div class="surface-card sticky top-24 overflow-hidden">
                    <img src="{{ $coach->photo_url }}" alt="عکس {{ $coach->name }}"
                         class="aspect-4/5 w-full object-cover" loading="eager" decoding="async">

                    <div class="p-6">
                        <dl class="space-y-4 text-sm">
                            @foreach ([
                                ['fa-award', 'درجه', $coach->dan_label],
                                ['fa-clock-rotate-left', 'سابقهٔ مربیگری', fa($coach->experience_years).' سال'],
                                ['fa-user-tie', 'سمت', $coach->title],
                            ] as [$icon, $label, $value])
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="flex items-center gap-2 text-muted">
                                        <i class="fa-solid {{ $icon }} w-4 text-center text-xs" aria-hidden="true"></i>
                                        {{ $label }}
                                    </dt>
                                    <dd class="font-semibold text-heading">{{ $value }}</dd>
                                </div>
                            @endforeach

                            @if ($coach->belt)
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="flex items-center gap-2 text-muted">
                                        <i class="fa-solid fa-ranking-star w-4 text-center text-xs" aria-hidden="true"></i>
                                        کمربند
                                    </dt>
                                    <dd class="flex items-center gap-2 font-semibold text-heading">
                                        <span class="h-3 w-3 rounded-full ring-1 ring-line"
                                              style="background-color: {{ $coach->belt->color }}" aria-hidden="true"></span>
                                        {{ $coach->belt->name }}
                                    </dd>
                                </div>
                            @endif
                        </dl>

                        <div class="mt-6 space-y-2 border-t border-line pt-6">
                            @if ($coach->phone)
                                <a href="tel:{{ $coach->phone }}"
                                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-copy transition hover:bg-surface-muted">
                                    <i class="fa-solid fa-phone w-4 text-center text-xs text-muted" aria-hidden="true"></i>
                                    <span class="ltr">{{ fa($coach->phone) }}</span>
                                </a>
                            @endif

                            @if ($coach->email)
                                <a href="mailto:{{ $coach->email }}"
                                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-copy transition hover:bg-surface-muted">
                                    <i class="fa-solid fa-envelope w-4 text-center text-xs text-muted" aria-hidden="true"></i>
                                    <span class="ltr truncate">{{ $coach->email }}</span>
                                </a>
                            @endif

                            @if ($coach->instagram)
                                <a href="https://instagram.com/{{ $coach->instagram }}" target="_blank" rel="noopener noreferrer"
                                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-copy transition hover:bg-surface-muted">
                                    <x-icons.instagram class="h-4 w-4 shrink-0 text-muted" />
                                    <span class="ltr truncate">{{ $coach->instagram }}@</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </aside>

            {{-- ================================================== content --}}
            <div class="space-y-10 lg:col-span-8">
                <section class="reveal">
                    <h2 class="text-xl font-extrabold text-heading">بیوگرافی</h2>
                    <p class="mt-4 leading-loose text-copy">{{ $coach->bio }}</p>
                </section>

                @if (filled($coach->specialties))
                    <section class="reveal">
                        <h2 class="text-xl font-extrabold text-heading">تخصص فنی</h2>
                        <div class="mt-4 flex flex-wrap gap-2.5">
                            @foreach ($coach->specialties as $specialty)
                                <x-ui.badge variant="brand" icon="fa-hand-fist">{{ $specialty }}</x-ui.badge>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if (filled($coach->certificates))
                    <section class="reveal">
                        <h2 class="text-xl font-extrabold text-heading">مدارک و گواهینامه‌ها</h2>
                        <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                            @foreach ($coach->certificates as $certificate)
                                <li class="surface-card flex items-start gap-3 p-4">
                                    <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-accent-soft text-accent-text">
                                        <i class="fa-solid fa-certificate text-xs" aria-hidden="true"></i>
                                    </span>
                                    <span class="text-sm leading-relaxed text-copy">{{ $certificate }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($coach->trainingClasses->isNotEmpty())
                    <section class="reveal">
                        <h2 class="text-xl font-extrabold text-heading">کلاس‌های زیر نظر این مربی</h2>

                        <div class="mt-4 space-y-3">
                            @foreach ($coach->trainingClasses as $class)
                                <div class="surface-card flex flex-wrap items-center justify-between gap-4 p-4">
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-heading">{{ $class->title }}</h3>
                                        <p class="mt-1 text-xs text-muted">{{ $class->schedule_summary }}</p>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <x-ui.badge variant="neutral">{{ $class->age_group->label() }}</x-ui.badge>

                                        @if ($class->is_full)
                                            <x-ui.badge variant="brand">تکمیل</x-ui.badge>
                                        @else
                                            <x-ui.button size="sm" variant="soft"
                                                         :href="route('register', ['class' => $class->slug])">
                                                ثبت‌نام
                                            </x-ui.button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>

        @if ($others->isNotEmpty())
            <section class="mt-16 border-t border-line pt-14">
                <x-ui.section-heading eyebrow="کادر فنی" title="سایر مربیان هیئت" />

                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($others as $other)
                        <x-cards.coach :coach="$other" data-reveal-delay="{{ $loop->index * 90 }}" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>

@endsection
