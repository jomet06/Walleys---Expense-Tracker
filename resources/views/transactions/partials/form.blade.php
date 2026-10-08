<div class="space-y-6">
    <div data-field>
        <label for="title" class="ui-label">Title</label>
        <input id="title" name="title" type="text" class="ui-field mt-2" value="{{ old('title', $transaction?->title) }}" required autofocus maxlength="50" placeholder="e.g. Weekly groceries"
            @if ($errors->has('title')) aria-invalid="true" aria-describedby="title-error" @endif>
        <x-input-error for="title" id="title-error" class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div data-field>
        <div class="flex flex-wrap items-center justify-between gap-2">
            <label for="category_id" class="ui-label">Category</label>
            <a href="{{ route('categories.create') }}" class="ui-link text-xs">Add a category</a>
        </div>
        <select id="category_id" name="category_id" required class="ui-field mt-2" @if ($errors->has('category_id')) aria-invalid="true" aria-describedby="category-error" @endif>
            <option value="">Select a category</option>
            <optgroup label="Income">
                @foreach ($categories->where('type', 'income') as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $transaction?->category_id) === (string) $category->id)>{{ $category->name }} {{ $category->user_id === null ? '(Default)' : '(Custom)' }}</option>
                @endforeach
            </optgroup>
            <optgroup label="Expense">
                @foreach ($categories->where('type', 'expense') as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $transaction?->category_id) === (string) $category->id)>{{ $category->name }} {{ $category->user_id === null ? '(Default)' : '(Custom)' }}</option>
                @endforeach
            </optgroup>
        </select>
        <x-input-error for="category_id" id="category-error" class="mt-2" :messages="$errors->get('category_id')" />
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div data-field>
            <label for="amount" class="ui-label">Amount</label>
            <div class="relative mt-2">
                <span class="pointer-events-none absolute left-3.5 top-3 text-sm font-semibold text-gray-400" aria-hidden="true">Rp</span>
                <input id="amount" name="amount" type="number" inputmode="numeric" min="1" max="2147483647" step="1" class="ui-field pl-12" value="{{ old('amount', $transaction?->amount) }}" required placeholder="50000" aria-describedby="amount-hint{{ $errors->has('amount') ? ' amount-error' : '' }}" @if ($errors->has('amount')) aria-invalid="true" @endif>
            </div>
            <p id="amount-hint" class="mt-2 text-xs text-gray-500">Enter the amount as a whole number, for example 50000.</p>
            <x-input-error for="amount" id="amount-error" class="mt-2" :messages="$errors->get('amount')" />
        </div>
        <div data-field>
            <label for="transaction_date" class="ui-label">Transaction Date</label>
            <input id="transaction_date" name="transaction_date" type="date" class="ui-field mt-2" value="{{ old('transaction_date', $transaction?->transaction_date?->format('Y-m-d')) }}" required
                @if ($errors->has('transaction_date')) aria-invalid="true" aria-describedby="transaction-date-error" @endif>
            <x-input-error for="transaction_date" id="transaction-date-error" class="mt-2" :messages="$errors->get('transaction_date')" />
        </div>
    </div>

    <div data-field>
        <label for="description" class="ui-label">Description <span class="font-normal text-gray-400">(optional)</span></label>
        <textarea id="description" name="description" rows="3" class="ui-field mt-2 resize-y" placeholder="Add a note about this transaction"
            @if ($errors->has('description')) aria-invalid="true" aria-describedby="description-error" @endif>{{ old('description', $transaction?->description) }}</textarea>
        <x-input-error for="description" id="description-error" class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div data-field x-data="{
        receiptName: '', receiptError: '', preview: '',
        clearPreview() { if (this.preview) URL.revokeObjectURL(this.preview); this.preview = ''; },
        selectReceipt(event) {
            this.clearPreview(); this.receiptName = ''; this.receiptError = '';
            const file = event.target.files?.[0];
            if (!file) return;
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                this.receiptError = 'Choose a JPG, PNG, or WEBP image.'; event.target.value = ''; return;
            }
            if (file.size > 2 * 1024 * 1024) {
                this.receiptError = 'This image is larger than 2 MB. Choose a smaller image.'; event.target.value = ''; return;
            }
            this.receiptName = file.name; this.preview = URL.createObjectURL(file);
        },
        clearSelection() { this.clearPreview(); this.receiptName = ''; this.receiptError = ''; this.$refs.receiptInput.value = ''; },
        destroy() { this.clearPreview(); }
    }">
        <label for="receipt_image" class="ui-label">Receipt Image <span class="font-normal text-gray-400">(optional)</span></label>
        <div class="mt-2 rounded-xl border border-dashed border-gray-300 bg-gray-50/60 p-4 sm:p-5">
            <div class="mb-4 flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-brand-600"><x-ui-icon name="receipt" class="h-5 w-5" /></span>
                <div>
                    <p class="text-sm font-medium text-gray-700">{{ $transaction?->receipt_image ? 'Upload a replacement receipt' : 'Attach your receipt' }}</p>
                    <p id="receipt-hint" class="mt-1 text-xs text-gray-500">Optional. JPG, JPEG, PNG, or WEBP. Maximum 2 MB.</p>
                </div>
            </div>
            <input id="receipt_image" name="receipt_image" type="file" x-ref="receiptInput" @change="selectReceipt($event)" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                aria-describedby="receipt-hint receipt-client-error{{ $errors->has('receipt_image') ? ' receipt-error' : '' }}" :aria-invalid="receiptError ? 'true' : '{{ $errors->has('receipt_image') ? 'true' : 'false' }}'"
                class="block w-full rounded-lg text-sm text-gray-500 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-brand-100 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
            <p id="receipt-client-error" role="alert" x-cloak x-show="receiptError" x-text="receiptError" class="mt-3 text-sm text-red-600"></p>
            <p x-cloak x-show="receiptName" x-text="'Selected: ' + receiptName" class="mt-3 break-words text-xs text-gray-500" aria-live="polite"></p>
            <div x-cloak x-show="preview" class="mt-4">
                <div class="rounded-lg border border-gray-200 bg-white p-2"><img :src="preview || undefined" alt="Selected receipt preview" class="mx-auto max-h-64 max-w-full rounded-md object-contain"></div>
                <button type="button" @click="clearSelection()" class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-red-600"><x-ui-icon name="close" class="h-3.5 w-3.5" /> Remove selected image</button>
            </div>
        </div>
        <x-input-error for="receipt_image" id="receipt-error" class="mt-2" :messages="$errors->get('receipt_image')" />

        @if ($transaction?->receipt_image)
            <div class="mt-4" x-show="!preview">
                <p class="mb-2 text-xs font-medium text-gray-500">Current receipt:</p>
                <a href="{{ asset('storage/' . $transaction->receipt_image) }}" target="_blank" rel="noopener noreferrer" class="inline-block rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
                    <img src="{{ asset('storage/' . $transaction->receipt_image) }}" alt="Current receipt" class="h-28 w-28 rounded-lg border border-gray-200 object-cover">
                </a>
            </div>
        @endif
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-gray-100 pt-6">
        <a href="{{ route('transactions.index') }}" class="ui-button-secondary">Cancel</a>
        <button type="submit" class="ui-button"><x-ui-icon name="check" class="h-4 w-4" /> {{ $submitLabel }}</button>
    </div>
</div>
