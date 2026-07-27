@extends('layouts.app')

@section('title', 'فرم‌ها، آیین‌نامه‌ها و جزوات — هیئت جودو کازرون')
@section('meta_description', 'دریافت فرم‌های ثبت‌نام، آیین‌نامه‌های فنی و انضباطی و جزوات آموزشی هیئت جودو شهرستان کازرون.')

@section('content')

    <x-ui.page-header
        eyebrow="اسناد"
        title="فرم‌ها و دانلودها"
        description="فرم‌های ثبت‌نام، آیین‌نامه‌های رسمی و منابع آموزشی مورد نیاز ورزشکاران و مربیان."
        :breadcrumbs="[['label' => 'دانلودها']]" />

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">

        {{-- Category jump links --}}
        <nav aria-label="دسته‌بندی فایل‌ها" class="flex flex-wrap gap-2">
            @foreach ($categories as $category)
                @continue (! isset($groups[$category->value]))

                <a href="#{{ $category->value }}"
                   class="flex items-center gap-2 rounded-xl border border-line bg-surface px-5 py-2.5 text-sm font-semibold text-copy transition hover:border-brand hover:bg-brand-soft hover:text-brand-text">
                    <i class="fa-solid {{ $category->icon() }} text-xs" aria-hidden="true"></i>
                    {{ $category->label() }}
                    <span class="opacity-70">({{ fa($groups[$category->value]->count()) }})</span>
                </a>
            @endforeach
        </nav>

        <div class="mt-10 space-y-12">
            @foreach ($categories as $category)
                @continue (! isset($groups[$category->value]))

                <section id="{{ $category->value }}" class="scroll-mt-28">
                    <div class="flex items-start gap-4">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-brand text-on-brand">
                            <i class="fa-solid {{ $category->icon() }}" aria-hidden="true"></i>
                        </span>

                        <div>
                            <h2 class="text-xl font-extrabold text-heading">{{ $category->label() }}</h2>
                            <p class="mt-1 text-sm text-muted">{{ $category->description() }}</p>
                        </div>
                    </div>

                    <ul class="mt-6 grid gap-4 lg:grid-cols-2">
                        @foreach ($groups[$category->value] as $file)
                            <li class="reveal surface-card flex flex-wrap items-center gap-4 p-5"
                                data-reveal-delay="{{ ($loop->index % 2) * 80 }}">

                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-surface-muted text-muted">
                                    <i class="fa-solid {{ $file->icon }} text-lg" aria-hidden="true"></i>
                                </span>

                                <div class="min-w-0 flex-1">
                                    <h3 class="font-bold text-heading">{{ $file->title }}</h3>
                                    <p class="mt-1 line-clamp-2 text-sm leading-relaxed text-muted">{{ $file->description }}</p>

                                    <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-muted">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-file text-[0.7rem]" aria-hidden="true"></i>
                                            <span class="uppercase">{{ $file->extension }}</span>
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-weight-hanging text-[0.7rem]" aria-hidden="true"></i>
                                            {{ $file->human_size }}
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-download text-[0.7rem]" aria-hidden="true"></i>
                                            {{ fa_number($file->downloads_count) }} دانلود
                                        </span>
                                    </p>
                                </div>

                                <x-ui.button :href="route('downloads.file', $file)" variant="soft"
                                             size="sm" icon="fa-download" class="shrink-0">
                                    دریافت
                                </x-ui.button>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </div>

        <div class="surface-card mt-14 flex flex-wrap items-center justify-between gap-4 p-6">
            <div>
                <h2 class="font-bold text-heading">فایل مورد نظرتان را پیدا نکردید؟</h2>
                <p class="mt-1.5 text-sm text-muted">
                    با دبیرخانهٔ هیئت تماس بگیرید تا فرم یا سند مورد نیاز برایتان ارسال شود.
                </p>
            </div>

            <x-ui.button :href="route('contact')" variant="primary" icon="fa-headset">تماس با دبیرخانه</x-ui.button>
        </div>
    </div>

@endsection
