@extends('layouts.app')

@section('title', 'تأیید کد ورود — هیئت جودو کازرون')

@push('head')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')

    <div class="mx-auto max-w-lg px-4 py-16 sm:px-6">
        <div class="surface-card p-8 sm:p-10">

            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-brand-soft text-brand-text">
                <i class="fa-solid fa-comment-sms text-xl" aria-hidden="true"></i>
            </span>

            <h1 class="mt-6 text-center text-xl font-extrabold text-heading">کد ورود را وارد کنید</h1>

            <p class="mt-2 text-center text-sm leading-relaxed text-muted">
                کد {{ fa($length) }} رقمی به شمارهٔ
                <span class="ltr font-mono font-semibold text-heading">{{ $masked }}</span>
                پیامک شد.
            </p>

            @if (session('status'))
                <p role="status" class="mt-6 rounded-xl bg-open-soft px-4 py-3 text-center text-sm text-open-text">
                    {{ session('status') }}
                </p>
            @endif

            {{-- Local development only: there is no phone to read the code from.
                 See the note on `sms.expose_codes`. --}}
            @if (session('login_code'))
                <p role="status"
                   class="mt-4 rounded-xl border border-accent/40 bg-accent-soft px-4 py-3 text-center text-sm text-accent-text">
                    <span class="font-semibold">کد توسعه:</span>
                    <span class="ltr font-mono text-lg tracking-widest">{{ session('login_code') }}</span>
                </p>
            @endif

            @error('code')
                <p role="alert" class="mt-4 rounded-xl bg-danger-soft px-4 py-3 text-center text-sm text-danger-text">
                    {{ $message }}
                </p>
            @enderror

            <form action="{{ route('login.confirm') }}" method="POST" class="mt-8 space-y-5">
                @csrf

                <div data-field class="space-y-2">
                    <label for="code" class="sr-only">کد ورود</label>

                    <input id="code" type="text" name="code"
                           required autofocus autocomplete="one-time-code"
                           inputmode="numeric" maxlength="{{ $length }}" dir="ltr"
                           placeholder="{{ str_repeat('•', $length) }}"
                           @error('code') aria-invalid="true" @enderror
                           class="w-full rounded-xl border bg-surface px-4 py-4 text-center font-mono text-2xl
                                  tracking-[0.5em] text-heading transition placeholder:text-line-strong
                                  focus:border-brand
                                  @error('code') border-danger @else border-line @enderror">

                    <p class="text-center text-xs text-muted">
                        کد تا {{ fa((int) ceil($ttl / 60)) }} دقیقه معتبر است.
                    </p>
                </div>

                <x-ui.button type="submit" variant="primary" size="lg"
                             icon="fa-right-to-bracket" class="w-full">
                    ورود به پرتال
                </x-ui.button>
            </form>

            <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-6">
                <form action="{{ route('login.resend') }}" method="POST">
                    @csrf
                    <button type="submit"
                            @disabled($cooldown > 0)
                            class="inline-flex items-center gap-2 text-sm font-semibold text-brand-text transition
                                   hover:underline disabled:pointer-events-none disabled:text-muted disabled:no-underline">
                        <i class="fa-solid fa-rotate-right text-xs" aria-hidden="true"></i>
                        @if ($cooldown > 0)
                            ارسال دوباره تا {{ fa($cooldown) }} ثانیهٔ دیگر
                        @else
                            ارسال دوبارهٔ کد
                        @endif
                    </button>
                </form>

                <form action="{{ route('login.change') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-muted transition hover:text-heading hover:underline">
                        تغییر شمارهٔ موبایل
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection
