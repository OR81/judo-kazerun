@props(['athlete'])

@php $medals = $athlete->medal_counts; @endphp

<article {{ $attributes->merge(['class' => 'reveal group surface-card relative overflow-hidden text-center transition hover:shadow-lift']) }}>
    <div class="relative bg-gradient-to-b from-ink to-ink-soft pt-8 pb-6">
        @if ($athlete->is_national_team)
            <span class="absolute top-3 inset-inline-start-3">
                <x-ui.badge variant="accent" icon="fa-flag">ملی‌پوش</x-ui.badge>
            </span>
        @endif

        <x-ui.avatar :src="$athlete->photo_url" :name="$athlete->name" size="xl" ring class="mx-auto" />

        <h3 class="mt-4 text-lg font-bold text-white">
            <a href="{{ route('athletes.show', $athlete) }}" class="after:absolute after:inset-0">
                {{ $athlete->name }}
            </a>
        </h3>

        <p class="mt-1 text-sm text-on-ink">{{ $athlete->weight_class }}</p>
    </div>

    <div class="p-5">
        {{-- Medal tally --}}
        <ul class="flex items-center justify-center gap-3">
            @foreach ([
                ['gold', 'طلا', 'from-amber-300 to-amber-600 text-amber-950'],
                ['silver', 'نقره', 'from-slate-200 to-slate-400 text-slate-900'],
                ['bronze', 'برنز', 'from-orange-300 to-orange-700 text-orange-950'],
            ] as [$key, $label, $tone])
                <li class="flex flex-col items-center gap-1.5">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-gradient-to-br {{ $tone }} text-xs font-extrabold">
                        {{ fa($medals[$key]) }}
                    </span>
                    <span class="text-[0.7rem] text-muted">{{ $label }}</span>
                </li>
            @endforeach
        </ul>

        <div class="mt-5 flex items-center justify-between border-t border-line pt-4 text-xs text-muted">
            <span class="flex items-center gap-1.5">
                <span class="h-2.5 w-2.5 rounded-full ring-1 ring-line"
                      style="background-color: {{ $athlete->belt?->color ?? '#94a3b8' }}" aria-hidden="true"></span>
                {{ $athlete->belt?->name ?? 'بدون کمربند' }}
            </span>

            @if ($athlete->age)
                <span>{{ fa($athlete->age) }} سال</span>
            @endif
        </div>
    </div>
</article>
