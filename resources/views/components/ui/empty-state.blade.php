@props([
    'icon' => 'fa-inbox',
    'title' => 'موردی یافت نشد',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'surface-card flex flex-col items-center px-6 py-16 text-center']) }}>
    <span class="grid h-16 w-16 place-items-center rounded-2xl bg-surface-muted text-muted">
        <i class="fa-solid {{ $icon }} text-2xl" aria-hidden="true"></i>
    </span>

    <p class="mt-5 text-lg font-bold text-heading">{{ $title }}</p>

    @if ($description)
        <p class="mt-2 max-w-sm text-sm leading-relaxed text-muted">{{ $description }}</p>
    @endif

    @if (trim($slot) !== '')
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">{{ $slot }}</div>
    @endif
</div>
