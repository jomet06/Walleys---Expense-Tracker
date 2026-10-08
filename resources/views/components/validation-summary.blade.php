@props(['messages' => [], 'errorBag' => null])

@php
    $entries = [];
    $groupedMessages = $errorBag ? $errorBag->messages() : ['' => (array) $messages];
    foreach ($groupedMessages as $field => $fieldMessages) {
        foreach ((array) $fieldMessages as $message) {
            $entries[] = ['field' => $field, 'message' => $message];
        }
    }
@endphp

<div {{ $attributes->merge(['class' => 'ui-form-summary mb-5']) }} data-validation-summary role="alert" @if (! count($entries)) hidden @endif>
    <p class="font-semibold">Please check the highlighted fields.</p>
    @if (count($entries))
        <ul data-server-summary class="mt-2 space-y-1">
            @foreach ($entries as $entry)
                <li data-server-summary-field="{{ $entry['field'] }}">{{ $entry['message'] }}</li>
            @endforeach
        </ul>
    @endif
    <ul data-client-summary class="mt-2 space-y-1" hidden></ul>
</div>
