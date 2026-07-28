<footer class="mt-24 border-t border-line bg-ink text-on-ink">
    <x-site.newsletter />

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
        <div class="grid gap-10 lg:grid-cols-12">
            {{-- Identity --}}
            <div class="lg:col-span-4">
                <a href="{{ route('home') }}" class="group flex items-center gap-3">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-white/5">
                        <svg viewBox="0 0 32 32" class="h-7 w-7" role="img" aria-label="نشان هیئت جودو کازرون">
                            <circle cx="16" cy="16" r="15" fill="none" stroke="#F59E0B" stroke-width="1.5" opacity=".55" />
                            <path d="M16 4 L25 12 L16 20 L7 12 Z" fill="#DC2626" />
                            <path d="M16 12 L25 20 L16 28 L7 20 Z" fill="#F59E0B" opacity=".92" />
                            <circle cx="16" cy="16" r="2.6" fill="#fff" />
                        </svg>
                    </span>
                    <span>
                        <span class="block font-extrabold text-white">هیئت جودو کازرون</span>
                        <span class="mt-0.5 block text-xs text-on-ink-muted">{{ setting('site_tagline') }}</span>
                    </span>
                </a>

                <p class="mt-5 text-sm leading-relaxed text-on-ink-muted">
                    {{ setting('about_short') }}
                </p>

                <div class="mt-6 flex items-center gap-3">
                    <a href="{{ setting('instagram') }}" target="_blank" rel="noopener noreferrer"
                       class="grid h-10 w-10 place-items-center rounded-xl bg-white/5 transition hover:bg-brand hover:text-white"
                       aria-label="اینستاگرام هیئت جودو کازرون">
                        <x-icons.instagram class="h-4 w-4" />
                    </a>
                    <a href="{{ setting('telegram') }}" target="_blank" rel="noopener noreferrer"
                       class="grid h-10 w-10 place-items-center rounded-xl bg-white/5 transition hover:bg-brand hover:text-white"
                       aria-label="کانال تلگرام">
                        <x-icons.telegram class="h-4 w-4" />
                    </a>
                    <a href="{{ setting('aparat') }}" target="_blank" rel="noopener noreferrer"
                       class="grid h-10 w-10 place-items-center rounded-xl bg-white/5 transition hover:bg-brand hover:text-white"
                       aria-label="کانال آپارات">
                        <i class="fa-solid fa-play text-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            {{-- Link columns --}}
            <div class="grid gap-8 sm:grid-cols-3 lg:col-span-5">
                <div>
                    <h2 class="text-sm font-bold text-white">دسترسی سریع</h2>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        @foreach ([
                            ['برنامهٔ تمرینی', 'schedule'],
                            ['ثبت‌نام آنلاین', 'register'],
                            ['مربیان', 'coaches.index'],
                            ['ورزشکاران', 'athletes.index'],
                        ] as [$label, $route])
                            <li>
                                <a href="{{ route($route) }}" class="text-on-ink-muted transition hover:text-accent">{{ $label }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h2 class="text-sm font-bold text-white">هیئت</h2>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        @foreach ([
                            ['هیئت‌رئیسه', 'board'],
                            ['اخبار', 'news.index'],
                            ['رویدادها', 'events.index'],
                            ['گالری', 'gallery'],
                        ] as [$label, $route])
                            <li>
                                <a href="{{ route($route) }}" class="text-on-ink-muted transition hover:text-accent">{{ $label }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h2 class="text-sm font-bold text-white">خدمات</h2>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        @foreach ([
                            ['فرم‌ها و آیین‌نامه‌ها', 'downloads'],
                            ['تماس با ما', 'contact'],
                            ['ورود به پرتال', 'login'],
                        ] as [$label, $route])
                            <li>
                                <a href="{{ route($route) }}" class="text-on-ink-muted transition hover:text-accent">{{ $label }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Contact --}}
            <div class="lg:col-span-3">
                <h2 class="text-sm font-bold text-white">اطلاعات تماس</h2>
                <ul class="mt-4 space-y-4 text-sm text-on-ink-muted">
                    <li class="flex gap-3">
                        <i class="fa-solid fa-location-dot mt-1 shrink-0 text-accent" aria-hidden="true"></i>
                        <span class="leading-relaxed">{{ setting('address') }}</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-phone mt-1 shrink-0 text-accent" aria-hidden="true"></i>
                        <a href="tel:{{ setting('phone') }}" class="ltr transition hover:text-accent">
                            {{ fa(setting('phone')) }}
                        </a>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-envelope mt-1 shrink-0 text-accent" aria-hidden="true"></i>
                        <a href="mailto:{{ setting('email') }}" class="ltr transition hover:text-accent">
                            {{ setting('email') }}
                        </a>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-clock mt-1 shrink-0 text-accent" aria-hidden="true"></i>
                        <span class="leading-relaxed">
                            {{ setting('hours_weekdays') }}<br>
                            {{ setting('hours_thursday') }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 text-xs text-on-ink-muted/75 sm:flex-row">
            <p>
                © {{ shamsi(now(), 'year') }} — کلیهٔ حقوق برای
                <span class="text-on-ink-muted">هیئت جودو شهرستان کازرون</span>
                محفوظ است.
            </p>
            <p class="flex items-center gap-2">
                <span>زیر نظر</span>
                <span class="text-on-ink-muted">{{ setting('province_board') }}</span>
                <span aria-hidden="true">·</span>
                <span class="text-on-ink-muted">{{ setting('federation') }}</span>
            </p>
        </div>
    </div>
</footer>
