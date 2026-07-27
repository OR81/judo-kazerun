@props(['item', 'featured' => false])

{{-- `relative` anchors the stretched title link so the whole card is clickable. --}}
<article {{ $attributes->merge(['class' => 'reveal group surface-card relative flex flex-col overflow-hidden transition hover:shadow-lift']) }}>
    <a href="{{ route('news.show', $item) }}" class="relative block overflow-hidden {{ $featured ? 'aspect-16/9' : 'aspect-4/3' }}"
       tabindex="-1" aria-hidden="true">
        <img src="{{ $item->cover_url }}" alt="" loading="lazy" decoding="async"
             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">

        <span class="absolute top-3 inset-inline-start-3">
            <x-ui.badge :variant="$item->category->badgeClass()">{{ $item->category->name }}</x-ui.badge>
        </span>
    </a>

    <div class="flex flex-1 flex-col p-5">
        <h3 class="{{ $featured ? 'text-xl' : 'text-base' }} font-bold leading-snug text-heading">
            <a href="{{ route('news.show', $item) }}"
               class="transition after:absolute after:inset-0 hover:text-brand-text">
                {{ $item->title }}
            </a>
        </h3>

        <p class="mt-3 line-clamp-3 flex-1 text-sm leading-relaxed text-muted">
            {{ $item->excerpt }}
        </p>

        <div class="mt-5 flex items-center justify-between gap-3 border-t border-line pt-4 text-xs text-muted">
            <time datetime="{{ shamsi_attr($item->published_at) }}" class="flex items-center gap-1.5">
                <i class="fa-solid fa-calendar text-[0.7rem]" aria-hidden="true"></i>
                {{ shamsi($item->published_at) }}
            </time>

            <span class="flex items-center gap-3">
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-clock text-[0.7rem]" aria-hidden="true"></i>
                    {{ fa($item->read_minutes) }} دقیقه
                </span>
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-eye text-[0.7rem]" aria-hidden="true"></i>
                    {{ fa_number($item->views) }}
                </span>
            </span>
        </div>
    </div>
</article>
