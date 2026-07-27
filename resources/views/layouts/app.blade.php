<!DOCTYPE html>
<html lang="fa" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', setting('site_title', 'هیئت جودو کازرون'))</title>
    <meta name="description" content="@yield('meta_description', setting('site_description'))">
    <meta name="theme-color" content="#111827">

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
        Applied before first paint so the correct theme is painted on frame one
        and there's no white flash on a dark-mode reload. Mirrors resources/js/theme.js.
        The `js` class also lets .reveal hide itself only when JS is actually present.
    --}}
    <script>
        (function () {
            var root = document.documentElement;
            root.classList.add('js');
            try {
                var stored = localStorage.getItem('judo-theme');
                var dark = stored === 'dark' ||
                    (stored !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                root.classList.toggle('dark', dark);
                root.style.colorScheme = dark ? 'dark' : 'light';
            } catch (e) {
                /* private mode — fall back to the light theme */
            }
        })();
    </script>

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

    {{-- Live region for theme changes and other status announcements. --}}
    <p id="theme-status" class="sr-only" role="status" aria-live="polite"></p>

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
