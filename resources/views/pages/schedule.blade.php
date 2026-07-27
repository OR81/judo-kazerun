@extends('layouts.app')

@section('title', 'برنامهٔ تمرینی هفتگی — هیئت جودو کازرون')
@section('meta_description', 'جدول کامل کلاس‌های جودو در کازرون به تفکیک روز، ردهٔ سنی و جنسیت، همراه با ظرفیت و شهریهٔ هر کلاس.')

@section('content')

    <x-ui.page-header
        eyebrow="تمرین"
        title="برنامهٔ تمرینی هفتگی"
        description="کلاس‌های هیئت جودو کازرون به تفکیک روز و ردهٔ سنی. برای ثبت‌نام، کلاس مناسب خود را انتخاب کنید."
        :breadcrumbs="[['label' => 'برنامهٔ تمرینی']]">

        <x-ui.button :href="route('register')" variant="primary" icon="fa-user-plus">ثبت‌نام آنلاین</x-ui.button>
        <x-ui.button :href="route('coaches.index')" variant="outline" icon="fa-chalkboard-user">معرفی مربیان</x-ui.button>
    </x-ui.page-header>

    <div data-filters class="mx-auto max-w-7xl px-4 py-14 sm:px-6">

        {{-- ======================================================= filters --}}
        <div class="surface-card sticky top-20 z-30 p-5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="flex items-center gap-2 font-bold text-heading">
                    <i class="fa-solid fa-filter text-sm text-brand-text" aria-hidden="true"></i>
                    فیلتر کلاس‌ها
                </h2>

                <button type="button" data-filter-reset
                        class="flex items-center gap-2 text-xs font-semibold text-muted transition hover:text-brand-text">
                    <i class="fa-solid fa-rotate-left text-[0.7rem]" aria-hidden="true"></i>
                    حذف فیلترها
                </button>
            </div>

            {{-- Day --}}
            <div class="mt-5">
                <p class="text-xs font-semibold text-muted" id="filter-day-label">روز هفته</p>

                <div class="mt-2.5 flex flex-wrap gap-2" role="group" aria-labelledby="filter-day-label">
                    <button type="button" data-filter="day" data-value="all" aria-pressed="true"
                            class="rounded-xl border border-brand bg-brand px-4 py-2 text-xs font-semibold text-on-brand transition">
                        همهٔ روزها
                    </button>

                    @foreach ($week as $day)
                        <button type="button" data-filter="day" data-value="{{ $day['index'] }}" aria-pressed="false"
                                class="rounded-xl border border-line bg-surface px-4 py-2 text-xs font-semibold text-copy transition hover:border-line-strong">
                            {{ $day['name'] }}
                            @if ($day['isToday'])
                                <span class="ms-1 text-[0.65rem] text-accent-text">(امروز)</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="filter-age" class="text-xs font-semibold text-muted">ردهٔ سنی</label>
                    <select id="filter-age" data-filter="age"
                            class="mt-2 w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-sm text-heading transition focus:border-brand">
                        <option value="all">همهٔ رده‌ها</option>
                        @foreach ($ageGroups as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter-gender" class="text-xs font-semibold text-muted">جنسیت</label>
                    <select id="filter-gender" data-filter="gender"
                            class="mt-2 w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-sm text-heading transition focus:border-brand">
                        <option value="all">همه</option>
                        @foreach ($genders as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <p data-filter-status role="status" aria-live="polite" class="mt-4 text-xs text-muted"></p>
        </div>

        {{-- ================================================ class cards --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
            @foreach ($classes as $class)
                <x-cards.training-class :training-class="$class" />
            @endforeach
        </div>

        <div data-filter-empty hidden class="mt-8">
            <x-ui.empty-state icon="fa-calendar-xmark"
                              title="کلاسی با این فیلترها یافت نشد"
                              description="فیلترها را تغییر دهید یا برای مشاورهٔ انتخاب کلاس با دفتر هیئت تماس بگیرید.">
                <x-ui.button :href="route('contact')" variant="outline" icon="fa-headset">تماس با هیئت</x-ui.button>
            </x-ui.empty-state>
        </div>

        {{-- =============================================== weekly table --}}
        <section class="mt-16">
            <x-ui.section-heading
                eyebrow="نمای هفتگی"
                title="جدول تمرینات در یک نگاه"
                description="همهٔ جلسات هفته به ترتیب روز و ساعت." />

            <div class="mt-8 overflow-x-auto">
                <div class="grid min-w-[56rem] grid-cols-7 gap-3">
                    @foreach ($week as $day)
                        <div data-filter-group class="flex flex-col">
                            <div @class([
                                'rounded-xl px-3 py-2.5 text-center text-sm font-bold',
                                'bg-brand text-on-brand' => $day['isToday'],
                                'bg-surface-muted text-heading' => ! $day['isToday'],
                            ])>
                                {{ $day['name'] }}
                                @if ($day['isToday'])
                                    <span class="block text-[0.65rem] font-normal opacity-90">امروز</span>
                                @endif
                            </div>

                            <div class="mt-3 flex-1 space-y-2">
                                @forelse ($day['sessions'] as $row)
                                    @php
                                        $session = $row['session'];
                                        $class = $row['class'];
                                    @endphp

                                    <article data-filter-item
                                             data-day="{{ $session->day_of_week }}"
                                             data-age="{{ $class->age_group->value }}"
                                             data-gender="{{ $class->gender->value }}"
                                             class="surface-card p-3">
                                        <p class="text-xs font-bold text-brand-text">{{ $session->time_range }}</p>
                                        <p class="mt-1.5 text-sm leading-snug font-semibold text-heading">{{ $class->title }}</p>
                                        <p class="mt-1 text-[0.7rem] text-muted">{{ $class->coach?->name }}</p>

                                        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-surface-muted">
                                            <div class="h-full rounded-full {{ $class->capacity_tone }}"
                                                 style="width: {{ $class->occupancy_percent }}%"></div>
                                        </div>
                                    </article>
                                @empty
                                    <p class="rounded-xl border border-dashed border-line px-3 py-6 text-center text-xs text-muted">
                                        بدون تمرین
                                    </p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ==================================================== helper --}}
        <section class="mt-16">
            <div class="surface-card grid gap-6 p-8 lg:grid-cols-3">
                @foreach ([
                    ['fa-circle-question', 'کدام کلاس مناسب من است؟', 'ردهٔ سنی و سطح خود را با جدول بالا تطبیق دهید. در صورت تردید، کارشناسان هیئت راهنمایی می‌کنند.'],
                    ['fa-file-shield', 'مدارک لازم چیست؟', 'کارت ملی، عکس پرسنلی و گواهی سلامت پزشکی. فرم‌ها از بخش دانلودها قابل دریافت است.'],
                    ['fa-money-bill-wave', 'شهریه چگونه پرداخت می‌شود؟', 'پرداخت به‌صورت اینترنتی و از طریق درگاه بانکی در پایان فرایند ثبت‌نام انجام می‌شود.'],
                ] as [$icon, $title, $text])
                    <div class="reveal" data-reveal-delay="{{ $loop->index * 90 }}">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-accent-soft text-accent-text">
                            <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                        </span>
                        <h3 class="mt-4 font-bold text-heading">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

@endsection
