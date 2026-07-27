@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'hint' => null,
    'placeholder' => null,
    'value' => null,
    'dir' => null,
    'options' => null,
    'rows' => 4,
    'errorMessage' => null,
])

@php
    $id = 'field-'.$name;
    $errorId = $id.'-error';
    $hintId = $id.'-hint';
    $hasError = $errors->has($name);
    $current = old($name, $value);

    $describedBy = array_filter([$hint ? $hintId : null, $hasError ? $errorId : null]);

    $control = 'w-full rounded-xl border bg-surface px-4 py-3 text-sm text-heading transition
                placeholder:text-muted focus:border-brand
                '.($hasError ? 'border-crimson-600' : 'border-line');
@endphp

<div data-field {{ $attributes->merge(['class' => 'space-y-2']) }}>
    <label for="{{ $id }}" class="block text-sm font-semibold text-heading">
        {{ $label }}
        @if ($required)
            <span class="text-brand-text" aria-hidden="true">*</span>
            <span class="sr-only">(الزامی)</span>
        @endif
    </label>

    @if ($type === 'select')
        <select id="{{ $id }}" name="{{ $name }}"
                @if ($required) required @endif
                @if ($describedBy) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
                @if ($hasError) aria-invalid="true" @endif
                {{ $attributes->except('class') }}
                class="{{ $control }}">
            <option value="">{{ $placeholder ?? 'انتخاب کنید…' }}</option>
            @foreach ($options ?? [] as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>

    @elseif ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
                  @if ($required) required @endif
                  @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                  @if ($describedBy) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
                  @if ($hasError) aria-invalid="true" @endif
                  class="{{ $control }} resize-y">{{ $current }}</textarea>

    @else
        <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ $current }}"
               @if ($required) required @endif
               @if ($placeholder) placeholder="{{ $placeholder }}" @endif
               @if ($dir) dir="{{ $dir }}" @endif
               @if ($describedBy) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
               @if ($hasError) aria-invalid="true" @endif
               @if ($errorMessage) data-error-message="{{ $errorMessage }}" @endif
               {{ $attributes->except('class') }}
               class="{{ $control }}">
    @endif

    @if ($hint)
        <p id="{{ $hintId }}" class="text-xs leading-relaxed text-muted">{{ $hint }}</p>
    @endif

    {{-- Always present so client-side validation has somewhere to write. --}}
    <p id="{{ $errorId }}" data-field-error role="alert"
       class="text-xs font-medium text-brand-text" @unless ($hasError) hidden @endunless>
        {{ $errors->first($name) }}
    </p>
</div>
