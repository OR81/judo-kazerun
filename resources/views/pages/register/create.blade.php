@extends('layouts.app')

@section('title', 'ثبت‌نام آنلاین — هیئت جودو کازرون')
@section('meta_description', 'ثبت‌نام آنلاین کلاس‌های جودو در کازرون؛ انتخاب کلاس، بارگذاری مدارک و پرداخت اینترنتی شهریه.')

@section('content')

    <x-ui.page-header
        eyebrow="ثبت‌نام"
        title="ثبت‌نام آنلاین کلاس‌ها"
        description="در چهار مرحله ثبت‌نام خود را کامل کنید. کل فرایند کمتر از پنج دقیقه زمان می‌برد."
        :breadcrumbs="[['label' => 'ثبت‌نام آنلاین']]" />

    <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6">

        @if ($errors->any())
            <div role="alert" class="surface-card mb-8 border-danger/40 bg-danger-soft p-5">
                <h2 class="flex items-center gap-2 font-bold text-danger-text">
                    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                    لطفاً موارد زیر را اصلاح کنید
                </h2>
                <ul class="mt-3 list-disc space-y-1 ps-5 text-sm text-danger-text">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('registration.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            {{--
                Without JS every step is simply visible and the form still submits
                and validates server-side; wizard.js turns it into steps.
            --}}
            <div data-wizard>

                {{-- ============================================= progress --}}
                <div class="surface-card p-6">
                    <div class="flex items-center justify-between gap-2">
                        @foreach ([
                            ['انتخاب کلاس', 'fa-list-check'],
                            ['اطلاعات فردی', 'fa-user'],
                            ['بارگذاری مدارک', 'fa-file-arrow-up'],
                            ['بازبینی و پرداخت', 'fa-credit-card'],
                        ] as $i => [$label, $icon])
                            <div data-step-indicator data-state="{{ $i === 0 ? 'active' : 'todo' }}"
                                 class="group flex flex-1 flex-col items-center gap-2 text-center">
                                <span class="grid h-11 w-11 place-items-center rounded-full border-2 transition
                                             group-data-[state=active]:border-brand group-data-[state=active]:bg-brand group-data-[state=active]:text-on-brand
                                             group-data-[state=done]:border-emerald-500 group-data-[state=done]:bg-emerald-500 group-data-[state=done]:text-white
                                             group-data-[state=todo]:border-line group-data-[state=todo]:text-muted">
                                    <i class="fa-solid {{ $icon }} text-sm group-data-[state=done]:hidden" aria-hidden="true"></i>
                                    <i class="fa-solid fa-check hidden text-sm group-data-[state=done]:block" aria-hidden="true"></i>
                                </span>

                                <span class="hidden text-xs font-semibold text-muted group-data-[state=active]:text-heading sm:block">
                                    {{ $label }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 h-1.5 overflow-hidden rounded-full bg-surface-muted"
                         role="progressbar" aria-valuemin="1" aria-valuemax="4" aria-valuenow="1"
                         aria-label="پیشرفت ثبت‌نام">
                        <div data-step-progress class="h-full rounded-full bg-brand transition-[inline-size] duration-500"
                             style="inline-size: 25%"></div>
                    </div>

                    <p data-step-status role="status" aria-live="polite" class="mt-3 text-xs text-muted"></p>
                </div>

                {{-- ======================================== step 1: class --}}
                <section data-step data-step-title="انتخاب کلاس" class="surface-card mt-6 p-6 sm:p-8">
                    <h2 data-step-heading class="text-xl font-extrabold text-heading">۱. کلاس مورد نظر را انتخاب کنید</h2>
                    <p class="mt-2 text-sm text-muted">
                        ردهٔ سنی و جنسیت هر کلاس را با شرایط خود تطبیق دهید. کلاس‌های تکمیل‌شده قابل انتخاب نیستند.
                    </p>

                    <div data-field class="mt-6 space-y-3">
                        @foreach ($classes as $class)
                            @php $disabled = $class->is_full; @endphp

                            <label @class([
                                'flex cursor-pointer items-start gap-4 rounded-card border p-4 transition',
                                'border-line hover:border-brand hover:bg-brand-soft/40' => ! $disabled,
                                'cursor-not-allowed border-line opacity-55' => $disabled,
                                'has-checked:border-brand has-checked:bg-brand-soft/60' => ! $disabled,
                            ])>
                                <input type="radio" name="training_class_id" value="{{ $class->id }}"
                                       class="mt-1.5 h-4 w-4 shrink-0 accent-[var(--color-brand)]"
                                       @checked(old('training_class_id', $selected?->id) == $class->id)
                                       @disabled($disabled)
                                       required
                                       data-error-message="لطفاً یک کلاس را انتخاب کنید.">

                                <span class="min-w-0 flex-1">
                                    <span class="flex flex-wrap items-center gap-2">
                                        <span class="font-bold text-heading">{{ $class->title }}</span>
                                        <x-ui.badge variant="neutral">{{ $class->age_group->label() }}</x-ui.badge>
                                        <x-ui.badge variant="neutral">{{ $class->gender->label() }}</x-ui.badge>
                                        @if ($disabled)
                                            <x-ui.badge variant="brand">تکمیل</x-ui.badge>
                                        @endif
                                    </span>

                                    <span class="mt-2 block text-xs text-muted">{{ $class->schedule_summary }}</span>

                                    <span class="mt-1 block text-xs text-muted">
                                        مربی: {{ $class->coach?->name ?? 'کادر فنی' }} · {{ $class->venue }}
                                    </span>

                                    <span class="mt-3 flex items-center gap-3">
                                        <span class="h-1.5 flex-1 overflow-hidden rounded-full bg-surface-muted">
                                            <span class="block h-full rounded-full {{ $class->capacity_tone }}"
                                                  style="width: {{ $class->occupancy_percent }}%"></span>
                                        </span>
                                        <span class="shrink-0 text-[0.7rem] text-muted">
                                            {{ fa($class->remaining_seats) }} جای خالی
                                        </span>
                                    </span>
                                </span>

                                <span class="shrink-0 text-end">
                                    @if ($class->monthly_fee > 0)
                                        <span class="block font-extrabold text-heading">{{ fa_number($class->monthly_fee) }}</span>
                                        <span class="block text-[0.7rem] text-muted">تومان / ماه</span>
                                    @else
                                        <span class="block text-sm font-bold text-emerald-600">رایگان</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach

                        <p data-field-error role="alert" class="text-xs font-medium text-brand-text" hidden></p>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <x-ui.button type="button" data-step-next variant="primary" icon-end="fa-arrow-left">
                            مرحلهٔ بعد
                        </x-ui.button>
                    </div>
                </section>

                {{-- ====================================== step 2: personal --}}
                <section data-step data-step-title="اطلاعات فردی" hidden class="surface-card mt-6 p-6 sm:p-8">
                    <h2 data-step-heading class="text-xl font-extrabold text-heading">۲. اطلاعات فردی</h2>
                    <p class="mt-2 text-sm text-muted">اطلاعات را مطابق کارت ملی وارد کنید.</p>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        <x-ui.field name="first_name" label="نام" required autocomplete="given-name"
                                    error-message="نام را وارد کنید." />

                        <x-ui.field name="last_name" label="نام خانوادگی" required autocomplete="family-name"
                                    error-message="نام خانوادگی را وارد کنید." />

                        <x-ui.field name="national_code" label="کد ملی" required dir="ltr"
                                    placeholder="۱۰ رقم" inputmode="numeric" pattern="[0-9۰-۹]{10}"
                                    error-message="کد ملی باید ۱۰ رقم باشد." />

                        <x-ui.field name="mobile" label="شمارهٔ موبایل" type="tel" required dir="ltr"
                                    placeholder="09123456789" autocomplete="tel"
                                    pattern="0?9[0-9۰-۹]{9}"
                                    error-message="شمارهٔ موبایل معتبر نیست." />

                        <x-ui.field name="birth_date" label="تاریخ تولد" type="date" required dir="ltr"
                                    hint="تاریخ میلادی را از تقویم انتخاب کنید."
                                    error-message="تاریخ تولد را وارد کنید." />

                        <x-ui.field name="gender" label="جنسیت" type="select" required
                                    :options="collect($genders)->except('mixed')->all()"
                                    error-message="جنسیت را انتخاب کنید." />

                        <x-ui.field name="guardian_name" label="نام ولی" class="sm:col-span-2"
                                    hint="برای متقاضیان زیر ۱۸ سال الزامی است." />

                        <x-ui.field name="emergency_phone" label="تلفن تماس اضطراری" type="tel" required dir="ltr"
                                    placeholder="09123456789"
                                    error-message="یک شمارهٔ تماس اضطراری وارد کنید." />

                        <x-ui.field name="email" label="رایانامه (اختیاری)" type="email" dir="ltr"
                                    placeholder="you@example.com" autocomplete="email" />

                        <x-ui.field name="address" label="نشانی" type="textarea" :rows="3" class="sm:col-span-2" />

                        <x-ui.field name="medical_notes" label="سوابق یا محدودیت پزشکی" type="textarea" :rows="3"
                                    class="sm:col-span-2"
                                    hint="در صورت وجود بیماری، حساسیت یا آسیب قبلی حتماً ذکر کنید." />
                    </div>

                    <div class="mt-8 flex flex-wrap justify-between gap-3">
                        <x-ui.button type="button" data-step-back variant="outline" icon="fa-arrow-right">
                            مرحلهٔ قبل
                        </x-ui.button>
                        <x-ui.button type="button" data-step-next variant="primary" icon-end="fa-arrow-left">
                            مرحلهٔ بعد
                        </x-ui.button>
                    </div>
                </section>

                {{-- ===================================== step 3: documents --}}
                <section data-step data-step-title="بارگذاری مدارک" hidden class="surface-card mt-6 p-6 sm:p-8">
                    <h2 data-step-heading class="text-xl font-extrabold text-heading">۳. بارگذاری مدارک</h2>
                    <p class="mt-2 text-sm text-muted">تصاویر باید خوانا باشند. حجم هر فایل نباید از حد مجاز بیشتر شود.</p>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        @foreach ($documentTypes as $type)
                            <div data-field class="rounded-card border border-line p-5">
                                <label for="doc-{{ $type->value }}" class="flex items-start gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-soft text-brand-text">
                                        <i class="fa-solid {{ $type->icon() }}" aria-hidden="true"></i>
                                    </span>

                                    <span class="min-w-0">
                                        <span class="block text-sm font-semibold text-heading">
                                            {{ $type->label() }}
                                            @if ($type->isRequired())
                                                <span class="text-brand-text" aria-hidden="true">*</span>
                                                <span class="sr-only">(الزامی)</span>
                                            @else
                                                <span class="text-xs font-normal text-muted">(اختیاری)</span>
                                            @endif
                                        </span>
                                        <span class="mt-1 block text-xs leading-relaxed text-muted">{{ $type->hint() }}</span>
                                    </span>
                                </label>

                                <input id="doc-{{ $type->value }}" type="file"
                                       name="documents[{{ $type->value }}]"
                                       accept="{{ collect($type->acceptedMimes())->map(fn ($m) => '.'.$m)->implode(',') }}"
                                       @required($type->isRequired())
                                       data-error-message="{{ $type->label() }} را بارگذاری کنید."
                                       class="mt-4 block w-full cursor-pointer rounded-xl border border-line bg-surface text-sm text-copy
                                              file:me-4 file:cursor-pointer file:rounded-e-none file:rounded-s-xl file:border-0
                                              file:bg-surface-muted file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-copy">

                                <div class="mt-3 flex items-center gap-3">
                                    <div data-file-preview class="shrink-0"></div>
                                    <p data-file-name class="min-w-0 flex-1 truncate text-xs text-muted"></p>
                                </div>

                                <p data-field-error role="alert" class="mt-2 text-xs font-medium text-brand-text"
                                   @unless ($errors->has('documents.'.$type->value)) hidden @endunless>
                                    {{ $errors->first('documents.'.$type->value) }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 flex flex-wrap justify-between gap-3">
                        <x-ui.button type="button" data-step-back variant="outline" icon="fa-arrow-right">
                            مرحلهٔ قبل
                        </x-ui.button>
                        <x-ui.button type="button" data-step-next variant="primary" icon-end="fa-arrow-left">
                            بازبینی نهایی
                        </x-ui.button>
                    </div>
                </section>

                {{-- ======================================== step 4: review --}}
                <section data-step data-step-title="بازبینی و پرداخت" hidden class="surface-card mt-6 p-6 sm:p-8">
                    <h2 data-step-heading class="text-xl font-extrabold text-heading">۴. بازبینی و پرداخت</h2>
                    <p class="mt-2 text-sm text-muted">اطلاعات را بررسی کنید و در صورت تأیید، پرداخت را انجام دهید.</p>

                    <dl class="mt-6 divide-y divide-line rounded-card border border-line">
                        @foreach ([
                            'first_name' => 'نام',
                            'last_name' => 'نام خانوادگی',
                            'national_code' => 'کد ملی',
                            'mobile' => 'موبایل',
                            'birth_date' => 'تاریخ تولد',
                            'gender' => 'جنسیت',
                            'guardian_name' => 'نام ولی',
                            'emergency_phone' => 'تماس اضطراری',
                        ] as $field => $label)
                            <div class="flex items-center justify-between gap-4 px-5 py-3.5 text-sm">
                                <dt class="text-muted">{{ $label }}</dt>
                                <dd data-review-for="{{ $field }}" class="font-semibold text-heading">—</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-6 rounded-card border border-line bg-surface-muted p-5">
                        <h3 class="font-bold text-heading">قوانین و مقررات</h3>
                        <ul class="mt-3 list-disc space-y-1.5 ps-5 text-sm leading-relaxed text-muted">
                            <li>حضور منظم در تمرینات و رعایت آیین‌نامهٔ انضباطی هیئت الزامی است.</li>
                            <li>شهریه پس از شروع دوره مسترد نمی‌شود مگر با تأیید کتبی هیئت.</li>
                            <li>ارائهٔ گواهی سلامت پزشکی معتبر برای حضور روی تاتامی ضروری است.</li>
                            <li>مسئولیت صحت اطلاعات واردشده بر عهدهٔ متقاضی است.</li>
                        </ul>
                    </div>

                    <div data-field class="mt-5">
                        <label class="flex cursor-pointer items-start gap-3">
                            <input type="checkbox" name="terms" value="1" required
                                   @checked(old('terms'))
                                   data-error-message="پذیرش قوانین برای ادامه الزامی است."
                                   class="mt-0.5 h-4 w-4 shrink-0 rounded accent-[var(--color-brand)]">
                            <span class="text-sm text-copy">
                                قوانین و مقررات هیئت جودو کازرون را خوانده‌ام و می‌پذیرم.
                                <span class="text-brand-text" aria-hidden="true">*</span>
                            </span>
                        </label>

                        <p data-field-error role="alert" class="mt-2 text-xs font-medium text-brand-text"
                           @unless ($errors->has('terms')) hidden @endunless>
                            {{ $errors->first('terms') }}
                        </p>
                    </div>

                    <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
                        <x-ui.button type="button" data-step-back variant="outline" icon="fa-arrow-right">
                            مرحلهٔ قبل
                        </x-ui.button>

                        <x-ui.button type="submit" variant="primary" size="lg" icon="fa-credit-card">
                            پرداخت و نهایی‌سازی ثبت‌نام
                        </x-ui.button>
                    </div>

                    <p class="mt-4 flex items-center justify-center gap-2 text-xs text-muted">
                        <i class="fa-solid fa-lock text-[0.7rem]" aria-hidden="true"></i>
                        پرداخت از طریق درگاه امن بانکی انجام می‌شود.
                    </p>
                </section>
            </div>
        </form>
    </div>

@endsection
