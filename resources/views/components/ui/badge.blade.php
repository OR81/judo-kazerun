@props([
    'variant' => 'neutral',
    'icon' => null,
    'class' => '',
])

@php
    $variants = [
        'neutral' => 'bg-surface-muted text-copy',
        'brand' => 'bg-brand-soft text-brand-text',
        'accent' => 'bg-accent-soft text-accent-text',
        'success' => 'bg-emerald-500/12 text-emerald-700',
        'info' => 'bg-sky-500/12 text-sky-700',
        'dark' => 'bg-gray-900 text-white',
    ];

    // A raw utility string may be passed instead of a named variant — the enums
    // use that to carry their own badge colours.
    $tone = $variants[$variant] ?? $variant;
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {$tone} {$class}",
]) }}>
    @if ($icon)
        <i class="fa-solid {{ $icon }} text-[0.7rem]" aria-hidden="true"></i>
    @endif
    {{ $slot }}
</span>
