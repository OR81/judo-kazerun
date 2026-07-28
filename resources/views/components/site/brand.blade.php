@props(['compact' => false])

{{--
    Wordmark. The emblem is an inline SVG so it costs no request and stays crisp
    at any size. Kept identical to public/favicon.svg.
--}}
<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'group flex items-center gap-3']) }}>
    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-ink shadow-soft transition group-hover:shadow-lift">
        <svg viewBox="0 0 32 32" class="h-6 w-6" role="img" aria-label="نشان هیئت جودو کازرون">
            {{-- The mat keyline, the crossed judogi lapels, and the belt. --}}
            <rect x="3" y="3" width="26" height="26" rx="6" fill="none" stroke="#D97706" stroke-width="1.5" opacity=".55" />
            <path d="M10 8 L16 17 L22 8" fill="none" stroke="#fff" stroke-width="3.2"
                  stroke-linecap="round" stroke-linejoin="round" />
            <rect x="8" y="20.4" width="16" height="3.4" rx="1.7" fill="#D97706" />
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
