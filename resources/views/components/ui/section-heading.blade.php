@props([
    'eyebrow' => null,
    'title' => '',
    'description' => null,
    'align' => 'start',
    'level' => 'h2',
])

<div {{ $attributes->merge([
    'class' => 'reveal max-w-2xl '.($align === 'center' ? 'mx-auto text-center' : ''),
]) }}>
    @if ($eyebrow)
        <p class="flex items-center gap-2 text-sm font-bold text-brand-text {{ $align === 'center' ? 'justify-center' : '' }}">
            <span class="h-px w-6 bg-brand" aria-hidden="true"></span>
            {{ $eyebrow }}
        </p>
    @endif

    <{{ $level }} class="mt-3 text-2xl font-extrabold text-balance text-heading sm:text-3xl lg:text-4xl">
        {{ $title }}
    </{{ $level }}>

    @if ($description)
        <p class="mt-4 leading-relaxed text-muted">{{ $description }}</p>
    @endif

    {{ $slot }}
</div>
