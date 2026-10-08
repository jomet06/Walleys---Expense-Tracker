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
    $selectedIcon = old('icon', $category?->icon);
    if ($selectedIcon && ! array_key_exists($selectedIcon, $icons)) {
        $icons[$selectedIcon] = 'Current icon';
    }
@endphp

<div class="space-y-6">
    <div>
        <label for="name" class="ui-label">Name</label>
        <input id="name" name="name" type="text" class="ui-field mt-2" value="{{ old('name', $category?->name) }}" required autofocus maxlength="32" placeholder="e.g. Side projects"
            @if ($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif>
        <x-input-error id="name-error" class="mt-2" :messages="$errors->get('name')" />
    </div>

    <fieldset>
        <legend class="ui-label">Type</legend>
        <div class="mt-2 grid grid-cols-2 gap-3">
            @foreach (['income' => ['Income', 'Money you receive', 'arrow-down'], 'expense' => ['Expense', 'Money you spend', 'arrow-up']] as $value => [$label, $hint, $icon])
                <label class="relative cursor-pointer">
                    <input type="radio" name="type" value="{{ $value }}" class="peer sr-only" required @checked(old('type', $category?->type) === $value)
                        @if ($errors->has('type')) aria-invalid="true" aria-describedby="type-error" @endif>
                    <span class="flex h-full items-center gap-3 rounded-xl border border-gray-200 p-3 transition peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500 peer-focus-visible:ring-offset-2 sm:p-4">
                        <x-ui-icon :name="$icon" class="h-5 w-5 shrink-0 text-brand-600" />
                        <span><span class="block text-sm font-semibold text-gray-800">{{ $label }}</span><span class="mt-1 block text-xs text-gray-500">{{ $hint }}</span></span>
                    </span>
                </label>
            @endforeach
        </div>
        <x-input-error id="type-error" class="mt-2" :messages="$errors->get('type')" />
    </fieldset>

    <fieldset>
        <legend class="ui-label">Icon</legend>
        <p class="mt-1 text-xs text-gray-500">Choose an icon for your transaction list.</p>
        <div class="mt-3 grid grid-cols-3 gap-2 sm:grid-cols-4">
            @foreach ($icons as $value => $label)
                <label class="relative cursor-pointer">
                    <input type="radio" name="icon" value="{{ $value }}" class="peer sr-only" required @checked($selectedIcon === $value)
                        @if ($errors->has('icon')) aria-invalid="true" aria-describedby="icon-error" @endif>
                    <span class="flex h-full min-h-[90px] flex-col items-center justify-center gap-2 rounded-xl border border-gray-200 p-3 text-gray-500 transition hover:border-brand-200 peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500 peer-focus-visible:ring-offset-2">
                        <x-category-icon :name="$value" class="h-5 w-5" />
                        <span class="text-center text-xs font-medium">{{ $label }}</span>
                    </span>
                </label>
            @endforeach
        </div>
        <x-input-error id="icon-error" class="mt-2" :messages="$errors->get('icon')" />
    </fieldset>

    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-gray-100 pt-6">
        <a href="{{ route('categories.index') }}" class="ui-button-secondary">Cancel</a>
        <button type="submit" class="ui-button"><x-ui-icon name="check" class="h-4 w-4" /> {{ $submitLabel }}</button>
    </div>
</div>
