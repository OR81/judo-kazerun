@props([
    'title' => '',
    'eyebrow' => null,
    'description' => null,
    'breadcrumbs' => [],
])

{{-- Shared masthead for every inner page, so the site reads as one system. --}}
<section class="relative overflow-hidden border-b border-line bg-gray-900">
    {{-- Tatami-inspired grid, kept faint so it never competes with the heading. --}}
    <div class="pointer-events-none absolute inset-0 opacity-[0.07]" aria-hidden="true"
         style="background-image:linear-gradient(#fff 1px,transparent 1px),linear-gradient(90deg,#fff 1px,transparent 1px);background-size:56px 56px"></div>

    <div class="pointer-events-none absolute -top-24 inset-inline-start-1/4 h-72 w-72 rounded-full bg-brand/25 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -bottom-32 inset-inline-end-0 h-72 w-72 rounded-full bg-accent/15 blur-3xl" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:py-16">
        @if (! empty($breadcrumbs))
            <div class="[&_a]:text-gray-400 [&_a:hover]:text-accent [&_ol]:text-gray-400 [&_span]:text-gray-200">
                <x-ui.breadcrumbs :items="$breadcrumbs" />
            </div>
        @endif

        <div class="mt-6 max-w-3xl">
            @if ($eyebrow)
                <p class="flex items-center gap-2 text-sm font-bold text-accent">
                    <span class="h-px w-6 bg-accent" aria-hidden="true"></span>
                    {{ $eyebrow }}
                </p>
            @endif

            <h1 class="mt-3 text-3xl font-extrabold text-balance text-white sm:text-4xl lg:text-5xl">
                {{ $title }}
            </h1>

            @if ($description)
                <p class="mt-4 max-w-2xl leading-relaxed text-gray-300">{{ $description }}</p>
            @endif

            @if (trim($slot) !== '')
                <div class="mt-7 flex flex-wrap items-center gap-3">{{ $slot }}</div>
            @endif
        </div>
    </div>
</section>
