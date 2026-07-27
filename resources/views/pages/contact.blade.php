@extends('layouts.app')

@section('title', 'تماس با ما — هیئت جودو کازرون')
@section('meta_description', 'نشانی، شمارهٔ تماس، ساعات کاری و فرم ارتباط با هیئت جودو شهرستان کازرون.')

@section('content')

    <x-ui.page-header
        eyebrow="ارتباط"
        title="تماس با هیئت"
        description="برای مشاورهٔ ثبت‌نام، پیگیری امور اداری یا همکاری با هیئت، از راه‌های زیر با ما در ارتباط باشید."
        :breadcrumbs="[['label' => 'تماس با ما']]" />

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">

        {{-- ================================================ quick cards --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['fa-location-dot', 'نشانی دفتر', setting('address'), null],
                ['fa-phone', 'تلفن ثابت', fa(setting('phone')), 'tel:'.setting('phone')],
                ['fa-mobile-screen', 'همراه و واتس‌اپ', fa(setting('mobile')), 'tel:'.setting('mobile')],
                ['fa-envelope', 'رایانامه', setting('email'), 'mailto:'.setting('email')],
            ] as [$icon, $label, $value, $href])
                <div class="reveal surface-card p-6" data-reveal-delay="{{ $loop->index * 80 }}">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-brand-soft text-brand-text">
                        <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                    </span>

                    <p class="mt-4 text-xs font-semibold text-muted">{{ $label }}</p>

                    @if ($href)
                        <a href="{{ $href }}" class="mt-1.5 block text-sm leading-relaxed font-semibold text-heading transition hover:text-brand-text">
                            <span @class(['ltr' => str_starts_with($href, 'tel:') || str_starts_with($href, 'mailto:')])>{{ $value }}</span>
                        </a>
                    @else
                        <p class="mt-1.5 text-sm leading-relaxed font-semibold text-heading">{{ $value }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-10 grid gap-10 lg:grid-cols-12">

            {{-- =================================================== form --}}
            <div class="lg:col-span-7">
                <div class="surface-card p-8">
                    <h2 class="text-xl font-extrabold text-heading">ارسال پیام</h2>
                    <p class="mt-2 text-sm text-muted">
                        فرم زیر را تکمیل کنید؛ همکاران ما در نخستین فرصت اداری پاسخ می‌دهند.
                    </p>

                    <form action="{{ route('contact.store') }}" method="POST" class="mt-8 space-y-5" novalidate>
                        @csrf

                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-ui.field name="name" label="نام و نام خانوادگی" required
                                        placeholder="مثلاً علی رضایی" autocomplete="name" />

                            <x-ui.field name="phone" label="شمارهٔ تماس" type="tel" dir="ltr"
                                        placeholder="09123456789" autocomplete="tel"
                                        hint="برای پاسخ سریع‌تر، شمارهٔ همراه خود را وارد کنید." />
                        </div>

                        <x-ui.field name="email" label="رایانامه" type="email" dir="ltr"
                                    placeholder="you@example.com" autocomplete="email" />

                        <x-ui.field name="subject" label="موضوع" required
                                    placeholder="مثلاً شرایط ثبت‌نام ردهٔ کودکان" />

                        <x-ui.field name="message" label="متن پیام" type="textarea" required :rows="6"
                                    placeholder="پیام خود را بنویسید…" />

                        <div class="flex flex-wrap items-center gap-4 pt-2">
                            <x-ui.button type="submit" variant="primary" size="lg" icon="fa-paper-plane">
                                ارسال پیام
                            </x-ui.button>

                            <p class="text-xs text-muted">
                                اطلاعات شما تنها برای پاسخ‌گویی استفاده می‌شود.
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================================================ sidebar --}}
            <aside class="space-y-6 lg:col-span-5">

                {{-- Map --}}
                <div class="surface-card overflow-hidden">
                    <h2 class="border-b border-line px-6 py-4 font-bold text-heading">موقعیت روی نقشه</h2>

                    {{--
                        Static placeholder rather than an embedded Google Map: the site
                        must stay usable without third-party scripts, and Google Maps is
                        not reliably reachable from Iran. Swap the inner block for an
                        iframe (or a Neshan/Balad embed) whenever you want a live map.
                    --}}
                    <div class="relative aspect-4/3 bg-surface-muted">
                        <div class="absolute inset-0 opacity-40" aria-hidden="true"
                             style="background-image:linear-gradient(var(--color-line) 1px,transparent 1px),linear-gradient(90deg,var(--color-line) 1px,transparent 1px);background-size:32px 32px"></div>

                        <div class="absolute inset-0 grid place-items-center p-6 text-center">
                            <div>
                                <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-brand text-on-brand shadow-pop">
                                    <i class="fa-solid fa-location-dot text-xl" aria-hidden="true"></i>
                                </span>

                                <p class="mt-4 font-bold text-heading">خانهٔ جودو کازرون</p>
                                <p class="mt-1.5 text-sm leading-relaxed text-muted">{{ setting('address') }}</p>

                                <p class="mt-3 text-xs text-muted">
                                    <span class="ltr">{{ setting('map_lat') }}, {{ setting('map_lng') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <x-ui.button
                            href="https://www.google.com/maps/search/?api=1&query={{ setting('map_lat') }},{{ setting('map_lng') }}"
                            target="_blank" rel="noopener noreferrer"
                            variant="outline" size="sm" icon="fa-diamond-turn-right" class="w-full">
                            مسیریابی
                        </x-ui.button>
                    </div>
                </div>

                {{-- Hours --}}
                <div class="surface-card p-6">
                    <h2 class="flex items-center gap-2 font-bold text-heading">
                        <i class="fa-solid fa-clock text-sm text-brand-text" aria-hidden="true"></i>
                        ساعات کاری دفتر
                    </h2>

                    <ul class="mt-4 space-y-3 text-sm">
                        @foreach ([
                            ['شنبه تا چهارشنبه', setting('hours_weekdays')],
                            ['پنج‌شنبه', setting('hours_thursday')],
                            ['جمعه', setting('hours_friday')],
                        ] as [$day, $hours])
                            <li class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-0 last:pb-0">
                                <span class="shrink-0 font-semibold text-heading">{{ $day }}</span>
                                <span class="text-end text-muted">{{ \Illuminate\Support\Str::after($hours, ': ') }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <p class="mt-4 rounded-xl bg-surface-muted p-3 text-xs leading-relaxed text-muted">
                        {{ setting('hours_note') }}
                    </p>
                </div>

                {{-- Social --}}
                <div class="surface-card p-6">
                    <h2 class="font-bold text-heading">شبکه‌های اجتماعی</h2>

                    <div class="mt-4 grid grid-cols-3 gap-3">
                        @foreach ([
                            ['اینستاگرام', setting('instagram'), 'instagram'],
                            ['تلگرام', setting('telegram'), 'telegram'],
                            ['واتس‌اپ', 'https://wa.me/'.setting('whatsapp'), 'whatsapp'],
                        ] as [$label, $url, $icon])
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                               class="flex flex-col items-center gap-2 rounded-xl border border-line p-4 text-muted transition hover:border-brand hover:bg-brand-soft hover:text-brand-text">
                                <x-dynamic-component :component="'icons.'.$icon" class="h-5 w-5" />
                                <span class="text-xs font-semibold">{{ $label }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </div>

@endsection
