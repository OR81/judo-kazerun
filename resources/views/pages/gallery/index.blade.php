@extends('layouts.app')

@section('title', ($activeType === \App\Enums\GalleryType::Video ? 'ویدئوها' : 'گالری تصاویر').' — هیئت جودو کازرون')
@section('meta_description', 'آلبوم تصاویر و ویدئوهای مسابقات، اردوها و تمرینات هیئت جودو شهرستان کازرون.')

@section('content')

    <x-ui.page-header
        eyebrow="رسانه"
        :title="$activeType === \App\Enums\GalleryType::Video ? 'ویدئوهای هیئت' : 'گالری تصاویر'"
        description="لحظه‌های ماندگار مسابقات، اردوها و تمرینات روزانهٔ جودوکاران کازرون."
        :breadcrumbs="[['label' => 'گالری']]" />

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">

        {{-- ===================================================== tabs --}}
        <div class="flex flex-wrap gap-2" role="group" aria-label="نوع رسانه">
            @foreach ([
                [\App\Enums\GalleryType::Photo, route('gallery')],
                [\App\Enums\GalleryType::Video, route('gallery.videos')],
            ] as [$type, $url])
                <a href="{{ $url }}"
                   @if ($activeType === $type) aria-current="page" @endif
                   class="flex items-center gap-2 rounded-xl border px-5 py-2.5 text-sm font-semibold transition
                          {{ $activeType === $type ? 'border-brand bg-brand text-on-brand' : 'border-line bg-surface text-copy hover:border-line-strong' }}">
                    <i class="fa-solid {{ $type->icon() }} text-xs" aria-hidden="true"></i>
                    {{ $type->label() }}
                    <span class="opacity-70">({{ fa($counts[$type->value]) }})</span>
                </a>
            @endforeach
        </div>

        @if ($albums->isEmpty())
            <x-ui.empty-state class="mt-10" icon="fa-images"
                              title="آلبومی در این بخش وجود ندارد"
                              description="به‌زودی تصاویر و ویدئوهای جدید بارگذاری می‌شود." />
        @else
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($albums as $album)
                    <a href="{{ route('gallery.show', $album) }}"
                       class="reveal group surface-card relative overflow-hidden transition hover:shadow-lift"
                       data-reveal-delay="{{ ($loop->index % 3) * 90 }}">

                        <div class="relative aspect-4/3 overflow-hidden">
                            <img src="{{ $album->cover_url }}" alt="" loading="lazy" decoding="async"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">

                            <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-transparent to-transparent"
                                 aria-hidden="true"></div>

                            @if ($album->type === \App\Enums\GalleryType::Video)
                                <span class="absolute inset-0 grid place-items-center">
                                    <span class="grid h-14 w-14 place-items-center rounded-full bg-white/20 backdrop-blur-sm transition group-hover:bg-brand">
                                        <i class="fa-solid fa-play text-lg text-white" aria-hidden="true"></i>
                                    </span>
                                </span>
                            @endif

                            <span class="absolute top-3 inset-inline-end-3">
                                <x-ui.badge variant="ink" :icon="$album->type->icon()">
                                    {{ fa($album->items->count()) }}
                                </x-ui.badge>
                            </span>
                        </div>

                        <div class="p-5">
                            <h2 class="font-bold text-heading transition group-hover:text-brand-text">{{ $album->title }}</h2>
                            <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-muted">{{ $album->description }}</p>

                            <p class="mt-4 flex items-center gap-2 border-t border-line pt-4 text-xs text-muted">
                                <i class="fa-solid fa-calendar text-[0.7rem]" aria-hidden="true"></i>
                                {{ shamsi($album->taken_on) }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

@endsection
