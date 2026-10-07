@php
    $icons = [
        'salary-icon' => 'Salary',
        'investments-icon' => 'Investments',
        'foodbeverage-icon' => 'Food & Beverages',
        'transport-icon' => 'Transport',
        'entertainment-icon' => 'Entertainment',
        'groceries-icon' => 'Groceries',
        'education-icon' => 'Education',
        'gifts-icon' => 'Gifts',
        'travel-icon' => 'Travel',
        'healthcare-icon' => 'Healthcare',
        'others-icon' => 'Others',
        'briefcase-icon' => 'Briefcase',
    ];
@endphp

<div>
    <x-input-label for="name" :value="__('Name')" />

    <x-text-input
        id="name"
        name="name"
        type="text"
        class="mt-1 block w-full"
        :value="old('name', $category?->name)"
        required
        autofocus
        maxlength="32"
    />

    <x-input-error class="mt-2" :messages="$errors->get('name')" />
</div>

<div class="mt-6">
    <x-input-label for="type" :value="__('Type')" />

    <select
        id="type"
        name="type"
        required
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
    >
        <option value="">Select a category type</option>
        <option value="income" @selected(old('type', $category?->type) === 'income')>
            Income
        </option>
        <option value="expense" @selected(old('type', $category?->type) === 'expense')>
            Expense
        </option>
    </select>

    <x-input-error class="mt-2" :messages="$errors->get('type')" />
</div>

<div class="mt-6">
    <x-input-label for="icon" :value="__('Icon')" />

    <select
        id="icon"
        name="icon"
        required
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
    >
        <option value="">Select an icon</option>

        @foreach ($icons as $value => $label)
            <option value="{{ $value }}" @selected(old('icon', $category?->icon) === $value)>
                {{ $label }}
            </option>
        @endforeach
    </select>

    <x-input-error class="mt-2" :messages="$errors->get('icon')" />
</div>

<div class="mt-6 flex items-center gap-4">
    <x-primary-button>
        {{ $submitLabel }}
    </x-primary-button>

    <a
        href="{{ route('categories.index') }}"
        class="text-sm text-gray-600 underline hover:text-gray-900"
    >
        Cancel
    </a>
</div>
