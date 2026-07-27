@props(['compact' => false])

{{--
    Wordmark. The emblem is an inline SVG so it costs no request, scales
    crisply, and inherits the current colour in both themes.
--}}
<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'group flex items-center gap-3']) }}>
    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gray-900 shadow-soft transition group-hover:shadow-lift">
        <svg viewBox="0 0 32 32" class="h-6 w-6" role="img" aria-label="نشان هیئت جودو کازرون">
            {{-- Stylised judogi lapel crossing over a rising sun. --}}
            <circle cx="16" cy="16" r="15" fill="none" stroke="#F59E0B" stroke-width="1.5" opacity=".55" />
            <path d="M16 4 L25 12 L16 20 L7 12 Z" fill="#DC2626" />
            <path d="M16 12 L25 20 L16 28 L7 20 Z" fill="#F59E0B" opacity=".92" />
            <circle cx="16" cy="16" r="2.6" fill="#fff" />
        </svg>
    </span>

    @unless ($compact)
        <span class="min-w-0">
            <span class="block text-sm leading-tight font-extrabold text-heading sm:text-base">
                هیئت جودو کازرون
            </span>
            <span class="mt-0.5 hidden text-[0.7rem] leading-tight text-muted sm:block">
                {{ setting('site_tagline', 'پرورش قهرمانان، ترویج اخلاق پهلوانی') }}
            </span>
        </span>
    @endunless
</a>
