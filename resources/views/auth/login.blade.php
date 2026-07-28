@extends('layouts.app')

@section('title', 'ورود به پرتال — هیئت جودو کازرون')

@push('head')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')

    <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <div class="grid gap-10 lg:grid-cols-12">

            {{-- ============================================ portal chooser --}}
            <div class="lg:col-span-5">
                <x-ui.section-heading
                    eyebrow="پرتال اعضا"
                    title="ورود با شمارهٔ موبایل"
                    description="رمز عبوری در کار نیست: شماره‌تان را وارد کنید تا کد ورود پیامک شود." />

                <ul class="mt-8 space-y-3">
                    @foreach ([
                        ['fa-user', 'پرتال ورزشکار', 'مشاهدهٔ کلاس‌ها، وضعیت پرداخت، مدارک و کمربند.'],
                        ['fa-chalkboard-user', 'پرتال مربی', 'فهرست هنرجویان، برنامهٔ کلاس‌ها و ثبت‌نام‌های در انتظار.'],
                        ['fa-shield-halved', 'پنل مدیریت', 'مدیریت محتوا، سانس‌های سالن و تنظیمات سایت.'],
                    ] as [$icon, $title, $description])
                        <li class="surface-card flex items-start gap-4 p-5">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-soft text-brand-text">
                                <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                            </span>
                            <span>
                                <span class="block font-bold text-heading">{{ $title }}</span>
                                <span class="mt-1 block text-sm leading-relaxed text-muted">{{ $description }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>

                @if (app()->isLocal())
                    <div class="surface-card mt-6 p-5">
                        <p class="flex items-center gap-2 text-sm font-bold text-heading">
                            <i class="fa-solid fa-key text-xs text-accent-text" aria-hidden="true"></i>
                            شماره‌های نمونه (محیط توسعه)
                        </p>

                        <table class="mt-3 w-full text-xs">
                            <tbody class="divide-y divide-line">
                                @foreach ([
                                    ['مدیر', '09171234567'],
                                    ['مربی', '09171112233'],
                                    ['ورزشکار', '09173334455'],
                                ] as [$role, $sample])
                                    <tr>
                                        <td class="py-2 text-muted">{{ $role }}</td>
                                        <td class="ltr py-2 text-end font-mono text-heading">{{ $sample }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <p class="mt-3 text-xs leading-relaxed text-muted">
                            با درایور <code class="ltr">log</code> پیامکی ارسال نمی‌شود؛ کد ورود در صفحهٔ بعد
                            نشان داده می‌شود و در <code class="ltr">storage/logs</code> هم ثبت می‌ماند.
                        </p>
                    </div>
                @endif
            </div>

            {{-- =================================================== form --}}
            <div class="lg:col-span-7">
                <div class="surface-card p-8 sm:p-10">
                    <h2 class="text-xl font-extrabold text-heading">ورود</h2>
                    <p class="mt-2 text-sm text-muted">
                        شمارهٔ موبایلی را که هنگام ثبت‌نام اعلام کرده‌اید وارد کنید.
                    </p>

                    @if (session('status'))
                        <p role="status" class="mt-6 rounded-xl bg-open-soft px-4 py-3 text-sm text-open-text">
                            {{ session('status') }}
                        </p>
                    @endif

                    @error('mobile')
                        <p role="alert" class="mt-6 rounded-xl bg-danger-soft px-4 py-3 text-sm text-danger-text">
                            {{ $message }}
                        </p>
                    @enderror

                    <form action="{{ route('login.store') }}" method="POST" class="mt-8 space-y-5">
                        @csrf

                        <div data-field class="space-y-2">
                            <label for="mobile" class="block text-sm font-semibold text-heading">
                                شمارهٔ موبایل
                                <span class="text-brand-text" aria-hidden="true">*</span>
                                <span class="sr-only">(الزامی)</span>
                            </label>

                            <input id="mobile" type="tel" name="mobile" value="{{ old('mobile') }}"
                                   required autofocus autocomplete="tel" dir="ltr"
                                   inputmode="numeric" maxlength="13"
                                   placeholder="09123456789"
                                   aria-describedby="mobile-hint"
                                   @error('mobile') aria-invalid="true" @enderror
                                   class="w-full rounded-xl border bg-surface px-4 py-3 text-center font-mono text-lg
                                          tracking-widest text-heading transition placeholder:text-muted
                                          placeholder:tracking-normal focus:border-brand
                                          @error('mobile') border-danger @else border-line @enderror">

                            <p id="mobile-hint" class="text-xs leading-relaxed text-muted">
                                کد ورود به همین شماره پیامک می‌شود.
                            </p>
                        </div>

                        <label class="flex cursor-pointer items-center gap-2.5">
                            <input type="checkbox" name="remember" value="1"
                                   class="h-4 w-4 rounded accent-[var(--color-brand)]">
                            <span class="text-sm text-copy">مرا به خاطر بسپار</span>
                        </label>

                        <x-ui.button type="submit" variant="primary" size="lg"
                                     icon="fa-comment-sms" class="w-full">
                            ارسال کد ورود
                        </x-ui.button>
                    </form>

                    <div class="mt-8 border-t border-line pt-6 text-center">
                        <p class="text-sm text-muted">
                            هنوز عضو نشده‌اید؟
                            <a href="{{ route('register') }}" class="font-semibold text-brand-text transition hover:underline">
                                ثبت‌نام در کلاس‌های جودو
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
