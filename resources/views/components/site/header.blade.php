@php
    $nav = config('navigation');

    // A nav item is "current" when its own route matches, or any pattern it claims.
    $isActive = function (array $item): bool {
        foreach ((array) ($item['active'] ?? []) as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }

        return isset($item['route']) && request()->routeIs($item['route']);
    };
@endphp

<header data-header
        class="sticky top-0 z-50 border-b border-transparent transition-[background-color,border-color,box-shadow] duration-300
               [&.is-scrolled]:glass [&.is-scrolled]:border-line [&.is-scrolled]:shadow-soft">
    {{-- Utility strip: contact details and portal entry, desktop only. --}}
    <div class="hidden bg-gray-900 text-gray-300 lg:block">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 py-2 text-xs">
            <div class="flex items-center gap-6">
                <a href="tel:{{ setting('phone') }}" class="flex items-center gap-2 transition hover:text-white">
                    <i class="fa-solid fa-phone text-[0.7rem] text-accent" aria-hidden="true"></i>
                    <span class="ltr">{{ fa(setting('phone')) }}</span>
                </a>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-clock text-[0.7rem] text-accent" aria-hidden="true"></i>
                    {{ setting('hours_weekdays') }}
                </span>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ setting('instagram') }}" target="_blank" rel="noopener noreferrer"
                   class="transition hover:text-white" aria-label="اینستاگرام هیئت جودو کازرون">
                    <x-icons.instagram class="h-4 w-4" />
                </a>
                <a href="{{ setting('telegram') }}" target="_blank" rel="noopener noreferrer"
                   class="transition hover:text-white" aria-label="کانال تلگرام هیئت جودو کازرون">
                    <x-icons.telegram class="h-4 w-4" />
                </a>
                <span class="h-4 w-px bg-white/15" aria-hidden="true"></span>
                <a href="{{ route('login') }}" class="flex items-center gap-2 font-medium transition hover:text-accent">
                    <i class="fa-solid fa-right-to-bracket text-[0.7rem]" aria-hidden="true"></i>
                    ورود به پرتال
                </a>
            </div>
        </div>
    </div>

    <nav class="bg-surface/85 lg:bg-transparent" aria-label="ناوبری اصلی">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:py-4">
            <x-site.brand />

            {{-- Desktop navigation --}}
            <ul class="hidden items-center gap-1 lg:flex">
                @foreach ($nav as $index => $item)
                    @php $active = $isActive($item); @endphp

                    @if (empty($item['columns']))
                        <li>
                            <a href="{{ route($item['route']) }}"
                               @if ($active) aria-current="page" @endif
                               class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-medium transition
                                      hover:bg-surface-muted hover:text-heading
                                      {{ $active ? 'text-brand-text' : 'text-copy' }}">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @else
                        <li class="relative" data-mega>
                            <button type="button" data-mega-trigger
                                    aria-expanded="false" aria-haspopup="true"
                                    aria-controls="mega-{{ $index }}"
                                    class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-medium transition
                                           hover:bg-surface-muted hover:text-heading
                                           {{ $active ? 'text-brand-text' : 'text-copy' }}">
                                {{ $item['label'] }}
                                <i class="fa-solid fa-chevron-down text-[0.65rem] opacity-60 transition-transform
                                          group-aria-expanded:rotate-180" aria-hidden="true"></i>
                            </button>

                            <div id="mega-{{ $index }}" data-mega-panel
                                 class="invisible absolute inset-inline-start-1/2 top-full z-50 w-[min(46rem,90vw)]
                                        -translate-x-1/2 translate-y-2 pt-3 opacity-0 transition duration-200 rtl:translate-x-1/2">
                                <div class="surface-card overflow-hidden p-2 shadow-pop">
                                    <div class="grid gap-1 sm:grid-cols-2">
                                        @foreach ($item['columns'] as $column)
                                            <div class="p-3">
                                                <p class="mb-2 px-2 text-xs font-semibold tracking-wide text-muted">
                                                    {{ $column['heading'] }}
                                                </p>
                                                <ul class="space-y-1">
                                                    @foreach ($column['links'] as $link)
                                                        <li>
                                                            <a href="{{ route($link['route']) }}"
                                                               class="group flex items-start gap-3 rounded-xl p-3 transition hover:bg-surface-muted">
                                                                <span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-lg
                                                                             bg-brand-soft text-brand-text transition group-hover:bg-brand group-hover:text-on-brand">
                                                                    <i class="fa-solid {{ $link['icon'] }} text-sm" aria-hidden="true"></i>
                                                                </span>
                                                                <span class="min-w-0">
                                                                    <span class="block text-sm font-semibold text-heading">{{ $link['label'] }}</span>
                                                                    <span class="mt-0.5 block text-xs leading-relaxed text-muted">{{ $link['description'] }}</span>
                                                                </span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach
                                    </div>

                                    @isset ($item['feature'])
                                        <a href="{{ route($item['feature']['route']) }}"
                                           class="mt-1 flex items-center justify-between gap-4 rounded-xl bg-gray-900 p-4 text-white transition hover:bg-gray-800:bg-gray-700">
                                            <span>
                                                <span class="block text-sm font-semibold">{{ $item['feature']['title'] }}</span>
                                                <span class="mt-1 block text-xs text-gray-300">{{ $item['feature']['description'] }}</span>
                                            </span>
                                            <span class="flex shrink-0 items-center gap-2 rounded-lg bg-accent px-3 py-2 text-xs font-bold text-on-accent">
                                                {{ $item['feature']['cta'] }}
                                                <i class="fa-solid fa-arrow-left text-[0.65rem]" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                    @endisset
                                </div>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>

            {{-- Actions --}}
            <div class="flex items-center gap-1.5">
                <button type="button" data-search-open
                        class="grid h-10 w-10 place-items-center rounded-xl text-copy transition hover:bg-surface-muted hover:text-heading"
                        aria-label="جستجو در سایت">
                    <i class="fa-solid fa-magnifying-glass text-sm" aria-hidden="true"></i>
                </button>

                <button type="button" data-theme-toggle
                        class="grid h-10 w-10 place-items-center rounded-xl text-copy transition hover:bg-surface-muted hover:text-heading"
                        aria-label="تغییر پوستهٔ سایت">
                    <i data-theme-icon class="fa-solid fa-circle-half-stroke text-sm" aria-hidden="true"></i>
                </button>

                <a href="{{ route('register') }}"
                   class="hidden rounded-xl bg-brand px-5 py-2.5 text-sm font-bold text-on-brand shadow-soft
                          transition hover:bg-brand-hover hover:shadow-lift sm:inline-flex">
                    ثبت‌نام
                </a>

                <button type="button" data-drawer-open
                        class="grid h-10 w-10 place-items-center rounded-xl text-copy transition hover:bg-surface-muted lg:hidden"
                        aria-expanded="false" aria-controls="mobile-drawer" aria-label="باز کردن منو">
                    <i class="fa-solid fa-bars text-base" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </nav>

    <x-site.drawer :nav="$nav" />
</header>
