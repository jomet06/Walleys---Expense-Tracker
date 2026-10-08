@props(['disabled' => false, 'errorBag' => 'default'])

@php
    $fieldName = $attributes->get('name');
    $fieldId = $attributes->get('id', $fieldName);
    $hasError = $fieldName && $errors->getBag($errorBag)->has($fieldName);
    if ($hasError) {
        $descriptions = array_filter(preg_split('/\s+/', (string) $attributes->get('aria-describedby', '')));
        $descriptions[] = $fieldId.'-error';
        $attributes = $attributes->except(['aria-invalid', 'aria-describedby'])->merge([
            'aria-invalid' => 'true',
            'data-server-invalid' => 'true',
            'aria-describedby' => implode(' ', array_unique($descriptions)),
        ]);
    }
@endphp

<input @disabled($disabled) {{ $attributes->merge(['class' => 'ui-field']) }}>
