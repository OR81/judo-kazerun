@php
    $number = setting('whatsapp');
    $message = rawurlencode('سلام، درباره‌ی کلاس‌های جودو سؤالی داشتم.');
@endphp

@if ($number)
    <a href="https://wa.me/{{ $number }}?text={{ $message }}"
       target="_blank" rel="noopener noreferrer"
       class="group fixed bottom-6 inset-inline-end-6 z-40 flex items-center gap-3 rounded-full bg-[#25D366]
              py-3 ps-3 pe-4 text-white shadow-pop transition-transform hover:scale-105"
       aria-label="گفتگو در واتس‌اپ با هیئت جودو کازرون">
        <x-icons.whatsapp class="h-6 w-6 shrink-0" />

        {{-- Label expands on hover at desktop width; the icon alone carries it on mobile. --}}
        <span class="hidden text-sm font-bold whitespace-nowrap sm:inline">مشاوره در واتس‌اپ</span>
    </a>
@endif
