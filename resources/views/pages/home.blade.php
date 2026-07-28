@extends('layouts.app')

@section('title', setting('hall_name', 'خانهٔ جودو کازرون').' — '.setting('site_title'))
@section('meta_description', 'خانهٔ جودو کازرون؛ سالن اختصاصی هیئت جودو شهرستان. تابلوی سانس‌های هفتگی، اجارهٔ سالن به باشگاه‌ها و گروه‌های ورزشی، و ثبت‌نام در کلاس‌های هیئت.')

@section('content')

    {{-- ============================================================ hero --}}
    <section class="relative overflow-hidden">
        {{-- Two soft washes and the mat weave. Everything decorative sits behind
             aria-hidden overlays so nothing here reaches the accessibility tree. --}}
        <div class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-brand-soft/70 via-canvas to-canvas"
             aria-hidden="true"></div>
        <div class="pointer-events-none absolute inset-0 -z-10 tatami-weave text-brand opacity-[0.045] [--tatami-gap:1.75rem]"
             aria-hidden="true"></div>
        <div class="pointer-events-none absolute -top-32 inset-inline-start-1/4 -z-10 h-96 w-96 rounded-full bg-accent/10 blur-3xl"
             aria-hidden="true"></div>

        <div class="mx-auto max-w-7xl px-4 pt-12 pb-16 sm:px-6 lg:pt-20 lg:pb-24">
            <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-16">

                {{-- ------------------------------------------------ copy --}}
                <div class="lg:col-span-6">
                    <p class="flex items-center gap-2 text-sm font-bold text-brand-text">
                        <span class="h-px w-8 bg-brand" aria-hidden="true"></span>
                        {{ setting('site_title') }}
                    </p>

                    <h1 class="mt-5 text-4xl leading-tight font-extrabold text-balance text-heading sm:text-5xl lg:text-6xl">
                        {{ setting('hall_name', 'خانهٔ جودو کازرون') }}
                    </h1>

                    <p class="mt-5 max-w-xl text-lg leading-relaxed text-copy">
                        {{ setting('hall_intro') }}
                    </p>

                    {{-- Open / closed, decided on the server in the board's own timezone. --}}
                    <p class="mt-7 inline-flex items-center gap-2.5 rounded-full border px-4 py-2 text-sm font-semibold
                              {{ $hours['isOpen'] ? 'border-open/30 bg-open-soft text-open-text' : 'border-line bg-surface-muted text-muted' }}">
                        <span class="relative flex h-2.5 w-2.5" aria-hidden="true">
                            @if ($hours['isOpen'])
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-open opacity-60"></span>
                            @endif
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full {{ $hours['isOpen'] ? 'bg-open' : 'bg-line-strong' }}"></span>
                        </span>

                        @if ($hours['isOpen'])
                            هم‌اکنون باز است — تا ساعت {{ fa($hours['closesAt']) }}
                        @else
                            هم‌اکنون بسته است — بازگشایی ساعت {{ fa($hours['opensAt']) }}
                        @endif
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <x-ui.button href="#hall-board" variant="primary" size="lg" icon="fa-calendar-week">
                            تابلوی سانس‌ها
                        </x-ui.button>

                        <x-ui.button :href="route('register')" variant="outline" size="lg" icon="fa-user-plus">
                            ثبت‌نام در کلاس‌ها
                        </x-ui.button>
                    </div>

                    {{-- Three numbers that answer «چه اندازه است و چه چیزی آزاد است؟». --}}
                    <dl class="mt-10 grid max-w-lg grid-cols-3 gap-6 border-t border-line pt-7">
                        @foreach ([
                            ['fa-vector-square', fa_number($venues->sum('tatami_area')).' م²', 'تاتامی'],
                            ['fa-users', fa_number($venues->sum('capacity')).' نفر', 'ظرفیت هم‌زمان'],
                            ['fa-lock-open', fa($openThisWeek->count()).' سانس', 'آزاد این هفته'],
                        ] as [$icon, $value, $label])
                            <div>
                                <dt class="flex items-center gap-2 text-xs text-muted">
                                    <i class="fa-solid {{ $icon }} text-[0.7rem] text-accent-text" aria-hidden="true"></i>
                                    {{ $label }}
                                </dt>
                                <dd class="mt-1.5 text-lg font-extrabold text-heading sm:text-xl">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                {{-- ----------------------------------------------- photo --}}
                <div class="lg:col-span-6">
                    <div class="relative">
                        <div class="relative overflow-hidden rounded-panel shadow-pop">
                            @if ($hall?->image_url)
                                <img src="{{ $hall->image_url }}" alt="نمای سالن اصلی خانهٔ جودو کازرون"
                                     class="aspect-4/3 w-full object-cover"
                                     fetchpriority="high" decoding="async">
                            @else
                                <div class="grid aspect-4/3 w-full place-items-center bg-surface-muted text-muted">
                                    <i class="fa-solid fa-image text-4xl" aria-hidden="true"></i>
                                </div>
                            @endif

                            {{-- A single warm gradient at the foot, just enough to seat the card. --}}
                            <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-ink/60 to-transparent"
                                 aria-hidden="true"></div>
                        </div>

                        {{-- What is happening on the mat, right now or next. --}}
                        <div class="relative -mt-14 mx-4 rounded-card border border-line bg-surface p-5 shadow-lift sm:mx-8">
                            @if ($currentSlot)
                                <p class="flex items-center gap-2 text-xs font-bold text-open-text">
                                    <span class="h-2 w-2 rounded-full bg-open" aria-hidden="true"></span>
                                    هم‌اکنون روی تاتامی
                                </p>
                                <p class="mt-2 font-bold text-heading">{{ $currentSlot->occupant_label }}</p>
                                <p class="mt-1 text-sm text-muted">
                                    {{ $currentSlot->time_range }} · {{ $currentSlot->venue?->name }}
                                </p>
                            @elseif ($nextSlot)
                                <p class="flex items-center gap-2 text-xs font-bold text-accent-text">
                                    <i class="fa-solid fa-forward text-[0.7rem]" aria-hidden="true"></i>
                                    سانس بعدی امروز
                                </p>
                                <p class="mt-2 font-bold text-heading">{{ $nextSlot->occupant_label }}</p>
                                <p class="mt-1 text-sm text-muted">
                                    {{ $nextSlot->time_range }} · {{ $nextSlot->venue?->name }}
                                </p>
                            @else
                                <p class="flex items-center gap-2 text-xs font-bold text-muted">
                                    <i class="fa-solid fa-mug-hot text-[0.7rem]" aria-hidden="true"></i>
                                    برنامهٔ امروز به پایان رسیده است
                                </p>
                                <p class="mt-2 font-bold text-heading">سالن فردا از ساعت {{ fa($hours['from']) }} باز است</p>
                            @endif

                            <div class="mt-4 flex items-center justify-between gap-3 border-t border-line pt-3">
                                <span class="text-xs text-muted">
                                    {{ $todayLabel }} — {{ fa($openToday->count()) }} سانس آزاد
                                </span>

                                <a href="#hall-board"
                                   class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-text transition hover:underline">
                                    برنامهٔ کامل
                                    <i class="fa-solid fa-arrow-left text-[0.65rem]" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================================== facilities --}}
    <section class="border-y border-line bg-surface" aria-label="امکانات خانهٔ جودو">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <ul class="grid divide-y divide-line sm:grid-cols-2 sm:divide-x sm:divide-y-0 lg:grid-cols-4">
                @foreach ([
                    ['fa-ranking-star', 'تاتامی استاندارد', 'مورد تأیید فدراسیون، برای تمرین و مسابقه'],
                    ['fa-shower', 'رختکن و دوش آب گرم', 'رختکن مجزای بانوان و آقایان'],
                    ['fa-temperature-half', 'گرمایش و تهویه', 'دمای سالن در تمام فصل‌ها کنترل می‌شود'],
                    ['fa-shield-heart', 'ایمنی و کمک‌های اولیه', 'تجهیزات امداد و بیمهٔ ورزشی'],
                ] as [$icon, $title, $text])
                    <li class="reveal flex items-start gap-4 py-7 sm:px-6 lg:py-8"
                        data-reveal-delay="{{ $loop->index * 70 }}">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-soft text-brand-text">
                            <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="font-bold text-heading">{{ $title }}</p>
                            <p class="mt-1 text-xs leading-relaxed text-muted">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ===================================================== hall board --}}
    <section id="hall-board" class="mx-auto max-w-7xl scroll-mt-28 px-4 py-20 sm:px-6">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-ui.section-heading
                eyebrow="تابلوی سانس‌ها"
                title="این هفته روی تاتامی چه خبر است؟"
                description="برنامهٔ هفتگی هر دو سالن خانهٔ جودو: کلاس‌های هیئت، سانس‌های رزروشده، و سانس‌هایی که هنوز آزادند." />

            <x-ui.button href="#rent" variant="outline" icon="fa-file-signature" class="reveal">
                شرایط اجارهٔ سالن
            </x-ui.button>
        </div>

        <x-site.hall-board :week="$week" :venues="$venues" :today-index="$todayIndex" />
    </section>

    {{-- ========================================================== rent --}}
    <section id="rent" class="scroll-mt-28 border-y border-line bg-surface-muted/50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <x-ui.section-heading
                eyebrow="اجارهٔ سالن"
                title="سالن را برای یک سانس یا یک فصل بگیرید"
                :description="setting('hall_rent_note')" />

            {{-- ------------------------------------------------- halls --}}
            <div class="mt-10 grid gap-6 lg:grid-cols-2">
                @foreach ($venues as $venue)
                    @php $free = $openThisWeek->where('venue_id', $venue->id)->count(); @endphp

                    <article class="reveal surface-card flex flex-col overflow-hidden"
                             data-reveal-delay="{{ $loop->index * 90 }}">
                        <div class="relative">
                            @if ($venue->image_url)
                                <img src="{{ $venue->image_url }}" alt="{{ $venue->name }}"
                                     loading="lazy" decoding="async"
                                     class="aspect-21/9 w-full object-cover">
                            @endif

                            <span class="absolute top-4 inset-inline-start-4 inline-flex items-center gap-1.5 rounded-full
                                         bg-surface/90 px-3 py-1 text-xs font-bold text-heading backdrop-blur-sm">
                                <i class="fa-solid fa-vector-square text-[0.7rem] text-brand" aria-hidden="true"></i>
                                {{ fa_number($venue->tatami_area) }} متر مربع تاتامی
                            </span>
                        </div>

                        <div class="flex flex-1 flex-col p-6">
                            <h3 class="text-lg font-extrabold text-heading">{{ $venue->name }}</h3>
                            <p class="mt-1 text-sm text-accent-text">{{ $venue->tagline }}</p>

                            <p class="mt-3 flex-1 text-sm leading-relaxed text-muted">{{ $venue->description }}</p>

                            <ul class="mt-5 flex flex-wrap gap-2">
                                @foreach ($venue->features ?? [] as $feature)
                                    <li class="rounded-lg bg-surface-muted px-2.5 py-1 text-xs text-copy">{{ $feature }}</li>
                                @endforeach
                            </ul>

                            <div class="mt-6 grid grid-cols-3 gap-4 rounded-card bg-surface-muted/70 p-4 text-center">
                                @foreach ([
                                    [fa_number($venue->capacity).' نفر', 'ظرفیت هم‌زمان'],
                                    [$venue->rate_label, 'هر سانس'],
                                    [fa($free).' سانس', 'آزاد این هفته'],
                                ] as [$value, $label])
                                    <div>
                                        <p class="text-sm font-extrabold text-heading">{{ $value }}</p>
                                        <p class="mt-0.5 text-[0.7rem] text-muted">{{ $label }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- ----------------------------------------------- process --}}
            <div class="mt-10 grid gap-6 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <h3 class="reveal text-lg font-extrabold text-heading">اجاره در سه گام</h3>

                    <ol class="mt-6 space-y-4">
                        @foreach ([
                            ['سانس آزاد را انتخاب کنید', 'در تابلوی بالا، سانس‌های سبزرنگ آزادند. روز و ساعت مناسب خود را پیدا کنید.'],
                            ['با دفتر هیئت هماهنگ کنید', 'از دکمهٔ «درخواست رزرو» یا تماس تلفنی؛ سانس تا هماهنگی نهایی برای شما نگه داشته می‌شود.'],
                            ['قرارداد و پرداخت', 'قرارداد کوتاه‌مدت امضا می‌شود و از هفتهٔ بعد سالن در آن ساعت در اختیار شماست.'],
                        ] as $index => [$title, $text])
                            <li class="reveal flex gap-4" data-reveal-delay="{{ $index * 80 }}">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand text-sm font-extrabold text-on-brand">
                                    {{ fa($index + 1) }}
                                </span>
                                <div class="min-w-0 pt-1">
                                    <p class="font-bold text-heading">{{ $title }}</p>
                                    <p class="mt-1 text-sm leading-relaxed text-muted">{{ $text }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>

                    <div class="reveal mt-7 flex flex-wrap items-center gap-3">
                        <x-ui.button :href="route('contact', ['subject' => 'درخواست اجارهٔ سانس خانهٔ جودو'])"
                                     variant="primary" icon="fa-file-signature">
                            ثبت درخواست اجاره
                        </x-ui.button>

                        <a href="tel:{{ setting('mobile') }}"
                           class="inline-flex items-center gap-2 text-sm font-bold text-brand-text transition hover:underline">
                            <i class="fa-solid fa-phone text-xs" aria-hidden="true"></i>
                            <span class="ltr">{{ fa(setting('mobile')) }}</span>
                        </a>
                    </div>
                </div>

                {{-- ------------------------------------------- the rules --}}
                <div class="lg:col-span-5">
                    <div class="reveal surface-card h-full p-6">
                        <h3 class="flex items-center gap-2 font-extrabold text-heading">
                            <i class="fa-solid fa-clipboard-list text-sm text-accent-text" aria-hidden="true"></i>
                            مقررات استفاده از سالن
                        </h3>

                        <ul class="mt-5 space-y-3 text-sm leading-relaxed text-muted">
                            @foreach ([
                                'ورود با کفش روی تاتامی ممنوع است.',
                                'حضور مربی یا سرپرست مسئول در تمام مدت سانس الزامی است.',
                                'بیمهٔ ورزشی افراد بر عهدهٔ گروه اجاره‌کننده است.',
                                'لغو رزرو تا ۲۴ ساعت پیش از سانس بدون جریمه ممکن است.',
                            ] as $rule)
                                <li class="flex gap-3">
                                    <i class="fa-solid fa-circle-check mt-1.5 text-[0.7rem] text-brand" aria-hidden="true"></i>
                                    <span>{{ $rule }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <p class="mt-6 rounded-card bg-accent-soft p-4 text-sm leading-relaxed text-accent-text">
                            رزرو ثابت هفتگی از {{ fa(setting('hall_min_booking', '۴')) }} سانس به بالا،
                            <strong>{{ fa(setting('hall_monthly_discount', '۱۵')) }}٪ تخفیف</strong> دارد.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================================================= classes --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-ui.section-heading
                eyebrow="کلاس‌های هیئت"
                title="آموزش جودو، از کودکان تا پیشکسوتان"
                :description="'هیئت '.fa($classCount).' کلاس در خانهٔ جودو برگزار می‌کند؛ با مربیان رسمی فدراسیون و ثبت‌نام اینترنتی.'" />

            <x-ui.button :href="route('schedule')" variant="outline" icon-end="fa-arrow-left" class="reveal">
                برنامهٔ کامل کلاس‌ها
            </x-ui.button>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-3">
            @foreach ($classes as $class)
                <x-cards.training-class :training-class="$class" data-reveal-delay="{{ $loop->index * 80 }}" />
            @endforeach
        </div>
    </section>

    {{-- ======================================================= coaches --}}
    <section class="border-y border-line bg-surface py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="flex flex-wrap items-center justify-between gap-8">
                <div class="reveal">
                    <p class="text-sm font-bold text-brand-text">کادر فنی</p>
                    <p class="mt-1 text-lg font-extrabold text-heading">مربیان رسمی هیئت جودو کازرون</p>
                </div>

                <div class="flex flex-wrap items-center gap-6">
                    <ul class="flex flex-wrap items-center gap-5">
                        @foreach ($coaches as $coach)
                            <li class="reveal" data-reveal-delay="{{ $loop->index * 50 }}">
                                <a href="{{ route('coaches.show', $coach) }}" class="group flex items-center gap-3">
                                    <x-ui.avatar :src="$coach->photo_url" :name="$coach->name" size="sm" />
                                    <span class="min-w-0">
                                        <span class="block text-sm font-semibold text-heading transition group-hover:text-brand-text">
                                            {{ $coach->name }}
                                        </span>
                                        <span class="block text-xs text-muted">{{ $coach->dan_label }}</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <x-ui.button :href="route('coaches.index')" variant="ghost" size="sm" icon-end="fa-arrow-left" class="reveal">
                        همهٔ مربیان
                    </x-ui.button>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================ news & events --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6">
        <div class="grid gap-12 lg:grid-cols-12">

            <div class="lg:col-span-7">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-ui.section-heading eyebrow="تازه‌ها" title="اخبار هیئت" />

                    <x-ui.button :href="route('news.index')" variant="ghost" size="sm" icon-end="fa-arrow-left" class="reveal">
                        آرشیو اخبار
                    </x-ui.button>
                </div>

                <div class="mt-8 grid gap-6 sm:grid-cols-2">
                    @foreach ($latestNews as $item)
                        <x-cards.news :item="$item" data-reveal-delay="{{ $loop->index * 80 }}" />
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-ui.section-heading eyebrow="تقویم" title="رویدادهای پیش‌رو" />

                    <x-ui.button :href="route('events.index')" variant="ghost" size="sm" icon-end="fa-arrow-left" class="reveal">
                        تقویم کامل
                    </x-ui.button>
                </div>

                <div class="mt-8 space-y-4">
                    @forelse ($upcomingEvents as $event)
                        <x-cards.event :event="$event" data-reveal-delay="{{ $loop->index * 70 }}" />
                    @empty
                        <x-ui.empty-state icon="fa-calendar-xmark"
                                          title="رویداد پیش‌رویی ثبت نشده"
                                          description="به‌زودی برنامهٔ مسابقات و آزمون‌های جدید اعلام می‌شود." />
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- ======================================================= gallery --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-ui.section-heading
                eyebrow="گالری"
                title="خانهٔ جودو در قاب تصویر"
                description="تصاویر مسابقات، اردوها و تمرین‌های روزانه روی تاتامی." />

            <x-ui.button :href="route('gallery')" variant="outline" icon-end="fa-arrow-left" class="reveal">
                گالری کامل
            </x-ui.button>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($albums as $album)
                <a href="{{ route('gallery.show', $album) }}"
                   class="reveal group relative aspect-4/3 overflow-hidden rounded-panel"
                   data-reveal-delay="{{ $loop->index * 80 }}">
                    <img src="{{ $album->cover_url }}" alt="" loading="lazy" decoding="async"
                         class="h-full w-full object-cover transition duration-500 group-hover:scale-105">

                    <div class="absolute inset-0 bg-gradient-to-t from-ink/85 to-transparent" aria-hidden="true"></div>

                    <div class="absolute inset-x-0 bottom-0 p-5">
                        <x-ui.badge variant="ink" :icon="$album->type->icon()">
                            {{ fa($album->items->count()) }} {{ $album->type->label() }}
                        </x-ui.badge>
                        <h3 class="mt-3 font-bold text-white">{{ $album->title }}</h3>
                        <p class="mt-1 text-xs text-on-ink">{{ shamsi($album->taken_on) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ================================================== honours + CTA --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6">
        <div class="reveal relative overflow-hidden rounded-panel bg-ink px-6 py-14 sm:px-12 lg:py-16">
            <div class="pointer-events-none absolute inset-0 tatami-weave text-white opacity-[0.06] [--tatami-gap:3rem]"
                 aria-hidden="true"></div>
            <div class="pointer-events-none absolute -top-24 inset-inline-end-0 h-72 w-72 rounded-full bg-brand/40 blur-3xl"
                 aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-28 inset-inline-start-0 h-72 w-72 rounded-full bg-accent/20 blur-3xl"
                 aria-hidden="true"></div>

            <div class="relative grid gap-10 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-7">
                    <p class="flex items-center gap-2 text-sm font-bold text-accent">
                        <span class="h-px w-8 bg-accent" aria-hidden="true"></span>
                        کارنامهٔ هیئت
                    </p>

                    <h2 class="mt-4 text-2xl font-extrabold text-balance text-white sm:text-3xl lg:text-4xl">
                        از سال {{ fa(setting('founded_year')) }}، روی همین تاتامی
                    </h2>

                    <p class="mt-4 max-w-xl leading-relaxed text-on-ink">
                        {{ setting('about_short') }}
                    </p>

                    <dl class="mt-8 grid grid-cols-2 gap-6 sm:grid-cols-4">
                        @foreach ($stats as $stat)
                            <div>
                                <dt class="sr-only">{{ $stat['label'] }}</dt>
                                <dd>
                                    <span class="block text-2xl font-extrabold text-white sm:text-3xl">{{ $stat['value'] }}</span>
                                    <span class="mt-1 block text-xs text-on-ink-muted">{{ $stat['label'] }}</span>
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <div class="lg:col-span-5">
                    <p class="text-sm font-semibold text-on-ink-muted">چهره‌های مدال‌آور</p>

                    <ul class="mt-4 flex flex-wrap gap-3">
                        @foreach ($champions as $athlete)
                            <li>
                                <a href="{{ route('athletes.show', $athlete) }}"
                                   class="flex items-center gap-3 rounded-xl border border-white/15 bg-white/5 p-2.5 pe-4
                                          transition hover:border-accent/50 hover:bg-white/10">
                                    <x-ui.avatar :src="$athlete->photo_url" :name="$athlete->name" size="sm" />
                                    <span class="min-w-0">
                                        <span class="block text-sm font-semibold text-white">{{ $athlete->name }}</span>
                                        <span class="block text-xs text-on-ink-muted">{{ $athlete->weight_class }}</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-7 flex flex-col gap-3 sm:flex-row lg:flex-col">
                        <x-ui.button :href="route('register')" variant="primary" size="lg" icon="fa-user-plus" class="flex-1">
                            ثبت‌نام آنلاین در کلاس‌ها
                        </x-ui.button>

                        <x-ui.button href="#hall-board" variant="accent" size="lg" icon="fa-calendar-week" class="flex-1">
                            رزرو سانس سالن
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ====================================================== sponsors --}}
    <section class="border-t border-line py-14" aria-label="حامیان هیئت">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <p class="reveal text-center text-sm font-semibold text-muted">با حمایت و همکاری</p>

            <ul class="mt-8 flex flex-wrap items-center justify-center gap-x-10 gap-y-8">
                @foreach ($sponsors as $sponsor)
                    <li class="reveal" data-reveal-delay="{{ $loop->index * 60 }}">
                        @if ($sponsor->url)
                            <a href="{{ $sponsor->url }}" target="_blank" rel="noopener noreferrer"
                               class="flex flex-col items-center gap-2 opacity-70 grayscale transition hover:opacity-100 hover:grayscale-0">
                                <img src="{{ $sponsor->logo_url }}" alt="{{ $sponsor->name }}"
                                     loading="lazy" class="h-14 w-14 rounded-xl object-contain">
                                <span class="max-w-32 text-center text-xs text-muted">{{ $sponsor->name }}</span>
                            </a>
                        @else
                            <div class="flex flex-col items-center gap-2 opacity-70 grayscale transition hover:opacity-100 hover:grayscale-0">
                                <img src="{{ $sponsor->logo_url }}" alt="{{ $sponsor->name }}"
                                     loading="lazy" class="h-14 w-14 rounded-xl object-contain">
                                <span class="max-w-32 text-center text-xs text-muted">{{ $sponsor->name }}</span>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

@endsection
