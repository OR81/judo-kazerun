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
                    title="ورود به حساب کاربری"
                    description="بسته به نقش خود وارد شوید. هر پرتال امکانات مخصوص به خود را دارد." />

                <ul class="mt-8 space-y-3">
                    @foreach ([
                        ['fa-user', 'پرتال ورزشکار', 'مشاهدهٔ کلاس‌ها، وضعیت پرداخت، مدارک و کمربند.'],
                        ['fa-chalkboard-user', 'پرتال مربی', 'فهرست هنرجویان، برنامهٔ کلاس‌ها و ثبت‌نام‌های در انتظار.'],
                        ['fa-shield-halved', 'پنل مدیریت', 'مدیریت کامل محتوا، ثبت‌نام‌ها و تنظیمات سایت.'],
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
                            حساب‌های نمونه (محیط توسعه)
                        </p>

                        <table class="mt-3 w-full text-xs">
                            <tbody class="divide-y divide-line">
                                @foreach ([
                                    ['مدیر', '09171234567'],
                                    ['مربی', '09171112233'],
                                    ['ورزشکار', '09173334455'],
                                ] as [$role, $mobile])
                                    <tr>
                                        <td class="py-2 text-muted">{{ $role }}</td>
                                        <td class="ltr py-2 text-end font-mono text-heading">{{ $mobile }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="py-2 text-muted">رمز عبور</td>
                                    <td class="ltr py-2 text-end font-mono text-heading">password</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- =================================================== form --}}
            <div class="lg:col-span-7">
                <div class="surface-card p-8 sm:p-10">
                    <h2 class="text-xl font-extrabold text-heading">ورود</h2>
                    <p class="mt-2 text-sm text-muted">
                        با شمارهٔ موبایل یا رایانامهٔ ثبت‌شده وارد شوید.
                    </p>

                    @if (session('status'))
                        <p role="status" class="mt-6 rounded-xl bg-emerald-500/12 px-4 py-3 text-sm text-emerald-700">
                            {{ session('status') }}
                        </p>
                    @endif

                    @error('email')
                        <p role="alert" class="mt-6 rounded-xl bg-brand-soft px-4 py-3 text-sm text-brand-text">
                            {{ $message }}
                        </p>
                    @enderror

                    <form action="{{ route('login') }}" method="POST" class="mt-8 space-y-5">
                        @csrf

                        <div data-field class="space-y-2">
                            <label for="login" class="block text-sm font-semibold text-heading">
                                شمارهٔ موبایل یا رایانامه
                                <span class="text-brand-text" aria-hidden="true">*</span>
                                <span class="sr-only">(الزامی)</span>
                            </label>

                            <input id="login" type="text" name="email" value="{{ old('email') }}"
                                   required autofocus autocomplete="username" dir="ltr"
                                   placeholder="09123456789"
                                   @error('email') aria-invalid="true" @enderror
                                   class="w-full rounded-xl border bg-surface px-4 py-3 text-sm text-heading transition
                                          placeholder:text-muted focus:border-brand
                                          @error('email') border-crimson-600 @else border-line @enderror">
                        </div>

                        <div data-field class="space-y-2">
                            <div class="flex items-center justify-between gap-3">
                                <label for="password" class="block text-sm font-semibold text-heading">
                                    رمز عبور
                                    <span class="text-brand-text" aria-hidden="true">*</span>
                                    <span class="sr-only">(الزامی)</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}"
                                       class="text-xs font-semibold text-brand-text transition hover:underline">
                                        رمز عبور را فراموش کرده‌اید؟
                                    </a>
                                @endif
                            </div>

                            <input id="password" type="password" name="password" required
                                   autocomplete="current-password" dir="ltr"
                                   @error('password') aria-invalid="true" @enderror
                                   class="w-full rounded-xl border bg-surface px-4 py-3 text-sm text-heading transition
                                          focus:border-brand
                                          @error('password') border-crimson-600 @else border-line @enderror">

                            @error('password')
                                <p role="alert" class="text-xs font-medium text-brand-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <label class="flex cursor-pointer items-center gap-2.5">
                            <input type="checkbox" name="remember"
                                   class="h-4 w-4 rounded accent-[var(--color-brand)]">
                            <span class="text-sm text-copy">مرا به خاطر بسپار</span>
                        </label>

                        <x-ui.button type="submit" variant="primary" size="lg"
                                     icon="fa-right-to-bracket" class="w-full">
                            ورود به پرتال
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
