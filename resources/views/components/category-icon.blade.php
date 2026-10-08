@props(['name' => 'others-icon'])
@php
    $icon = [
        'salary-icon' => 'wallet', 'investments-icon' => 'trend', 'foodbeverage-icon' => 'coffee',
        'food-icon' => 'coffee', 'transport-icon' => 'car', 'entertainment-icon' => 'play',
        'groceries-icon' => 'shopping', 'education-icon' => 'book', 'gifts-icon' => 'gift',
        'travel-icon' => 'plane', 'healthcare-icon' => 'heart', 'briefcase-icon' => 'briefcase',
    ][$name] ?? 'grid';
@endphp
<x-ui-icon :name="$icon" {{ $attributes }} />
