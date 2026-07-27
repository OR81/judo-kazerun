@props(['class' => null, 'trainingClass'])

@php
    $item = $trainingClass;
    $full = $item->is_full;
@endphp

<article data-filter-item
         data-day="{{ $item->sessions->pluck('day_of_week')->unique()->implode(' ') }}"
         data-age="{{ $item->age_group->value }}"
         data-gender="{{ $item->gender->value }}"
         {{ $attributes->merge(['class' => 'reveal surface-card flex flex-col p-5 transition hover:shadow-lift']) }}>

    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.badge variant="brand" :icon="$item->age_group->icon()">
                    {{ $item->age_group->label() }}
                </x-ui.badge>
                <x-ui.badge variant="neutral">{{ $item->gender->label() }}</x-ui.badge>
                <x-ui.badge variant="neutral">{{ $item->level->label() }}</x-ui.badge>
            </div>

            <h3 class="mt-3 text-lg font-bold text-heading">{{ $item->title }}</h3>
            <p class="mt-1 text-xs text-muted">{{ $item->age_group->ageRange() }}</p>
        </div>

        @if ($full)
            <x-ui.badge variant="brand" icon="fa-circle-xmark">تکمیل</x-ui.badge>
        @endif
    </div>

    <p class="mt-4 line-clamp-2 flex-1 text-sm leading-relaxed text-muted">{{ $item->description }}</p>

    {{-- Weekly sessions --}}
    <ul class="mt-4 space-y-2">
        @foreach ($item->sessions as $session)
            <li class="flex items-center justify-between gap-3 rounded-xl bg-surface-muted px-3 py-2 text-sm">
                <span class="font-semibold text-heading">{{ $session->day_name }}</span>
                <span class="text-muted">{{ $session->time_range }}</span>
            </li>
        @endforeach
    </ul>

    {{-- Coach --}}
    @if ($item->coach)
        <a href="{{ route('coaches.show', $item->coach) }}"
           class="mt-4 flex items-center gap-3 rounded-xl border border-line p-3 transition hover:bg-surface-muted">
            <x-ui.avatar :src="$item->coach->photo_url" :name="$item->coach->name" size="sm" />
            <span class="min-w-0">
                <span class="block text-sm font-semibold text-heading">{{ $item->coach->name }}</span>
                <span class="block text-xs text-muted">{{ $item->coach->dan_label }} · {{ $item->coach->title }}</span>
            </span>
        </a>
    @endif

    {{-- Capacity --}}
    <div class="mt-4">
        <div class="flex items-center justify-between text-xs">
            <span class="text-muted">ظرفیت کلاس</span>
            <span class="font-semibold text-heading">
                {{ fa($item->enrolled_count) }} از {{ fa($item->capacity) }}
                @unless ($full)
                    <span class="text-muted">({{ fa($item->remaining_seats) }} جای خالی)</span>
                @endunless
            </span>
        </div>

        <div class="mt-2 h-2 overflow-hidden rounded-full bg-surface-muted"
             role="progressbar"
             aria-valuenow="{{ $item->enrolled_count }}"
             aria-valuemin="0"
             aria-valuemax="{{ $item->capacity }}"
             aria-label="ظرفیت تکمیل‌شدهٔ {{ $item->title }}">
            <div class="h-full rounded-full {{ $item->capacity_tone }} transition-[width] duration-700"
                 style="width: {{ $item->occupancy_percent }}%"></div>
        </div>
    </div>

    <div class="mt-5 flex items-center justify-between gap-3 border-t border-line pt-4">
        <span class="text-sm">
            @if ($item->monthly_fee > 0)
                <span class="font-extrabold text-heading">{{ fa_number($item->monthly_fee) }}</span>
                <span class="text-xs text-muted">تومان / ماه</span>
            @else
                <span class="font-bold text-emerald-600">بدون شهریه</span>
            @endif
        </span>

        @if ($full)
            <x-ui.button variant="outline" size="sm" disabled>ظرفیت تکمیل است</x-ui.button>
        @else
            <x-ui.button variant="primary" size="sm" icon="fa-user-plus"
                         :href="route('register', ['class' => $item->slug])">
                ثبت‌نام
            </x-ui.button>
        @endif
    </div>
</article>
