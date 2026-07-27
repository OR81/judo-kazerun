@extends('layouts.app')

@section('title', ($nationalOnly ? 'ملی‌پوشان' : 'ورزشکاران و قهرمانان').' — هیئت جودو کازرون')
@section('meta_description', 'قهرمانان، ملی‌پوشان و مدال‌آوران جودوی شهرستان کازرون به همراه افتخارات و سوابق ورزشی.')

@section('content')

    <x-ui.page-header
        eyebrow="افتخارات"
        :title="$nationalOnly ? 'ملی‌پوشان کازرون' : 'ورزشکاران و قهرمانان'"
        :description="$nationalOnly
            ? 'جودوکاران شهرستان کازرون که در اردوها و مسابقات تیم ملی حضور داشته‌اند.'
            : 'مدال‌آوران و قهرمانان جودوی کازرون در میادین شهرستانی، استانی، کشوری و بین‌المللی.'"
        :breadcrumbs="$nationalOnly
            ? [['label' => 'ورزشکاران', 'url' => route('athletes.index')], ['label' => 'ملی‌پوشان']]
            : [['label' => 'ورزشکاران']]" />

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">

        {{-- ================================================ medal tally --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach ([
                ['طلا', $totals['gold'], 'from-amber-300 to-amber-600 text-amber-950'],
                ['نقره', $totals['silver'], 'from-slate-200 to-slate-400 text-slate-900'],
                ['برنز', $totals['bronze'], 'from-orange-300 to-orange-700 text-orange-950'],
            ] as [$label, $count, $tone])
                <div class="reveal surface-card p-6 text-center" data-reveal-delay="{{ $loop->index * 80 }}">
                    <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-gradient-to-br {{ $tone }}">
                        <i class="fa-solid fa-medal" aria-hidden="true"></i>
                    </span>
                    <p class="mt-3 text-3xl font-extrabold text-heading">
                        <span data-counter="{{ $count }}">{{ fa_number($count) }}</span>
                    </p>
                    <p class="mt-1 text-sm text-muted">مدال {{ $label }}</p>
                </div>
            @endforeach

            <div class="reveal surface-card p-6 text-center" data-reveal-delay="240">
                <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-brand-soft text-brand-text">
                    <i class="fa-solid fa-flag" aria-hidden="true"></i>
                </span>
                <p class="mt-3 text-3xl font-extrabold text-heading">
                    <span data-counter="{{ $nationalCount }}">{{ fa_number($nationalCount) }}</span>
                </p>
                <p class="mt-1 text-sm text-muted">ملی‌پوش</p>
            </div>
        </div>

        {{-- ===================================================== tabs --}}
        <div class="mt-10 flex flex-wrap items-center gap-2" role="group" aria-label="فیلتر ورزشکاران">
            <a href="{{ route('athletes.index') }}"
               @if (! $nationalOnly) aria-current="page" @endif
               class="rounded-xl border px-5 py-2.5 text-sm font-semibold transition
                      {{ ! $nationalOnly ? 'border-brand bg-brand text-on-brand' : 'border-line bg-surface text-copy hover:border-line-strong' }}">
                همهٔ ورزشکاران
            </a>

            <a href="{{ route('athletes.national') }}"
               @if ($nationalOnly) aria-current="page" @endif
               class="rounded-xl border px-5 py-2.5 text-sm font-semibold transition
                      {{ $nationalOnly ? 'border-brand bg-brand text-on-brand' : 'border-line bg-surface text-copy hover:border-line-strong' }}">
                ملی‌پوشان
                <span class="ms-1 text-xs opacity-80">({{ fa($nationalCount) }})</span>
            </a>
        </div>

        {{-- ================================================== athletes --}}
        @if ($athletes->isEmpty())
            <x-ui.empty-state class="mt-10" icon="fa-user-slash"
                              title="ورزشکاری ثبت نشده است"
                              description="فهرست ورزشکاران به‌زودی تکمیل می‌شود." />
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($athletes as $athlete)
                    <x-cards.athlete :athlete="$athlete" data-reveal-delay="{{ ($loop->index % 4) * 80 }}" />
                @endforeach
            </div>
        @endif

        {{-- ================================================ honour roll --}}
        <section class="mt-20">
            <x-ui.section-heading
                eyebrow="تابلوی افتخارات"
                title="آخرین مدال‌های کسب‌شده"
                description="نتایج جودوکاران کازرون در میادین رسمی." />

            <div class="mt-8 overflow-x-auto">
                <table class="w-full min-w-[42rem] border-collapse text-sm">
                    <caption class="sr-only">فهرست مدال‌های کسب‌شده توسط ورزشکاران هیئت جودو کازرون</caption>

                    <thead>
                        <tr class="border-b border-line text-start text-xs text-muted">
                            <th scope="col" class="px-4 py-3 text-start font-semibold">ورزشکار</th>
                            <th scope="col" class="px-4 py-3 text-start font-semibold">رویداد</th>
                            <th scope="col" class="px-4 py-3 text-start font-semibold">سطح</th>
                            <th scope="col" class="px-4 py-3 text-start font-semibold">سال</th>
                            <th scope="col" class="px-4 py-3 text-start font-semibold">مقام</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-line">
                        @foreach ($honourRoll as $achievement)
                            <tr class="transition hover:bg-surface-muted/60">
                                <td class="px-4 py-3">
                                    <a href="{{ route('athletes.show', $achievement->athlete) }}"
                                       class="flex items-center gap-3 font-semibold text-heading transition hover:text-brand-text">
                                        <x-ui.avatar :src="$achievement->athlete->photo_url"
                                                     :name="$achievement->athlete->name" size="sm" />
                                        {{ $achievement->athlete->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-copy">{{ $achievement->competition }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge variant="neutral">{{ $achievement->level->label() }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-muted">{{ fa($achievement->year) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="grid h-7 w-7 place-items-center rounded-full bg-gradient-to-br text-[0.65rem] font-extrabold {{ $achievement->rank->ringClass() }}">
                                            <i class="fa-solid fa-medal" aria-hidden="true"></i>
                                        </span>
                                        <span class="font-semibold text-heading">{{ $achievement->rank->placeLabel() }}</span>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

@endsection
