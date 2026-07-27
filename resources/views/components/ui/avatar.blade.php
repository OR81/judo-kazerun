@props([
    'src' => null,
    'name' => '',
    'size' => 'md',
    'ring' => false,
])

@php
    $sizes = [
        'sm' => 'h-10 w-10 text-xs',
        'md' => 'h-14 w-14 text-sm',
        'lg' => 'h-20 w-20 text-lg',
        'xl' => 'h-28 w-28 text-2xl',
    ];

    $box = $sizes[$size] ?? $sizes['md'];

    // Initials fallback keeps the layout intact if an image 404s.
    $parts = preg_split('/\s+/u', trim($name)) ?: [];
    $initials = mb_substr($parts[0] ?? '', 0, 1).mb_substr($parts[1] ?? '', 0, 1);
@endphp

<span {{ $attributes->merge([
    'class' => "relative inline-grid shrink-0 place-items-center overflow-hidden rounded-full bg-surface-muted font-bold text-muted {$box} "
        .($ring ? 'ring-2 ring-accent ring-offset-2 ring-offset-surface' : ''),
]) }}>
    @if ($src)
        <img src="{{ $src }}" alt="" loading="lazy" decoding="async"
             class="h-full w-full object-cover">
    @else
        <span aria-hidden="true">{{ $initials }}</span>
    @endif
</span>
