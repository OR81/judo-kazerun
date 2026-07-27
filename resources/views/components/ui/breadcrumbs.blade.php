@props(['items' => []])

{{-- $items: [['label' => '…', 'url' => '…'|null], …]. The last entry is the current page. --}}
<nav aria-label="مسیر صفحه" {{ $attributes }}>
    <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted">
        <li>
            <a href="{{ route('home') }}" class="transition hover:text-brand-text">
                <i class="fa-solid fa-house text-xs" aria-hidden="true"></i>
                <span class="sr-only">خانه</span>
            </a>
        </li>

        @foreach ($items as $item)
            @php $isLast = $loop->last; @endphp

            <li class="flex items-center gap-2">
                {{-- Chevron points left: that's "forward" in an RTL trail. --}}
                <i class="fa-solid fa-chevron-left text-[0.6rem] opacity-50" aria-hidden="true"></i>

                @if ($isLast || empty($item['url']))
                    <span @if ($isLast) aria-current="page" @endif class="font-medium text-copy">
                        {{ $item['label'] }}
                    </span>
                @else
                    <a href="{{ $item['url'] }}" class="transition hover:text-brand-text">{{ $item['label'] }}</a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
