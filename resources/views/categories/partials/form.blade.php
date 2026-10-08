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
    <div data-field>
        <label for="name" class="ui-label">Name</label>
        <input id="name" name="name" type="text" class="ui-field mt-2" value="{{ old('name', $category?->name) }}" required autofocus maxlength="32" placeholder="e.g. Side projects" data-required-message="Enter a category name."
            @if ($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif>
        <x-input-error id="name-error" for="name" class="mt-2" :messages="$errors->get('name')" />
    </div>

    <fieldset data-field @if ($errors->has('type')) data-invalid="true" @endif>
        <legend class="ui-label">Type</legend>
        <div class="mt-2 grid grid-cols-2 gap-3">
            @foreach (['income' => ['Income', 'Money you receive', 'arrow-down'], 'expense' => ['Expense', 'Money you spend', 'arrow-up']] as $value => [$label, $hint, $icon])
                <label class="relative cursor-pointer">
                    <input id="type-{{ $value }}" type="radio" name="type" value="{{ $value }}" class="peer sr-only" required @checked(old('type', $category?->type) === $value)
                        @if ($errors->has('type')) aria-invalid="true" aria-describedby="type-error" @endif>
                    <span @class(['flex h-full items-center gap-3 rounded-xl border border-gray-200 p-3 transition peer-focus-visible:ring-2 peer-focus-visible:ring-offset-2 sm:p-4', 'peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-focus-visible:ring-emerald-500' => $value === 'income', 'peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-focus-visible:ring-rose-500' => $value === 'expense'])>
                        <x-ui-icon :name="$icon" :class="$value === 'income' ? 'h-5 w-5 shrink-0 text-emerald-600' : 'h-5 w-5 shrink-0 text-rose-600'" />
                        <span><span class="block text-sm font-semibold text-gray-800">{{ $label }}</span><span class="mt-1 block text-xs text-gray-500">{{ $hint }}</span></span>
                    </span>
                </label>
            @endforeach
        </div>
        <x-input-error id="type-error" for="type" class="mt-2" :messages="$errors->get('type')" />
    </fieldset>

    <div data-field x-data="{ selectedIcon: @js($selectedIcon ?? '') }">
        <label for="icon" class="ui-label">Icon</label>
        <p id="icon-hint" class="mt-1 text-xs text-gray-500">Choose an icon for your transaction list.</p>
        <div class="mt-3 flex items-center gap-3">
            <span class="flex h-10 w-8 shrink-0 items-center justify-center text-slate-500" aria-hidden="true">
                <span x-show="!selectedIcon"><x-ui-icon name="categories" class="h-5 w-5" /></span>
                @foreach ($icons as $value => $label)
                    <span x-cloak x-show="selectedIcon === $el.dataset.icon" data-icon="{{ $value }}"><x-category-icon :name="$value" class="h-5 w-5" /></span>
                @endforeach
            </span>
            <select id="icon" name="icon" class="ui-field" required x-on:change="selectedIcon = $event.target.value" aria-describedby="icon-hint{{ $errors->has('icon') ? ' icon-error' : '' }}" @if ($errors->has('icon')) aria-invalid="true" @endif>
                <option value="">Choose an icon</option>
                @foreach ($icons as $value => $label)
                    <option value="{{ $value }}" @selected($selectedIcon === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <x-input-error id="icon-error" for="icon" class="mt-2" :messages="$errors->get('icon')" />
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-gray-100 pt-6">
        <a href="{{ route('categories.index') }}" class="ui-button-secondary">Cancel</a>
        <button type="submit" class="ui-button"><x-ui-icon name="check" class="h-4 w-4" /> {{ $submitLabel }}</button>
    </div>
</div>
