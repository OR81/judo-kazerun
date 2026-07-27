@extends('layouts.app')

@section('title', 'درگاه پرداخت آزمایشی')

@push('head')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')

    <div class="mx-auto max-w-lg px-4 py-16 sm:px-6">

        <div role="note"
             class="mb-6 flex items-start gap-3 rounded-card border border-gold-500/40 bg-gold-50 p-4">
            <i class="fa-solid fa-flask mt-0.5 text-gold-700" aria-hidden="true"></i>
            <p class="text-sm leading-relaxed text-gold-900">
                این یک <strong>درگاه آزمایشی</strong> است و هیچ تراکنش واقعی انجام نمی‌شود.
                برای اتصال به درگاه بانکی، مقدار <code class="ltr">PAYMENT_GATEWAY</code> را در فایل
                <code class="ltr">.env</code> روی <code class="ltr">zarinpal</code> بگذارید.
            </p>
        </div>

        <div class="surface-card overflow-hidden">
            {{-- Mimics a bank's payment header. --}}
            <div class="bg-gray-900 px-6 py-5 text-center">
                <p class="text-xs text-gray-400">درگاه پرداخت اینترنتی</p>
                <p class="mt-1 font-bold text-white">{{ setting('site_title') }}</p>
            </div>

            <dl class="divide-y divide-line">
                @foreach ([
                    ['پذیرنده', setting('site_title')],
                    ['بابت', $transaction->enrollment->trainingClass->title],
                    ['کد پیگیری', $transaction->enrollment->reference],
                    ['شمارهٔ موبایل', fa($transaction->enrollment->mobile)],
                ] as [$label, $value])
                    <div class="flex items-center justify-between gap-4 px-6 py-3.5 text-sm">
                        <dt class="text-muted">{{ $label }}</dt>
                        <dd class="text-end font-semibold text-heading">{{ $value }}</dd>
                    </div>
                @endforeach

                <div class="flex items-center justify-between gap-4 bg-surface-muted/50 px-6 py-4">
                    <dt class="font-semibold text-heading">مبلغ قابل پرداخت</dt>
                    <dd class="text-xl font-extrabold text-brand-text">{{ toman($transaction->amount) }}</dd>
                </div>
            </dl>

            <div class="space-y-3 p-6">
                <a href="{{ $callback }}?status=OK&Status=OK&authority={{ $transaction->authority }}"
                   class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-emerald-700">
                    <i class="fa-solid fa-check" aria-hidden="true"></i>
                    تأیید و پرداخت موفق
                </a>

                <a href="{{ $callback }}?status=NOK&Status=NOK&authority={{ $transaction->authority }}"
                   class="flex w-full items-center justify-center gap-2 rounded-xl border border-line px-6 py-3.5 text-sm font-semibold text-copy transition hover:bg-surface-muted">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    انصراف از پرداخت
                </a>
            </div>
        </div>
    </div>

@endsection
