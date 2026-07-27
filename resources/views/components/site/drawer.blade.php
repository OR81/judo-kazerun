@props(['nav' => []])

{{-- Mobile navigation. Starts inert so its links stay out of the tab order until opened. --}}
<div id="mobile-drawer" data-drawer inert
     class="pointer-events-none fixed inset-0 z-50 lg:hidden">

    <div data-drawer-backdrop data-drawer-close
         class="absolute inset-0 bg-gray-950/60 opacity-0 backdrop-blur-sm transition-opacity duration-300"></div>

    <div data-drawer-panel role="dialog" aria-modal="true" aria-label="منوی ناوبری"
         class="absolute inset-y-0 inset-inline-end-0 flex w-[min(22rem,88vw)] translate-x-full flex-col
                bg-surface shadow-pop transition-transform duration-300 rtl:-translate-x-full">

        <div class="flex items-center justify-between gap-3 border-b border-line p-4">
            <x-site.brand />
            <button type="button" data-drawer-close
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-xl text-copy transition hover:bg-surface-muted"
                    aria-label="بستن منو">
                <i class="fa-solid fa-xmark text-base" aria-hidden="true"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto overscroll-contain p-4" aria-label="ناوبری موبایل">
            <ul class="space-y-1">
                @foreach ($nav as $index => $item)
                    <li>
                        @if (empty($item['columns']))
                            <a href="{{ route($item['route']) }}"
                               @if (request()->routeIs($item['route'])) aria-current="page" @endif
                               class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition hover:bg-surface-muted
                                      {{ request()->routeIs($item['route']) ? 'bg-brand-soft text-brand-text' : 'text-copy' }}">
                                <i class="fa-solid {{ $item['icon'] }} w-5 text-center text-muted" aria-hidden="true"></i>
                                {{ $item['label'] }}
                            </a>
                        @else
                            <button type="button" data-accordion-trigger
                                    aria-expanded="false" aria-controls="drawer-section-{{ $index }}"
                                    class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-copy transition hover:bg-surface-muted">
                                <i class="fa-solid {{ $item['icon'] }} w-5 text-center text-muted" aria-hidden="true"></i>
                                <span class="flex-1 text-start">{{ $item['label'] }}</span>
                                <i class="fa-solid fa-chevron-down text-[0.7rem] text-muted transition-transform
                                          [button[aria-expanded=true]_&]:rotate-180" aria-hidden="true"></i>
                            </button>

                            <div id="drawer-section-{{ $index }}" class="hidden ps-4">
                                @foreach ($item['columns'] as $column)
                                    <p class="mt-3 px-3 text-xs font-semibold text-muted">{{ $column['heading'] }}</p>
                                    <ul class="mt-1 space-y-0.5">
                                        @foreach ($column['links'] as $link)
                                            <li>
                                                <a href="{{ route($link['route']) }}"
                                                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-copy transition hover:bg-surface-muted">
                                                    <i class="fa-solid {{ $link['icon'] }} w-4 text-center text-xs text-muted" aria-hidden="true"></i>
                                                    {{ $link['label'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="space-y-3 border-t border-line p-4">
            <a href="{{ route('register') }}"
               class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand px-5 py-3 text-sm font-bold text-on-brand transition hover:bg-brand-hover">
                <i class="fa-solid fa-user-plus text-xs" aria-hidden="true"></i>
                ثبت‌نام آنلاین
            </a>

            <a href="{{ route('login') }}"
               class="flex w-full items-center justify-center gap-2 rounded-xl border border-line px-5 py-3 text-sm font-semibold text-copy transition hover:bg-surface-muted">
                <i class="fa-solid fa-right-to-bracket text-xs" aria-hidden="true"></i>
                ورود به پرتال
            </a>

            <div class="flex items-center justify-center gap-5 pt-1 text-muted">
                <a href="{{ setting('instagram') }}" target="_blank" rel="noopener noreferrer"
                   class="transition hover:text-brand-text" aria-label="اینستاگرام">
                    <x-icons.instagram class="h-5 w-5" />
                </a>
                <a href="{{ setting('telegram') }}" target="_blank" rel="noopener noreferrer"
                   class="transition hover:text-brand-text" aria-label="تلگرام">
                    <x-icons.telegram class="h-5 w-5" />
                </a>
                <a href="tel:{{ setting('phone') }}" class="transition hover:text-brand-text" aria-label="تماس تلفنی">
                    <i class="fa-solid fa-phone" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</div>
