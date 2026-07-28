@extends('layouts.app')

@section('title', 'هیئت‌رئیسه و کمیته‌ها — هیئت جودو کازرون')
@section('meta_description', 'معرفی رئیس، نایب‌رئیس، دبیر، خزانه‌دار و کمیته‌های تخصصی هیئت جودو شهرستان کازرون.')

@section('content')

    <x-ui.page-header
        eyebrow="ساختار هیئت"
        title="هیئت‌رئیسه و کمیته‌ها"
        description="اعضای هیئت‌رئیسه و مسئولان کمیته‌های تخصصی هیئت جودو شهرستان کازرون."
        :breadcrumbs="[['label' => 'هیئت‌رئیسه']]" />

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">

        {{-- ================================================== officers --}}
        <section>
            <x-ui.section-heading
                eyebrow="ارکان هیئت"
                title="اعضای هیئت‌رئیسه"
                description="مسئولیت راهبری، برنامه‌ریزی و نظارت بر فعالیت‌های جودوی شهرستان." />

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($officers as $member)
                    <article class="reveal surface-card overflow-hidden text-center transition hover:shadow-lift"
                             data-reveal-delay="{{ $loop->index * 90 }}">
                        <div class="bg-gradient-to-b from-ink to-ink-soft px-6 pt-8 pb-6">
                            <x-ui.avatar :src="$member->photo_url" :name="$member->name" size="xl" ring class="mx-auto" />

                            <h3 class="mt-4 text-lg font-bold text-white">{{ $member->name }}</h3>

                            <p class="mt-2">
                                <span class="inline-flex rounded-full bg-accent px-3 py-1 text-xs font-bold text-on-accent">
                                    {{ $member->position->label() }}
                                </span>
                            </p>
                        </div>

                        <div class="p-5">
                            <p class="line-clamp-3 text-sm leading-relaxed text-muted">{{ $member->summary }}</p>

                            @if ($member->email)
                                <a href="mailto:{{ $member->email }}"
                                   class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-brand-text transition hover:underline">
                                    <i class="fa-solid fa-envelope text-[0.7rem]" aria-hidden="true"></i>
                                    ارتباط با {{ $member->position->label() }}
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- ================================================ committees --}}
        <section class="mt-20">
            <x-ui.section-heading
                eyebrow="کمیته‌های تخصصی"
                title="کمیته‌ها و اعضای آن‌ها"
                description="هر کمیته مسئولیت بخشی از فعالیت‌های فنی، آموزشی و اجرایی هیئت را بر عهده دارد." />

            <div class="mt-10 space-y-6">
                @foreach ($committees as $committee => $members)
                    <div class="reveal surface-card overflow-hidden">
                        <div class="flex items-center gap-3 border-b border-line bg-surface-muted/60 px-6 py-4">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand text-on-brand">
                                <i class="fa-solid fa-people-group text-sm" aria-hidden="true"></i>
                            </span>
                            <h3 class="font-bold text-heading">{{ $committee }}</h3>
                            <span class="ms-auto text-xs text-muted">{{ fa($members->count()) }} عضو</span>
                        </div>

                        <ul class="divide-y divide-line">
                            @foreach ($members as $member)
                                <li class="flex flex-wrap items-center gap-4 px-6 py-4">
                                    <x-ui.avatar :src="$member->photo_url" :name="$member->name" size="sm" />

                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-heading">{{ $member->name }}</p>
                                        <p class="mt-0.5 text-xs text-muted">{{ $member->summary }}</p>
                                    </div>

                                    <x-ui.badge variant="neutral">{{ $member->position->label() }}</x-ui.badge>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ===================================================== about --}}
        <section class="mt-20">
            <div class="surface-card grid gap-8 p-8 lg:grid-cols-2 lg:p-12">
                <div class="reveal">
                    <h2 class="text-2xl font-extrabold text-heading">دربارهٔ هیئت</h2>
                    <p class="mt-4 leading-loose text-copy">{{ setting('about_short') }}</p>
                    <p class="mt-4 leading-loose text-copy">{{ setting('about_mission') }}</p>
                </div>

                <div class="reveal grid grid-cols-2 gap-4 self-start" data-reveal-delay="120">
                    @foreach ([
                        ['fa-calendar-star', 'سال تأسیس', setting('founded_year')],
                        ['fa-users', 'ورزشکار فعال', setting('stat_athletes')],
                        ['fa-building', 'باشگاه تحت پوشش', setting('stat_clubs')],
                        ['fa-medal', 'مدال کسب‌شده', setting('stat_medals')],
                    ] as [$icon, $label, $value])
                        <div class="rounded-card bg-surface-muted p-5 text-center">
                            <span class="mx-auto grid h-10 w-10 place-items-center rounded-xl bg-brand-soft text-brand-text">
                                <i class="fa-solid {{ $icon }} text-sm" aria-hidden="true"></i>
                            </span>
                            <p class="mt-3 text-2xl font-extrabold text-heading">{{ fa($value) }}</p>
                            <p class="mt-1 text-xs text-muted">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

@endsection
