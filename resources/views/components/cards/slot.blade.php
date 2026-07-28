{{--
    یک سانس روی تابلوی سالن.

    The prop is `hallSlot`, not `slot`: Blade already binds `$slot` inside every
    component to the default content slot, and a prop of that name is silently
    overwritten by it.
--}}
@props(['hallSlot', 'showVenue' => true])

@php
    $item = $hallSlot;
    $status = $item->status;

    // A booking request is a message to the office, not a self-service reservation —
    // the hall is let by short contract, so the form is prefilled and a human answers.
    $requestUrl = route('contact', [
        'subject' => 'درخواست اجارهٔ سانس — '.$item->day_name.' '.$item->time_range
            .' — '.($item->venue?->name ?? 'خانهٔ جودو'),
    ]);
@endphp

<article data-hall-slot
         data-venue="{{ $item->venue_id }}"
         data-status="{{ $status->value }}"
         {{ $attributes->merge([
             'class' => 'flex overflow-hidden rounded-card border transition '.$status->cardClass(),
         ]) }}>

    {{-- The status rail: colour, always paired with the icon and label beside it. --}}
    <span class="w-1.5 shrink-0 {{ $status->railClass() }}" aria-hidden="true"></span>

    <div class="flex min-w-0 flex-1 flex-col gap-3 p-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="flex items-center gap-2 font-bold text-heading">
                <span class="tabular-nums">{{ $item->time_range }}</span>

                @if ($item->is_running_now)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-open-soft px-2 py-0.5
                                 text-[0.65rem] font-bold text-open-text">
                        <span class="h-1.5 w-1.5 rounded-full bg-open" aria-hidden="true"></span>
                        هم‌اکنون
                    </span>
                @endif
            </p>

            <x-ui.badge :variant="$status->badgeClass()" :icon="$status->icon()">
                {{ $status->shortLabel() }}
            </x-ui.badge>
        </div>

        <div class="min-w-0">
            <h4 class="truncate font-semibold text-heading">{{ $item->occupant_label }}</h4>

            <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted">
                @if ($showVenue && $item->venue)
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-[0.7rem]" aria-hidden="true"></i>
                        {{ $item->venue->name }}
                    </span>
                @endif

                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-hourglass-half text-[0.7rem]" aria-hidden="true"></i>
                    {{ $item->duration_label }}
                </span>

                {{-- «مختلط» is the default and adds nothing; a restriction is the news. --}}
                @if ($item->gender !== App\Enums\Gender::Mixed)
                    <span class="flex items-center gap-1.5 font-semibold text-accent-text">
                        <i class="fa-solid fa-user-check text-[0.7rem]" aria-hidden="true"></i>
                        ویژهٔ {{ $item->gender->label() }}
                    </span>
                @endif
            </p>

            @if ($item->note)
                <p class="mt-1.5 text-xs text-muted">{{ $item->note }}</p>
            @endif
        </div>

        {{-- Rented and closed slots get no footer: the badge above already says so,
             and repeating it would give an unavailable slot the same weight as a
             free one. Only actionable slots carry an action. --}}
        @if ($status->isBookable() || $item->trainingClass)
            <div class="mt-auto flex flex-wrap items-center justify-between gap-3 border-t border-line/70 pt-3">
                @if ($status->isBookable())
                    <span class="text-sm">
                        <span class="font-extrabold text-heading">{{ fa_number($item->effective_price) }}</span>
                        <span class="text-xs text-muted">تومان / سانس</span>
                    </span>

                    <a href="{{ $requestUrl }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-brand px-3.5 py-2 text-xs font-bold
                              text-on-brand transition hover:bg-brand-hover">
                        درخواست رزرو
                        <i class="fa-solid fa-arrow-left text-[0.65rem]" aria-hidden="true"></i>
                    </a>
                @else
                    <span class="text-xs text-muted">
                        مربی: {{ $item->trainingClass->coach?->name ?? 'کادر فنی هیئت' }}
                    </span>

                    <a href="{{ route('register', ['class' => $item->trainingClass->slug]) }}"
                       class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-text transition hover:underline">
                        ثبت‌نام در این کلاس
                        <i class="fa-solid fa-arrow-left text-[0.65rem]" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        @endif
    </div>
</article>
