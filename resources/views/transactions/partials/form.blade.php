<div>
    <x-input-label for="title" :value="__('Title')" />

    <x-text-input
        id="title"
        name="title"
        type="text"
        class="mt-1 block w-full"
        :value="old('title', $transaction?->title)"
        required
        autofocus
        maxlength="50"
    />

    <x-input-error class="mt-2" :messages="$errors->get('title')" />
</div>

<div class="mt-6">
    <x-input-label for="category_id" :value="__('Category')" />

    <select
        id="category_id"
        name="category_id"
        required
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
    >
        <option value="">Select a category</option>

        <optgroup label="Income">
            @foreach ($categories->where('type', 'income') as $category)
                <option
                    value="{{ $category->id }}"
                    @selected((string) old('category_id', $transaction?->category_id) === (string) $category->id)
                >
                    {{ $category->name }}
                    {{ $category->user_id === null ? '(Default)' : '(Custom)' }}
                </option>
            @endforeach
        </optgroup>

        <optgroup label="Expense">
            @foreach ($categories->where('type', 'expense') as $category)
                <option
                    value="{{ $category->id }}"
                    @selected((string) old('category_id', $transaction?->category_id) === (string) $category->id)
                >
                    {{ $category->name }}
                    {{ $category->user_id === null ? '(Default)' : '(Custom)' }}
                </option>
            @endforeach
        </optgroup>
    </select>

    <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
</div>

<div class="mt-6">
    <x-input-label for="amount" :value="__('Amount')" />

    <x-text-input
        id="amount"
        name="amount"
        type="number"
        min="1"
        step="1"
        class="mt-1 block w-full"
        :value="old('amount', $transaction?->amount)"
        required
    />

    <p class="mt-1 text-xs text-gray-500">
        Enter the amount as a whole number, for example 50000.
    </p>

    <x-input-error class="mt-2" :messages="$errors->get('amount')" />
</div>

<div class="mt-6">
    <x-input-label for="transaction_date" :value="__('Transaction Date')" />

    <x-text-input
        id="transaction_date"
        name="transaction_date"
        type="date"
        class="mt-1 block w-full"
        :value="old('transaction_date', $transaction?->transaction_date?->format('Y-m-d'))"
        required
    />

    <x-input-error class="mt-2" :messages="$errors->get('transaction_date')" />
</div>

<div class="mt-6">
    <x-input-label for="description" :value="__('Description')" />

    <textarea
        id="description"
        name="description"
        rows="4"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
    >{{ old('description', $transaction?->description) }}</textarea>

    <x-input-error class="mt-2" :messages="$errors->get('description')" />
</div>

<div class="mt-6">
    <x-input-label for="receipt_image" :value="__('Receipt Image')" />

    <input
        id="receipt_image"
        name="receipt_image"
        type="file"
        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
        class="mt-1 block w-full text-sm text-gray-700 file:me-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200"
    />

    <p class="mt-1 text-xs text-gray-500">
        Optional. JPG, JPEG, PNG, or WEBP. Maximum 2 MB.
    </p>

    <x-input-error class="mt-2" :messages="$errors->get('receipt_image')" />

    @if ($transaction?->receipt_image)
        <div class="mt-4">
            <p class="mb-2 text-sm text-gray-600">Current receipt:</p>

            <a
                href="{{ asset('storage/' . $transaction->receipt_image) }}"
                target="_blank"
                rel="noopener noreferrer"
            >
                <img
                    src="{{ asset('storage/' . $transaction->receipt_image) }}"
                    alt="Current receipt"
                    class="h-32 w-32 rounded-lg border border-gray-200 object-cover"
                >
            </a>
        </div>
    @endif
</div>

<div class="mt-6 flex items-center gap-4">
    <x-primary-button>
        {{ $submitLabel }}
    </x-primary-button>

    <a
        href="{{ route('transactions.index') }}"
        class="text-sm text-gray-600 underline hover:text-gray-900"
    >
        Cancel
    </a>
</div>
