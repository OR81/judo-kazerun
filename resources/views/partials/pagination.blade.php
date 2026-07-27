@if ($paginator->hasPages())
    {{-- Arrows are mirrored for RTL: "next" points left. --}}
    <nav role="navigation" aria-label="صفحه‌بندی" class="mt-12 flex items-center justify-center">
        <ul class="flex flex-wrap items-center gap-1.5">
            {{-- Previous --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true"
                          class="grid h-10 w-10 place-items-center rounded-xl border border-line text-muted opacity-40">
                        <i class="fa-solid fa-chevron-right text-xs" aria-hidden="true"></i>
                        <span class="sr-only">صفحهٔ قبل</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                       class="grid h-10 w-10 place-items-center rounded-xl border border-line text-copy transition hover:border-brand hover:bg-brand-soft hover:text-brand-text">
                        <i class="fa-solid fa-chevron-right text-xs" aria-hidden="true"></i>
                        <span class="sr-only">صفحهٔ قبل</span>
                    </a>
                @endif
            </li>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="grid h-10 w-10 place-items-center text-muted" aria-hidden="true">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                      class="grid h-10 w-10 place-items-center rounded-xl bg-brand text-sm font-bold text-on-brand">
                                    {{ fa($page) }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   class="grid h-10 w-10 place-items-center rounded-xl border border-line text-sm font-medium text-copy transition hover:border-brand hover:bg-brand-soft hover:text-brand-text">
                                    {{ fa($page) }}
                                    <span class="sr-only">صفحهٔ {{ fa($page) }}</span>
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                       class="grid h-10 w-10 place-items-center rounded-xl border border-line text-copy transition hover:border-brand hover:bg-brand-soft hover:text-brand-text">
                        <i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i>
                        <span class="sr-only">صفحهٔ بعد</span>
                    </a>
                @else
                    <span aria-disabled="true"
                          class="grid h-10 w-10 place-items-center rounded-xl border border-line text-muted opacity-40">
                        <i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i>
                        <span class="sr-only">صفحهٔ بعد</span>
                    </span>
                @endif
            </li>
        </ul>
    </nav>

    <p class="mt-4 text-center text-xs text-muted" role="status">
        نمایش {{ fa_number($paginator->firstItem() ?? 0) }} تا {{ fa_number($paginator->lastItem() ?? 0) }}
        از {{ fa_number($paginator->total()) }} مورد
    </p>
@endif
