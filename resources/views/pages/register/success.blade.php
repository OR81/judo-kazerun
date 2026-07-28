@extends('layouts.app')

@section('title', 'رسید ثبت‌نام — هیئت جودو کازرون')

@push('head')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')

    <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6">

        {{-- ==================================================== status --}}
        <div class="text-center">
            @if ($isPaid)
                <span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-emerald-500/12 text-emerald-600">
                    <i class="fa-solid fa-circle-check text-4xl" aria-hidden="true"></i>
                </span>

                <h1 class="mt-6 text-2xl font-extrabold text-heading sm:text-3xl">ثبت‌نام شما با موفقیت انجام شد</h1>
                <p class="mt-3 leading-relaxed text-muted">
                    پرداخت تأیید شد و ثبت‌نام شما در سامانه ثبت گردید. کد پیگیری را نزد خود نگه دارید.
                </p>
            @else
                <span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-accent-soft text-accent-text">
                    <i class="fa-solid fa-hourglass-half text-4xl" aria-hidden="true"></i>
                </span>

                <h1 class="mt-6 text-2xl font-extrabold text-heading sm:text-3xl">ثبت‌نام ثبت شد — در انتظار تکمیل</h1>
                <p class="mt-3 leading-relaxed text-muted">
                    درخواست شما ثبت شده اما پرداخت نهایی نشده است. می‌توانید با کد پیگیری، از دفتر هیئت پیگیری کنید.
                </p>
            @endif
        </div>

        {{-- =============================================== reference --}}
        <div class="surface-card mt-10 overflow-hidden">
            <div class="border-b border-line bg-surface-muted/60 px-6 py-5 text-center">
                <p class="text-xs font-semibold text-muted">کد پیگیری ثبت‌نام</p>
                <p class="ltr mt-2 font-mono text-2xl font-extrabold tracking-widest text-heading">
                    {{ $enrollment->reference }}
                </p>
            </div>

            <dl class="divide-y divide-line">
                @foreach (array_filter([
                    ['نام متقاضی', $enrollment->full_name],
                    ['کد ملی', fa($enrollment->national_code)],
                    ['شمارهٔ تماس', fa($enrollment->mobile)],
                    ['کلاس انتخابی', $enrollment->trainingClass->title],
                    ['مربی', $enrollment->trainingClass->coach?->name],
                    ['زمان تمرین', $enrollment->trainingClass->schedule_summary],
                    ['محل برگزاری', $enrollment->trainingClass->venue],
                    ['تاریخ ثبت‌نام', shamsi($enrollment->created_at, 'datetime')],
                ]) as [$label, $value])
                    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 text-sm">
                        <dt class="text-muted">{{ $label }}</dt>
                        <dd class="text-end font-semibold text-heading">{{ $value }}</dd>
                    </div>
                @endforeach

                <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 text-sm">
                    <dt class="text-muted">وضعیت</dt>
                    <dd>
                        <x-ui.badge :variant="$enrollment->status->badgeClass()">
                            {{ $enrollment->status->label() }}
                        </x-ui.badge>
                    </dd>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 bg-surface-muted/40 px-6 py-4">
                    <dt class="font-semibold text-heading">مبلغ</dt>
                    <dd class="text-lg font-extrabold text-heading">
                        {{ $enrollment->amount > 0 ? toman($enrollment->amount) : 'رایگان' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- ============================================== transaction --}}
        @if ($transaction)
            <div class="surface-card mt-6 p-6">
                <h2 class="flex items-center gap-2 font-bold text-heading">
                    <i class="fa-solid fa-receipt text-sm text-brand-text" aria-hidden="true"></i>
                    اطلاعات تراکنش
                </h2>

                <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                    @foreach (array_filter([
                        ['وضعیت پرداخت', $transaction->status->label()],
                        ['درگاه', strtoupper($transaction->gateway)],
                        $transaction->ref_id ? ['شمارهٔ پیگیری بانکی', fa($transaction->ref_id)] : null,
                        $transaction->card_pan ? ['کارت پرداخت', $transaction->masked_pan] : null,
                        $transaction->paid_at ? ['زمان پرداخت', shamsi($transaction->paid_at, 'datetime')] : null,
                    ]) as [$label, $value])
                        <div class="rounded-xl bg-surface-muted px-4 py-3">
                            <dt class="text-xs text-muted">{{ $label }}</dt>
                            <dd class="ltr mt-1 text-sm font-semibold text-heading">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>

                @if ($transaction->message)
                    <p class="mt-4 rounded-xl bg-brand-soft px-4 py-3 text-sm text-brand-text">
                        {{ $transaction->message }}
                    </p>
                @endif
            </div>
        @endif

        {{-- =================================================== next --}}
        <div class="surface-card mt-6 p-6">
            <h2 class="font-bold text-heading">مراحل بعدی</h2>

            <ol class="mt-4 space-y-4">
                @foreach ([
                    'مدارک بارگذاری‌شدهٔ شما توسط کارشناسان هیئت بررسی می‌شود.',
                    'نتیجهٔ بررسی از طریق پیامک به شمارهٔ ثبت‌شده اطلاع داده می‌شود.',
                    'در اولین جلسهٔ تمرین، اصل مدارک را همراه داشته باشید.',
                ] as $i => $step)
                    <li class="flex gap-3">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-brand-soft text-xs font-bold text-brand-text">
                            {{ fa($i + 1) }}
                        </span>
                        <span class="text-sm leading-relaxed text-copy">{{ $step }}</span>
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <x-ui.button variant="ink" icon="fa-print" onclick="window.print()">چاپ رسید</x-ui.button>
            <x-ui.button :href="route('schedule')" variant="outline" icon="fa-calendar-days">برنامهٔ تمرینی</x-ui.button>
            <x-ui.button :href="route('home')" variant="ghost" icon="fa-house">بازگشت به خانه</x-ui.button>
        </div>

        <p class="mt-6 text-center text-sm text-muted">
            سؤالی دارید؟ با دفتر هیئت تماس بگیرید:
            <a href="tel:{{ setting('phone') }}" class="ltr font-semibold text-brand-text hover:underline">
                {{ fa(setting('phone')) }}
            </a>
        </p>
    </div>

@endsection
