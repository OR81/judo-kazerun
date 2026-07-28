@extends('layouts.app')

@section('title', $athlete->name.' — ورزشکاران هیئت جودو کازرون')
@section('meta_description', $athlete->bio)
@section('og_image', $athlete->photo_url)

@section('content')

    @php $medals = $athlete->medal_counts; @endphp

    <x-ui.page-header
        :eyebrow="$athlete->is_national_team ? 'ملی‌پوش' : 'ورزشکار هیئت'"
        :title="$athlete->name"
        :description="$athlete->bio"
        :breadcrumbs="[
            ['label' => 'ورزشکاران', 'url' => route('athletes.index')],
            ['label' => $athlete->name],
        ]" />

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
        <div class="grid gap-10 lg:grid-cols-12">

            <aside class="lg:col-span-4">
                <div class="surface-card sticky top-24 overflow-hidden">
                    <div class="bg-gradient-to-b from-ink to-ink-soft px-6 pt-8 pb-6 text-center">
                        <x-ui.avatar :src="$athlete->photo_url" :name="$athlete->name" size="xl" ring class="mx-auto" />

                        <h2 class="mt-4 text-xl font-bold text-white">{{ $athlete->name }}</h2>
                        <p class="mt-1 text-sm text-on-ink">{{ $athlete->weight_class }}</p>

                        @if ($athlete->is_national_team)
                            <p class="mt-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-accent px-3 py-1 text-xs font-bold text-on-accent">
                                    <i class="fa-solid fa-flag text-[0.7rem]" aria-hidden="true"></i>
                                    عضو تیم ملی
                                </span>
                            </p>
                        @endif
                    </div>

                    <div class="p-6">
                        <ul class="flex items-center justify-center gap-4">
                            @foreach ([
                                ['gold', 'طلا', 'from-amber-300 to-amber-600 text-amber-950'],
                                ['silver', 'نقره', 'from-slate-200 to-slate-400 text-slate-900'],
                                ['bronze', 'برنز', 'from-orange-300 to-orange-700 text-orange-950'],
                            ] as [$key, $label, $tone])
                                <li class="flex flex-col items-center gap-2">
                                    <span class="grid h-11 w-11 place-items-center rounded-full bg-gradient-to-br {{ $tone }} text-sm font-extrabold">
                                        {{ fa($medals[$key]) }}
                                    </span>
                                    <span class="text-xs text-muted">{{ $label }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <dl class="mt-6 space-y-4 border-t border-line pt-6 text-sm">
                            @if ($athlete->belt)
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-muted">کمربند</dt>
                                    <dd class="flex items-center gap-2 font-semibold text-heading">
                                        <span class="h-3 w-3 rounded-full ring-1 ring-line"
                                              style="background-color: {{ $athlete->belt->color }}" aria-hidden="true"></span>
                                        {{ $athlete->belt->name }}
                                    </dd>
                                </div>
                            @endif

                            @if ($athlete->age)
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-muted">سن</dt>
                                    <dd class="font-semibold text-heading">{{ fa($athlete->age) }} سال</dd>
                                </div>
                            @endif

                            @foreach (array_filter([
                                'باشگاه' => $athlete->club,
                                'شهر' => $athlete->city,
                                'مربی' => $athlete->coach?->name,
                            ]) as $label => $value)
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-muted">{{ $label }}</dt>
                                    <dd class="font-semibold text-heading">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>

                        @if ($athlete->coach)
                            <x-ui.button :href="route('coaches.show', $athlete->coach)" variant="outline"
                                         size="sm" icon="fa-chalkboard-user" class="mt-6 w-full">
                                پروفایل مربی
                            </x-ui.button>
                        @endif
                    </div>
                </div>
            </aside>

            {{-- ================================================ timeline --}}
            <div class="lg:col-span-8">
                <h2 class="text-xl font-extrabold text-heading">افتخارات و سوابق</h2>

                @if ($athlete->achievements->isEmpty())
                    <x-ui.empty-state class="mt-6" icon="fa-medal"
                                      title="هنوز افتخاری ثبت نشده"
                                      description="نتایج این ورزشکار پس از شرکت در مسابقات رسمی در همین بخش نمایش داده می‌شود." />
                @else
                    <ol class="mt-6 space-y-4">
                        @foreach ($athlete->achievements as $achievement)
                            <li class="reveal surface-card flex gap-4 p-5" data-reveal-delay="{{ $loop->index * 70 }}">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-gradient-to-br {{ $achievement->rank->ringClass() }}">
                                    <i class="fa-solid fa-medal" aria-hidden="true"></i>
                                </span>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-bold text-heading">{{ $achievement->rank->placeLabel() }}</h3>
                                        <x-ui.badge variant="neutral">{{ $achievement->level->label() }}</x-ui.badge>
                                    </div>

                                    <p class="mt-1.5 text-sm text-copy">{{ $achievement->competition }}</p>

                                    @if ($achievement->description)
                                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $achievement->description }}</p>
                                    @endif
                                </div>

                                <span class="shrink-0 self-start rounded-lg bg-surface-muted px-3 py-1 text-xs font-semibold text-muted">
                                    {{ fa($achievement->year) }}
                                </span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>

        @if ($others->isNotEmpty())
            <section class="mt-16 border-t border-line pt-14">
                <x-ui.section-heading eyebrow="ورزشکاران" title="سایر جودوکاران هیئت" />

                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($others as $other)
                        <x-cards.athlete :athlete="$other" data-reveal-delay="{{ $loop->index * 80 }}" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>

@endsection
