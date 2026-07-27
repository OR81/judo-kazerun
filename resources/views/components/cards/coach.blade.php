@props(['coach'])

<article {{ $attributes->merge(['class' => 'reveal group surface-card relative overflow-hidden transition hover:shadow-lift']) }}>
    <div class="relative aspect-4/5 overflow-hidden bg-surface-muted">
        <img src="{{ $coach->photo_url }}" alt="" loading="lazy" decoding="async"
             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">

        {{-- Scrim keeps the name legible over any photo. --}}
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950/85 via-gray-950/25 to-transparent" aria-hidden="true"></div>

        <div class="absolute top-3 inset-inline-end-3">
            <x-ui.badge variant="dark" icon="fa-award">{{ $coach->dan_label }}</x-ui.badge>
        </div>

        <div class="absolute inset-x-0 bottom-0 p-5">
            <h3 class="text-lg font-bold text-white">
                <a href="{{ route('coaches.show', $coach) }}" class="after:absolute after:inset-0">
                    {{ $coach->name }}
                </a>
            </h3>
            <p class="mt-1 text-sm text-gray-300">{{ $coach->title }}</p>
        </div>
    </div>

    <div class="p-5">
        <p class="line-clamp-2 text-sm leading-relaxed text-muted">{{ $coach->summary }}</p>

        <div class="mt-4 flex flex-wrap gap-2">
            @foreach (array_slice($coach->specialties ?? [], 0, 3) as $specialty)
                <x-ui.badge variant="neutral">{{ $specialty }}</x-ui.badge>
            @endforeach
        </div>

        <div class="mt-5 flex items-center justify-between border-t border-line pt-4 text-xs text-muted">
            <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-clock-rotate-left text-[0.7rem]" aria-hidden="true"></i>
                {{ fa($coach->experience_years) }} سال سابقه
            </span>

            <span class="flex items-center gap-1.5 font-semibold text-brand-text">
                مشاهدهٔ پروفایل
                <i class="fa-solid fa-arrow-left text-[0.7rem] transition group-hover:-translate-x-1" aria-hidden="true"></i>
            </span>
        </div>
    </div>
</article>
