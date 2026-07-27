@extends('layouts.portal')

@section('title', 'پرتال ورزشکار — هیئت جودو کازرون')
@section('subtitle', 'پرتال ورزشکار — کلاس‌ها، پرداخت‌ها و مدارک شما')

@section('portal-actions')
    <x-ui.button :href="route('schedule')" variant="accent" size="md" icon="fa-calendar-days">برنامهٔ تمرینی</x-ui.button>
@endsection

@section('portal')

    <div class="grid gap-8 lg:grid-cols-12">

        {{-- ================================================== main --}}
        <div class="space-y-8 lg:col-span-8">

            {{-- Active class --}}
            <section>
                <h2 class="text-lg font-extrabold text-heading">کلاس فعال شما</h2>

                @if ($activeClass)
                    <div class="surface-card mt-4 p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-heading">{{ $activeClass->title }}</h3>
                                <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-muted">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-user-tie text-xs" aria-hidden="true"></i>
                                        {{ $activeClass->coach?->name }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-location-dot text-xs" aria-hidden="true"></i>
                                        {{ $activeClass->venue }}
                                    </span>
                                </p>
                            </div>

                            <x-ui.badge variant="success" icon="fa-circle-check">فعال</x-ui.badge>
                        </div>

                        <ul class="mt-5 grid gap-2 sm:grid-cols-2">
                            @foreach ($activeClass->sessions as $session)
                                <li class="flex items-center justify-between gap-3 rounded-xl bg-surface-muted px-4 py-3 text-sm">
                                    <span class="font-semibold text-heading">{{ $session->day_name }}</span>
                                    <span class="text-muted">{{ $session->time_range }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <x-ui.empty-state class="mt-4" icon="fa-calendar-plus"
                                      title="کلاس فعالی ندارید"
                                      description="برای شرکت در تمرینات، در یکی از کلاس‌های هیئت ثبت‌نام کنید.">
                        <x-ui.button :href="route('register')" variant="primary" icon="fa-user-plus">ثبت‌نام در کلاس</x-ui.button>
                    </x-ui.empty-state>
                @endif
            </section>

            {{-- Enrollments --}}
            <section>
                <h2 class="text-lg font-extrabold text-heading">تاریخچهٔ ثبت‌نام‌ها</h2>

                @if ($enrollments->isEmpty())
                    <x-ui.empty-state class="mt-4" icon="fa-inbox" title="ثبت‌نامی ثبت نشده است" />
                @else
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full min-w-[38rem] border-collapse text-sm">
                            <caption class="sr-only">فهرست ثبت‌نام‌های شما</caption>
                            <thead>
                                <tr class="border-b border-line text-xs text-muted">
                                    <th scope="col" class="px-4 py-3 text-start font-semibold">کد پیگیری</th>
                                    <th scope="col" class="px-4 py-3 text-start font-semibold">کلاس</th>
                                    <th scope="col" class="px-4 py-3 text-start font-semibold">تاریخ</th>
                                    <th scope="col" class="px-4 py-3 text-start font-semibold">مبلغ</th>
                                    <th scope="col" class="px-4 py-3 text-start font-semibold">وضعیت</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line">
                                @foreach ($enrollments as $enrollment)
                                    <tr class="transition hover:bg-surface-muted/60">
                                        <td class="px-4 py-3">
                                            <a href="{{ route('registration.success', $enrollment) }}"
                                               class="ltr font-mono text-xs font-semibold text-brand-text hover:underline">
                                                {{ $enrollment->reference }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-copy">{{ $enrollment->trainingClass->title }}</td>
                                        <td class="px-4 py-3 text-muted">{{ shamsi($enrollment->created_at) }}</td>
                                        <td class="px-4 py-3 font-semibold text-heading">
                                            {{ $enrollment->amount > 0 ? fa_number($enrollment->amount) : 'رایگان' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <x-ui.badge :variant="$enrollment->status->badgeClass()">
                                                {{ $enrollment->status->label() }}
                                            </x-ui.badge>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            {{-- Payments --}}
            @if ($payments->isNotEmpty())
                <section>
                    <h2 class="text-lg font-extrabold text-heading">پرداخت‌ها</h2>

                    <div class="mt-4 space-y-3">
                        @foreach ($payments as $payment)
                            <div class="surface-card flex flex-wrap items-center justify-between gap-4 p-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-surface-muted text-muted">
                                        <i class="fa-solid fa-receipt text-sm" aria-hidden="true"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-heading">{{ toman($payment->amount) }}</p>
                                        <p class="mt-0.5 text-xs text-muted">
                                            {{ $payment->paid_at ? shamsi($payment->paid_at, 'datetime') : 'پرداخت نشده' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    @if ($payment->ref_id)
                                        <span class="ltr text-xs text-muted">کد رهگیری: {{ fa($payment->ref_id) }}</span>
                                    @endif
                                    <x-ui.badge :variant="$payment->status->badgeClass()">
                                        {{ $payment->status->label() }}
                                    </x-ui.badge>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- ================================================ sidebar --}}
        <aside class="space-y-6 lg:col-span-4">

            {{-- Belt --}}
            <div class="surface-card p-6">
                <h2 class="font-bold text-heading">وضعیت ورزشی</h2>

                @if ($profile)
                    <div class="mt-4 flex items-center gap-3 rounded-xl bg-surface-muted p-4">
                        <span class="h-10 w-10 shrink-0 rounded-full ring-2 ring-line"
                              style="background-color: {{ $profile->belt?->color ?? '#94a3b8' }}" aria-hidden="true"></span>
                        <div>
                            <p class="text-xs text-muted">کمربند فعلی</p>
                            <p class="mt-0.5 font-bold text-heading">{{ $profile->belt?->name ?? 'ثبت نشده' }}</p>
                        </div>
                    </div>

                    <dl class="mt-4 space-y-3 text-sm">
                        @foreach (array_filter([
                            'وزن' => $profile->weight_class,
                            'باشگاه' => $profile->club,
                            'مربی' => $profile->coach?->name,
                        ]) as $label => $value)
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-muted">{{ $label }}</dt>
                                <dd class="font-semibold text-heading">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    @if ($profile->achievements->isNotEmpty())
                        <div class="mt-5 border-t border-line pt-5">
                            <p class="text-xs font-semibold text-muted">افتخارات</p>
                            <ul class="mt-3 space-y-2">
                                @foreach ($profile->achievements->take(4) as $achievement)
                                    <li class="flex items-center gap-2.5 text-sm">
                                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-gradient-to-br {{ $achievement->rank->ringClass() }}">
                                            <i class="fa-solid fa-medal text-[0.6rem]" aria-hidden="true"></i>
                                        </span>
                                        <span class="min-w-0 flex-1 truncate text-copy">{{ $achievement->competition }}</span>
                                        <span class="shrink-0 text-xs text-muted">{{ fa($achievement->year) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @else
                    <p class="mt-3 text-sm leading-relaxed text-muted">
                        پروندهٔ ورزشی شما هنوز توسط هیئت تکمیل نشده است. پس از تأیید ثبت‌نام، اطلاعات کمربند و
                        سوابق در همین بخش نمایش داده می‌شود.
                    </p>
                @endif
            </div>

            {{-- Documents --}}
            <div class="surface-card p-6">
                <h2 class="font-bold text-heading">مدارک بارگذاری‌شده</h2>

                @php $documents = $enrollments->flatMap->documents; @endphp

                @if ($documents->isEmpty())
                    <p class="mt-3 text-sm text-muted">مدرکی بارگذاری نشده است.</p>
                @else
                    <ul class="mt-4 space-y-2">
                        @foreach ($documents->unique('type') as $document)
                            <li class="flex items-center gap-3 rounded-xl bg-surface-muted px-4 py-3">
                                <i class="fa-solid {{ $document->type->icon() }} text-sm text-muted" aria-hidden="true"></i>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-semibold text-heading">{{ $document->type->label() }}</span>
                                    <span class="block text-xs text-muted">{{ $document->human_size }}</span>
                                </span>
                                <i class="fa-solid fa-circle-check text-sm text-emerald-500" aria-hidden="true"
                                   title="بارگذاری شده"></i>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Profile --}}
            <div class="surface-card p-6">
                <h2 class="font-bold text-heading">اطلاعات حساب</h2>

                <dl class="mt-4 space-y-3 text-sm">
                    @foreach (array_filter([
                        'موبایل' => fa($user->mobile),
                        'کد ملی' => $user->national_code ? fa($user->national_code) : null,
                        'رایانامه' => $user->email,
                        'شهر' => $user->city,
                    ]) as $label => $value)
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-muted">{{ $label }}</dt>
                            <dd class="ltr truncate font-semibold text-heading">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>

                <x-ui.button :href="route('contact')" variant="outline" size="sm"
                             icon="fa-pen" class="mt-5 w-full">
                    درخواست ویرایش اطلاعات
                </x-ui.button>
            </div>
        </aside>
    </div>

@endsection
