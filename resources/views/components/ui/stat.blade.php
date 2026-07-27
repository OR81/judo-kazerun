@props([
    'value' => 0,
    'label' => '',
    'icon' => null,
    'suffix' => null,
    'animate' => true,
])

<div {{ $attributes->merge(['class' => 'reveal surface-card p-6 text-center']) }}>
    @if ($icon)
        <span class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-brand-soft text-brand-text">
            <i class="fa-solid {{ $icon }} text-lg" aria-hidden="true"></i>
        </span>
    @endif

    <p class="mt-4 text-3xl font-extrabold text-heading sm:text-4xl">
        @if ($animate && is_numeric($value))
            {{-- counters.js ticks this up on first view and formats it in Persian. --}}
            <span data-counter="{{ $value }}">{{ fa_number($value) }}</span>
        @else
            {{ is_numeric($value) ? fa_number($value) : $value }}
        @endif

        @if ($suffix)
            <span class="text-lg font-bold text-accent-text">{{ $suffix }}</span>
        @endif
    </p>

    <p class="mt-2 text-sm text-muted">{{ $label }}</p>
</div>
