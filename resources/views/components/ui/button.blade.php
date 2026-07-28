@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'icon' => null,
    'iconEnd' => null,
])

@php
    $variants = [
        'primary' => 'bg-brand text-on-brand shadow-soft hover:bg-brand-hover hover:shadow-lift',
        'accent' => 'bg-accent text-on-accent shadow-soft hover:bg-accent-hover hover:shadow-lift',
        'ink' => 'bg-ink text-white shadow-soft hover:bg-ink-soft',
        'outline' => 'border border-line bg-surface text-copy hover:border-line-strong hover:bg-surface-muted',
        'ghost' => 'text-copy hover:bg-surface-muted hover:text-heading',
        'soft' => 'bg-brand-soft text-brand-text hover:bg-brand hover:text-on-brand',
    ];

    $sizes = [
        'sm' => 'px-4 py-2 text-xs gap-1.5',
        'md' => 'px-5 py-2.5 text-sm gap-2',
        'lg' => 'px-7 py-3.5 text-base gap-2.5',
    ];

    $classes = implode(' ', [
        'inline-flex items-center justify-center rounded-xl font-bold transition',
        'disabled:pointer-events-none disabled:opacity-50',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
    ]);

    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @else type="{{ $attributes->get('type', 'button') }}" @endif
    {{ $attributes->merge(['class' => $classes])->except('type') }}
>
    @if ($icon)
        <i class="fa-solid {{ $icon }} text-[0.85em]" aria-hidden="true"></i>
    @endif

    {{ $slot }}

    @if ($iconEnd)
        <i class="fa-solid {{ $iconEnd }} text-[0.85em]" aria-hidden="true"></i>
    @endif
</{{ $tag }}>
