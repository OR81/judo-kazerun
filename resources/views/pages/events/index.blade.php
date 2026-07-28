@extends('layouts.app')

@section('title', 'تقویم رویدادها و مسابقات — هیئت جودو کازرون')
@section('meta_description', 'تقویم مسابقات، آزمون‌های ارتقای دان، اردوهای آماده‌سازی و سمینارهای هیئت جودو شهرستان کازرون.')

@section('content')

    <x-ui.page-header
        eyebrow="تقویم"
        title="رویدادها و مسابقات"
        description="مسابقات قهرمانی، آزمون‌های دان، اردوهای آماده‌سازی و کارگاه‌های آموزشی هیئت."
        :breadcrumbs="[['label' => 'رویدادها']]" />

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">

        {{-- ================================================ type filter --}}
        <div class="flex flex-wrap gap-2" role="group" aria-label="فیلتر نوع رویداد">
            <a href="{{ route('events.index') }}"
               @if ($activeType === 'all') aria-current="page" @endif
               class="rounded-xl border px-5 py-2.5 text-sm font-semibold transition
                      {{ $activeType === 'all' ? 'border-brand bg-brand text-on-brand' : 'border-line bg-surface text-copy hover:border-line-strong' }}">
                همهٔ رویدادها
            </a>

            @foreach ($types as $value => $label)
                <a href="{{ route('events.index', ['type' => $value]) }}"
                   @if ($activeType === $value) aria-current="page" @endif
                   class="rounded-xl border px-5 py-2.5 text-sm font-semibold transition
                          {{ $activeType === $value ? 'border-brand bg-brand text-on-brand' : 'border-line bg-surface text-copy hover:border-line-strong' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="mt-10 grid gap-10 lg:grid-cols-12">

            {{-- =============================================== upcoming --}}
            <div class="lg:col-span-8">
                <h2 class="flex items-center gap-3 text-xl font-extrabold text-heading">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand text-on-brand">
                        <i class="fa-solid fa-calendar-day text-sm" aria-hidden="true"></i>
                    </span>
                    رویدادهای پیش‌رو
                    <span class="text-sm font-normal text-muted">({{ fa($upcoming->count()) }} مورد)</span>
                </h2>

                @if ($upcoming->isEmpty())
                    <x-ui.empty-state class="mt-6" icon="fa-calendar-xmark"
                                      title="رویداد پیش‌رویی ثبت نشده"
                                      description="برنامهٔ مسابقات و آزمون‌های آینده به‌زودی اعلام می‌شود.">
                        <x-ui.button :href="route('events.index')" variant="outline">حذف فیلتر</x-ui.button>
                    </x-ui.empty-state>
                @else
                    @foreach ($byMonth as $month => $events)
                        <section class="mt-8">
                            <h3 class="flex items-center gap-3 text-sm font-bold text-muted">
                                <span class="h-px flex-1 bg-line" aria-hidden="true"></span>
                                {{ $month }}
                                <span class="h-px flex-1 bg-line" aria-hidden="true"></span>
                            </h3>

                            <div class="mt-4 space-y-4">
                                @foreach ($events as $event)
                                    <x-cards.event :event="$event" data-reveal-delay="{{ $loop->index * 70 }}" />
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                @endif

                {{-- =============================================== past --}}
                @if ($past->isNotEmpty())
                    <section class="mt-14">
                        <h2 class="flex items-center gap-3 text-xl font-extrabold text-heading">
                            <span class="grid h-9 w-9 place-items-center rounded-xl bg-surface-muted text-muted">
                                <i class="fa-solid fa-clock-rotate-left text-sm" aria-hidden="true"></i>
                            </span>
                            رویدادهای برگزارشده
                        </h2>

                        <div class="mt-6 space-y-3">
                            @foreach ($past as $event)
                                <a href="{{ route('events.show', $event) }}"
                                   class="reveal surface-card flex flex-wrap items-center justify-between gap-4 p-4 opacity-80 transition hover:opacity-100 hover:shadow-lift">
                                    <div class="flex min-w-0 items-center gap-4">
                                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-surface-muted text-muted">
                                            <i class="fa-solid {{ $event->type->icon() }} text-sm" aria-hidden="true"></i>
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block truncate font-semibold text-heading">{{ $event->title }}</span>
                                            <span class="mt-0.5 block text-xs text-muted">{{ $event->location }}</span>
                                        </span>
                                    </div>

                                    <time datetime="{{ shamsi_attr($event->starts_at) }}" class="shrink-0 text-xs text-muted">
                                        {{ shamsi($event->starts_at) }}
                                    </time>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- ================================================ sidebar --}}
            <aside class="lg:col-span-4">
                <div class="sticky top-24 space-y-6">
                    {{-- Next event countdown --}}
                    @if ($next = $upcoming->first())
                        <div class="surface-card overflow-hidden">
                            <div class="bg-ink p-6 text-center">
                                <p class="text-xs font-semibold text-accent">نزدیک‌ترین رویداد</p>
                                <p class="mt-3 text-4xl font-extrabold text-white">{{ fa($next->days_until) }}</p>
                                <p class="mt-1 text-xs text-on-ink-muted">روز مانده</p>
                            </div>

                            <div class="p-6">
                                <h3 class="font-bold text-heading">{{ $next->title }}</h3>
                                <p class="mt-2 text-xs text-muted">{{ shamsi($next->starts_at, 'full') }}</p>

                                <x-ui.button :href="route('events.show', $next)" variant="primary"
                                             size="sm" class="mt-4 w-full">
                                    جزئیات رویداد
                                </x-ui.button>
                            </div>
                        </div>
                    @endif

                    {{-- Legend --}}
                    <div class="surface-card p-6">
                        <h3 class="font-bold text-heading">انواع رویداد</h3>

                        <ul class="mt-4 space-y-3">
                            @foreach (\App\Enums\EventType::cases() as $type)
                                <li class="flex items-center gap-3 text-sm">
                                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg {{ $type->badgeClass() }}">
                                        <i class="fa-solid {{ $type->icon() }} text-xs" aria-hidden="true"></i>
                                    </span>
                                    <span class="text-copy">{{ $type->label() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="surface-card p-6">
                        <h3 class="font-bold text-heading">اطلاع‌رسانی رویدادها</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">
                            برای باخبر شدن از مسابقات و آزمون‌های جدید، در خبرنامهٔ هیئت عضو شوید.
                        </p>
                        <x-ui.button :href="route('contact')" variant="outline" size="sm"
                                     icon="fa-headset" class="mt-4 w-full">
                            تماس با دبیرخانه
                        </x-ui.button>
                    </div>
                </div>
            </aside>
        </div>
    </div>

@endsection
