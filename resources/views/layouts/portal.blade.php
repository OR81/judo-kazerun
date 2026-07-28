{{--
    Portal shell for the athlete and coach dashboards.

    Extends the public layout so members stay inside the same design system
    rather than being dropped into a different-looking admin skin.

    Child views provide: @section('subtitle'), @section('portal-actions'),
    and @section('portal').
--}}
@extends('layouts.app')

@push('head')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')

    @php $user = auth()->user(); @endphp

    <section class="relative overflow-hidden border-b border-line bg-ink">
        <div class="pointer-events-none absolute inset-0 opacity-[0.07]" aria-hidden="true"
             style="background-image:linear-gradient(#fff 1px,transparent 1px),linear-gradient(90deg,#fff 1px,transparent 1px);background-size:56px 56px"></div>
        <div class="pointer-events-none absolute -top-24 inset-inline-start-1/3 h-72 w-72 rounded-full bg-brand/25 blur-3xl" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-7xl px-4 py-10 sm:px-6">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <x-ui.avatar :src="$user->avatar" :name="$user->name" size="lg" ring />

                    <div>
                        <p class="text-xs font-semibold text-accent">{{ $user->role->label() }}</p>
                        <h1 class="mt-1 text-2xl font-extrabold text-white">{{ $user->name }}</h1>
                        <p class="mt-1 text-sm text-on-ink-muted">@yield('subtitle', 'خوش آمدید')</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @yield('portal-actions')

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-white/5 px-5 py-2.5
                                       text-sm font-semibold text-on-ink transition hover:bg-white/15">
                            <i class="fa-solid fa-right-from-bracket text-xs" aria-hidden="true"></i>
                            خروج
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
        @yield('portal')
    </div>

@endsection
