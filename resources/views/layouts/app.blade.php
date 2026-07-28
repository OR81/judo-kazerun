<!DOCTYPE html>
<html lang="fa" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', setting('site_title', 'هیئت جودو کازرون'))</title>
    <meta name="description" content="@yield('meta_description', setting('site_description'))">
    <meta name="theme-color" content="#141f4a">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ setting('site_title') }}">
    <meta property="og:title" content="@yield('title', setting('site_title'))">
    <meta property="og:description" content="@yield('meta_description', setting('site_description'))">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    {{--
        Marks the document as scripted before first paint, so .reveal only hides
        itself when JS is actually there to bring it back.
    --}}
    <script>document.documentElement.classList.add('js');</script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="min-h-dvh bg-canvas font-sans text-copy antialiased">
    <a href="#main"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:inset-inline-start-4 focus:z-[100]
              focus:rounded-xl focus:bg-brand focus:px-5 focus:py-3 focus:text-on-brand focus:shadow-pop">
        پرش به محتوای اصلی
    </a>

    <x-site.header />

    <main id="main" class="min-h-[60vh]">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <x-site.footer />

    <x-site.search-overlay />
    <x-site.back-to-top />
    <x-site.whatsapp />

    {{-- Server-side flash messages, picked up by toast.js on load. --}}
    @foreach (['success' => 'success', 'error' => 'error', 'warning' => 'warning', 'status' => 'info'] as $key => $type)
        @if (session($key))
            <template data-toast-seed data-type="{{ $type }}" data-message="{{ session($key) }}"></template>
        @endif
    @endforeach

    @if ($errors->any() && ! session('error'))
        <template data-toast-seed data-type="error"
                  data-message="{{ __('لطفاً خطاهای فرم را بررسی کنید.') }}"></template>
    @endif

    @stack('scripts')
</body>
</html>
