@extends('layouts.app')

@section('title', 'مربیان — هیئت جودو کازرون')
@section('meta_description', 'معرفی مربیان رسمی هیئت جودو شهرستان کازرون؛ درجهٔ دان، سوابق، گواهینامه‌ها و تخصص فنی هر مربی.')

@section('content')

    <x-ui.page-header
        eyebrow="کادر فنی"
        title="مربیان هیئت جودو کازرون"
        description="مربیان رسمی فدراسیون جودو با سال‌ها تجربهٔ آموزش، قهرمان‌پروری و داوری."
        :breadcrumbs="[['label' => 'مربیان']]" />

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($coaches as $coach)
                <x-cards.coach :coach="$coach" data-reveal-delay="{{ ($loop->index % 3) * 90 }}" />
            @endforeach
        </div>

        <section class="mt-16">
            <div class="surface-card overflow-hidden">
                <div class="border-b border-line bg-surface-muted/60 px-6 py-5">
                    <h2 class="font-bold text-heading">علاقه‌مند به همکاری با هیئت هستید؟</h2>
                    <p class="mt-1.5 text-sm text-muted">
                        هیئت جودو کازرون از مربیان دارای کارت مربیگری معتبر فدراسیون دعوت به همکاری می‌کند.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 px-6 py-5">
                    <x-ui.button :href="route('contact')" variant="primary" icon="fa-paper-plane">ارسال درخواست همکاری</x-ui.button>
                    <x-ui.button :href="route('downloads')" variant="outline" icon="fa-file-arrow-down">دریافت فرم‌ها</x-ui.button>
                </div>
            </div>
        </section>
    </div>

@endsection
