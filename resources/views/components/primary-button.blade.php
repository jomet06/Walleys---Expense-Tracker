<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ui-button']) }}>
    {{ $slot }}
</button>
