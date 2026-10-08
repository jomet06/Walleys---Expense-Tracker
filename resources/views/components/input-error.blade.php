@props(['messages', 'for' => null])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-rose-700 space-y-1']) }} data-server-error @if ($for) data-error-for="{{ $for }}" @endif>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
