@props(['week', 'venues', 'todayIndex'])

{{--
    تابلوی سانس‌ها — the weekly state of every hall.

    All seven days are rendered. Without JavaScript they simply stack as a normal
    weekly timetable; hall.js turns the headings into a tab strip and shows one day
    at a time. The venue chips then narrow the visible day to a single hall.
--}}
<div data-hall class="mt-10">

    {{-- ======================================================== legend --}}
    <ul class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-muted" aria-label="راهنمای رنگ‌ها">
        @foreach (App\Enums\SlotStatus::cases() as $case)
            <li class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full {{ $case->railClass() }}" aria-hidden="true"></span>
                {{ $case->label() }}
            </li>
        @endforeach
    </ul>

    {{-- =========================================================== tabs --}}
    <div class="mt-5 overflow-x-auto pb-1">
        <div data-hall-tabs role="tablist" aria-label="روزهای هفته"
             class="flex min-w-max gap-2">
            @foreach ($week as $day)
                <button type="button" role="tab"
                        data-hall-tab="{{ $day['index'] }}"
                        id="hall-tab-{{ $day['index'] }}"
                        aria-controls="hall-panel-{{ $day['index'] }}"
                        aria-selected="{{ $day['isToday'] ? 'true' : 'false' }}"
                        tabindex="{{ $day['isToday'] ? '0' : '-1' }}"
                        @if ($day['isToday']) data-active @endif
                        class="group flex shrink-0 flex-col items-center gap-0.5 rounded-xl border border-line
                               bg-surface px-4 py-2.5 text-sm font-semibold text-copy transition
                               hover:border-line-strong hover:bg-surface-muted
                               data-active:border-brand data-active:bg-brand data-active:text-on-brand">
                    <span>{{ $day['name'] }}</span>

                    <span class="text-[0.65rem] font-medium text-muted transition group-data-active:text-on-brand/85">
                        @if ($day['openCount'])
                            {{ fa($day['openCount']) }} سانس آزاد
                        @else
                            بدون سانس آزاد
                        @endif
                    </span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ================================================== venue filter --}}
    @if ($venues->count() > 1)
        <div class="mt-4 flex flex-wrap items-center gap-2" role="group" aria-label="فیلتر سالن">
            <span class="text-xs font-semibold text-muted">سالن:</span>

            <button type="button" data-hall-venue="all" aria-pressed="true"
                    class="rounded-lg border border-line bg-surface px-3 py-1.5 text-xs font-semibold text-copy
                           transition hover:border-line-strong
                           aria-pressed:border-brand aria-pressed:bg-brand-soft aria-pressed:text-brand-text">
                همه
            </button>

            @foreach ($venues as $venue)
                <button type="button" data-hall-venue="{{ $venue->id }}" aria-pressed="false"
                        class="rounded-lg border border-line bg-surface px-3 py-1.5 text-xs font-semibold text-copy
                               transition hover:border-line-strong
                               aria-pressed:border-brand aria-pressed:bg-brand-soft aria-pressed:text-brand-text">
                    {{ $venue->name }}
                </button>
            @endforeach
        </div>
    @endif

    {{-- ========================================================= panels --}}
    @foreach ($week as $day)
        <section data-hall-panel="{{ $day['index'] }}"
                 id="hall-panel-{{ $day['index'] }}"
                 role="tabpanel"
                 aria-labelledby="hall-tab-{{ $day['index'] }}"
                 tabindex="0"
                 @if ($day['isToday']) data-active @endif
                 class="mt-6">

            {{-- Only a heading for the no-JS stack; the tab strip already names the day. --}}
            <h3 class="mb-4 flex items-center gap-3 text-sm font-bold text-heading js:sr-only">
                {{ $day['name'] }}
                @if ($day['isToday'])
                    <span class="rounded-full bg-accent-soft px-2 py-0.5 text-[0.65rem] text-accent-text">امروز</span>
                @endif
            </h3>

            @if ($day['slots']->isEmpty())
                <x-ui.empty-state icon="fa-mug-hot"
                                  title="این روز برنامه‌ای ندارد"
                                  description="سالن در این روز تعطیل است. روز دیگری را انتخاب کنید یا با دفتر هیئت تماس بگیرید." />
            @else
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($day['slots'] as $entry)
                        <x-cards.slot :hall-slot="$entry" />
                    @endforeach
                </div>

                <p data-hall-empty hidden class="mt-4 rounded-card border border-dashed border-line-strong
                                                 px-4 py-8 text-center text-sm text-muted">
                    این سالن در روز انتخاب‌شده برنامه‌ای ندارد.
                </p>
            @endif
        </section>
    @endforeach

    <p data-hall-status class="sr-only" role="status" aria-live="polite"></p>
</div>
