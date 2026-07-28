@extends('layouts.app')

@section('title', $album->title.' — گالری هیئت جودو کازرون')
@section('meta_description', $album->description)
@section('og_image', $album->cover_url)

@section('content')

    <x-ui.page-header
        :eyebrow="$album->type->label()"
        :title="$album->title"
        :description="$album->description"
        :breadcrumbs="[
            ['label' => 'گالری', 'url' => route('gallery')],
            ['label' => $album->title],
        ]">

        <span class="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-white/5 px-5 py-2.5 text-sm font-semibold text-on-ink">
            <i class="fa-solid {{ $album->type->icon() }} text-xs" aria-hidden="true"></i>
            {{ fa($album->items->count()) }} {{ $album->type->label() }}
        </span>

        @if ($album->taken_on)
            <span class="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-white/5 px-5 py-2.5 text-sm font-semibold text-on-ink">
                <i class="fa-solid fa-calendar text-xs" aria-hidden="true"></i>
                {{ shamsi($album->taken_on) }}
            </span>
        @endif

        @if ($album->event)
            <x-ui.button :href="route('events.show', $album->event)" variant="accent" icon="fa-trophy">
                رویداد مرتبط
            </x-ui.button>
        @endif
    </x-ui.page-header>

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">

        {{-- Masonry via CSS columns; each tile reserves its intrinsic ratio so
             nothing shifts as images arrive. --}}
        <div class="masonry-1 sm:masonry-2 lg:masonry-3">
            @foreach ($album->items as $item)
                <button type="button"
                        data-lightbox
                        data-lightbox-type="{{ $item->type->value }}"
                        data-lightbox-src="{{ $item->url }}"
                        data-lightbox-caption="{{ $item->caption }}"
                        class="masonry-item group relative block w-full overflow-hidden rounded-card"
                        aria-label="بزرگ‌نمایی: {{ $item->caption }}">

                    <img src="{{ $item->thumbnail_url }}" alt="{{ $item->caption }}"
                         loading="lazy" decoding="async"
                         width="{{ $item->width }}" height="{{ $item->height }}"
                         style="aspect-ratio: {{ $item->aspect_ratio }}"
                         class="w-full bg-surface-muted object-cover transition duration-500 group-hover:scale-105">

                    <span class="absolute inset-0 bg-ink/0 transition group-hover:bg-ink/35" aria-hidden="true"></span>

                    <span class="absolute inset-0 grid place-items-center opacity-0 transition group-hover:opacity-100"
                          aria-hidden="true">
                        <span class="grid h-12 w-12 place-items-center rounded-full bg-white/25 backdrop-blur-sm">
                            <i class="fa-solid {{ $item->type === \App\Enums\GalleryType::Video ? 'fa-play' : 'fa-expand' }} text-white"></i>
                        </span>
                    </span>
                </button>
            @endforeach
        </div>

        @if ($others->isNotEmpty())
            <section class="mt-16 border-t border-line pt-14">
                <x-ui.section-heading eyebrow="گالری" title="آلبوم‌های دیگر" />

                <div class="mt-8 grid gap-6 sm:grid-cols-3">
                    @foreach ($others as $other)
                        <a href="{{ route('gallery.show', $other) }}"
                           class="reveal group relative aspect-4/3 overflow-hidden rounded-card"
                           data-reveal-delay="{{ $loop->index * 90 }}">
                            <img src="{{ $other->cover_url }}" alt="" loading="lazy"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-ink/85 to-transparent"></div>
                            <span class="absolute inset-x-0 bottom-0 p-4 font-semibold text-white">{{ $other->title }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

@endsection
