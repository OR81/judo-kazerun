@props(['event', 'compact' => false])

<article {{ $attributes->merge(['class' => 'reveal group surface-card relative flex gap-4 p-4 transition hover:shadow-lift sm:gap-5 sm:p-5']) }}>
    {{-- Date block, Shamsi. --}}
    <div class="flex h-20 w-16 shrink-0 flex-col items-center justify-center rounded-xl bg-gray-900 text-white">
        <span class="text-2xl leading-none font-extrabold">{{ shamsi($event->starts_at, 'day') }}</span>
        <span class="mt-1 text-[0.7rem] text-gray-300">{{ shamsi($event->starts_at, 'month') }}</span>
    </div>

    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.badge :variant="$event->type->badgeClass()" :icon="$event->type->icon()">
                {{ $event->type->label() }}
            </x-ui.badge>

            @if ($event->starts_at->isFuture() && $event->days_until <= 14)
                <x-ui.badge variant="accent" icon="fa-hourglass-half">
                    {{ $event->days_until === 0 ? 'امروز' : fa($event->days_until).' روز مانده' }}
                </x-ui.badge>
            @endif
        </div>

        <h3 class="mt-2.5 text-base font-bold leading-snug text-heading sm:text-lg">
            <a href="{{ route('events.show', $event) }}"
               class="transition after:absolute after:inset-0 hover:text-brand-text">
                {{ $event->title }}
            </a>
        </h3>

        @unless ($compact)
            <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-muted">{{ $event->summary }}</p>
        @endunless

        <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-muted">
            <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-location-dot text-[0.7rem]" aria-hidden="true"></i>
                {{ $event->location }}
            </span>

            <time datetime="{{ shamsi_attr($event->starts_at) }}" class="flex items-center gap-1.5">
                <i class="fa-solid fa-calendar text-[0.7rem]" aria-hidden="true"></i>
                {{ shamsi($event->starts_at, 'full') }}
            </time>

            @if ($event->fee)
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-tag text-[0.7rem]" aria-hidden="true"></i>
                    {{ toman($event->fee) }}
                </span>
            @endif
        </div>
    </div>
</article>
