{{-- Opens from the header button, or with "/" or Ctrl/⌘+K. --}}
<div data-search inert
     class="pointer-events-none fixed inset-0 z-[65] opacity-0 transition-opacity duration-200">

    <div data-search-close class="absolute inset-0 bg-ink/70 backdrop-blur-sm"></div>

    <div role="dialog" aria-modal="true" aria-labelledby="search-heading"
         class="relative mx-auto mt-[12vh] w-[min(42rem,92vw)]">

        <form action="{{ route('news.index') }}" method="GET" class="surface-card overflow-hidden shadow-pop">
            <h2 id="search-heading" class="sr-only">جستجو در سایت</h2>

            <div class="flex items-center gap-3 border-b border-line px-5 py-4">
                <i class="fa-solid fa-magnifying-glass text-muted" aria-hidden="true"></i>

                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="جستجوی خبر، مسابقه، مربی…"
                       autocomplete="off"
                       class="w-full border-0 bg-transparent p-0 text-base text-heading placeholder:text-muted focus:outline-none">

                <button type="button" data-search-close
                        class="shrink-0 rounded-lg border border-line px-2 py-1 text-[0.7rem] text-muted transition hover:bg-surface-muted">
                    Esc
                </button>
            </div>

            <div class="p-5">
                <p class="text-xs font-semibold text-muted">دسترسی سریع</p>

                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    @foreach ([
                        ['برنامهٔ تمرینی', 'schedule', 'fa-calendar-days'],
                        ['ثبت‌نام آنلاین', 'register', 'fa-user-plus'],
                        ['اخبار هیئت', 'news.index', 'fa-newspaper'],
                        ['رویدادها و مسابقات', 'events.index', 'fa-trophy'],
                        ['مربیان', 'coaches.index', 'fa-chalkboard-user'],
                        ['فرم‌ها و دانلودها', 'downloads', 'fa-file-arrow-down'],
                    ] as [$label, $route, $icon])
                        <a href="{{ route($route) }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-copy transition hover:bg-surface-muted">
                            <i class="fa-solid {{ $icon }} w-4 text-center text-xs text-muted" aria-hidden="true"></i>
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <button type="submit"
                        class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-brand px-5 py-3 text-sm font-bold text-on-brand transition hover:bg-brand-hover">
                    <i class="fa-solid fa-magnifying-glass text-xs" aria-hidden="true"></i>
                    جستجو در اخبار
                </button>
            </div>
        </form>
    </div>
</div>
