@php
    $activeFilters = collect(['start_date', 'end_date', 'type', 'category_id'])->filter(fn ($field) => request()->filled($field))->count();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="ui-title">{{ __('Transactions') }}</h1>
                <p class="ui-description">Review and manage your income and expenses.</p>
            </div>
            <a href="{{ route('transactions.create') }}" class="ui-button"><x-ui-icon name="plus" class="h-4 w-4" /> Add Transaction</a>
        </div>
    </x-slot>

    <div class="ui-page space-y-6" x-data="{
        filtersOpen: {{ $activeFilters > 0 || $errors->any() ? 'true' : 'false' }}, deleteId: null, deleteName: '',
        confirmDelete(id, name) { this.deleteId = id; this.deleteName = name; this.$dispatch('open-modal', 'delete-transaction'); }
    }">
        @if (session('status'))
            <div role="status" class="flex items-center gap-3 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-700">
                <x-ui-icon name="check" class="h-5 w-5 shrink-0" />
                @switch(session('status'))
                    @case('transaction-created') Transaction created successfully. @break
                    @case('transaction-updated') Transaction updated successfully. @break
                    @case('transaction-deleted') Transaction deleted successfully. @break
                @endswitch
            </div>
        @endif

        <section class="ui-panel">
            <button type="button" @click="filtersOpen = !filtersOpen" :aria-expanded="filtersOpen" aria-controls="transaction-filters"
                class="flex w-full items-center justify-between gap-4 rounded-xl px-5 py-4 text-left transition hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand-500 sm:px-6">
                <span class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-ui-icon name="filter" class="h-4 w-4" /></span>
                    <span class="text-sm font-semibold text-gray-900">Filters</span>
                    @if ($activeFilters)
                        <span class="rounded-md bg-brand-100 px-2 py-1 text-xs font-semibold text-brand-700">{{ $activeFilters }} active</span>
                    @endif
                </span>
                <x-ui-icon name="chevron-down" class="h-5 w-5 text-gray-400 transition-transform" ::class="filtersOpen && 'rotate-180'" />
            </button>
            <div id="transaction-filters" x-show="filtersOpen" x-transition.opacity class="border-t border-gray-100 px-5 py-5 sm:px-6">
                <form method="GET" action="{{ route('transactions.index') }}" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label for="start_date" class="ui-label">Start Date</label>
                        <input id="start_date" name="start_date" type="date" class="ui-field mt-2" value="{{ request('start_date') }}"
                            @if ($errors->has('start_date')) aria-invalid="true" aria-describedby="start-date-error" @endif>
                        <x-input-error id="start-date-error" class="mt-2" :messages="$errors->get('start_date')" />
                    </div>
                    <div>
                        <label for="end_date" class="ui-label">End Date</label>
                        <input id="end_date" name="end_date" type="date" class="ui-field mt-2" value="{{ request('end_date') }}"
                            @if ($errors->has('end_date')) aria-invalid="true" aria-describedby="end-date-error" @endif>
                        <x-input-error id="end-date-error" class="mt-2" :messages="$errors->get('end_date')" />
                    </div>
                    <div>
                        <label for="type" class="ui-label">Type</label>
                        <select id="type" name="type" class="ui-field mt-2" @if ($errors->has('type')) aria-invalid="true" aria-describedby="filter-type-error" @endif>
                            <option value="">All Types</option>
                            <option value="income" @selected(request('type') === 'income')>Income</option>
                            <option value="expense" @selected(request('type') === 'expense')>Expense</option>
                        </select>
                        <x-input-error id="filter-type-error" class="mt-2" :messages="$errors->get('type')" />
                    </div>
                    <div>
                        <label for="category_id" class="ui-label">Category</label>
                        <select id="category_id" name="category_id" class="ui-field mt-2" @if ($errors->has('category_id')) aria-invalid="true" aria-describedby="filter-category-error" @endif>
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }} — {{ ucfirst($category->type) }}</option>
                            @endforeach
                        </select>
                        <x-input-error id="filter-category-error" class="mt-2" :messages="$errors->get('category_id')" />
                    </div>
                    <div class="flex flex-wrap gap-3 sm:col-span-2 xl:col-span-4">
                        <button type="submit" class="ui-button">Apply Filters</button>
                        <a href="{{ route('transactions.index') }}" class="ui-button-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </section>

        <section class="ui-panel overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-5 py-5 sm:px-6">
                <div>
                    <h2 class="text-base font-bold text-gray-900">{{ $activeFilters ? 'Filtered transactions' : 'All transactions' }}</h2>
                    <p class="mt-1 text-xs text-gray-500">{{ $transactions->total() }} {{ $transactions->total() === 1 ? 'transaction' : 'transactions' }}{{ $activeFilters ? ' matching your filters' : ' recorded' }}</p>
                </div>
                @if ($transactions->total() > 0)
                    <span class="text-xs text-gray-500">Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }}</span>
                @endif
            </div>

            @if ($transactions->isNotEmpty())
                <div class="divide-y divide-gray-100 lg:hidden">
                    @foreach ($transactions as $transaction)
                        <article class="p-5 sm:px-6">
                            <div class="flex items-center gap-3">
                                <span @class(['flex h-11 w-11 shrink-0 items-center justify-center rounded-xl', 'bg-brand-100 text-brand-700' => $transaction->category->type === 'income', 'bg-[#fff3eb] text-[#b56438]' => $transaction->category->type === 'expense'])>
                                    <x-category-icon :name="$transaction->category->icon" class="h-5 w-5" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <h3 class="break-words text-sm font-semibold text-gray-900">{{ $transaction->title }}</h3>
                                    <p class="mt-1 break-words text-xs text-gray-500">{{ $transaction->category->name }}</p>
                                </div>
                                <span class="{{ $transaction->category->type === 'income' ? 'ui-badge-income' : 'ui-badge-expense' }}">{{ ucfirst($transaction->category->type) }}</span>
                            </div>
                            <div class="mt-4 flex flex-wrap items-end justify-between gap-2">
                                <p @class(['block min-w-0 break-words text-lg font-bold tabular-nums', 'text-brand-700' => $transaction->category->type === 'income', 'text-[#b56438]' => $transaction->category->type === 'expense'])>{{ $transaction->category->type === 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                <time datetime="{{ $transaction->transaction_date->format('Y-m-d') }}" class="pb-0.5 text-xs text-gray-500">{{ $transaction->transaction_date->format('d M Y') }}</time>
                            </div>
                            @if ($transaction->description)
                                <p class="mt-2 break-words text-sm leading-relaxed text-gray-500">{{ $transaction->description }}</p>
                            @endif
                            <div class="mt-4 flex items-center justify-between gap-4 border-t border-gray-100 pt-3">
                                @if ($transaction->receipt_image)
                                    <a href="{{ asset('storage/' . $transaction->receipt_image) }}" target="_blank" rel="noopener noreferrer" class="ui-link inline-flex items-center gap-1.5" aria-label="View receipt for {{ $transaction->title }}"><x-ui-icon name="receipt" class="h-4 w-4" /> Receipt</a>
                                @else
                                    <span class="text-xs text-gray-400">No receipt</span>
                                @endif
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('transactions.edit', $transaction) }}" class="ui-link inline-flex items-center gap-1.5" aria-label="Edit {{ $transaction->title }}"><x-ui-icon name="edit" class="h-4 w-4" /> Edit</a>
                                    <button type="submit" form="delete-transaction-{{ $transaction->id }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 hover:text-red-600" aria-label="Delete {{ $transaction->title }}"><x-ui-icon name="trash" class="h-4 w-4" /> Delete</button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="hidden overflow-x-auto lg:block">
                    <table class="w-full min-w-[880px] text-sm">
                        <caption class="sr-only">Your income and expense transactions</caption>
                        <thead class="border-b border-gray-100 bg-gray-50/70 text-xs text-gray-500">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left font-medium">Transaction</th>
                                <th scope="col" class="px-4 py-3 text-left font-medium">Category</th>
                                <th scope="col" class="px-4 py-3 text-left font-medium">Date</th>
                                <th scope="col" class="px-4 py-3 text-left font-medium">Type</th>
                                <th scope="col" class="px-4 py-3 text-right font-medium">Amount</th>
                                <th scope="col" class="px-4 py-3 text-center font-medium">Receipt</th>
                                <th scope="col" class="px-6 py-3 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($transactions as $transaction)
                                <tr class="transition hover:bg-gray-50/60">
                                    <th scope="row" class="px-6 py-4 text-left font-normal">
                                        <div class="flex items-center gap-3">
                                            <span @class(['flex h-10 w-10 shrink-0 items-center justify-center rounded-xl', 'bg-brand-100 text-brand-700' => $transaction->category->type === 'income', 'bg-[#fff3eb] text-[#b56438]' => $transaction->category->type === 'expense'])><x-category-icon :name="$transaction->category->icon" class="h-5 w-5" /></span>
                                            <div class="max-w-[220px]">
                                                <a href="{{ route('transactions.edit', $transaction) }}" class="break-words font-semibold text-gray-900 hover:text-brand-600">{{ $transaction->title }}</a>
                                                @if ($transaction->description)<p class="mt-1 truncate text-xs text-gray-500" title="{{ $transaction->description }}">{{ $transaction->description }}</p>@endif
                                            </div>
                                        </div>
                                    </th>
                                    <td class="px-4 py-4 text-gray-600">{{ $transaction->category->name }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 text-gray-500"><time datetime="{{ $transaction->transaction_date->format('Y-m-d') }}">{{ $transaction->transaction_date->format('d M Y') }}</time></td>
                                    <td class="px-4 py-4"><span class="{{ $transaction->category->type === 'income' ? 'ui-badge-income' : 'ui-badge-expense' }}">{{ ucfirst($transaction->category->type) }}</span></td>
                                    <td @class(['whitespace-nowrap px-4 py-4 text-right font-semibold tabular-nums', 'text-brand-700' => $transaction->category->type === 'income', 'text-[#b56438]' => $transaction->category->type === 'expense'])>{{ $transaction->category->type === 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-center">
                                        @if ($transaction->receipt_image)
                                            <a href="{{ asset('storage/' . $transaction->receipt_image) }}" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-lg p-2 text-brand-600 hover:bg-brand-50" aria-label="View receipt for {{ $transaction->title }}"><x-ui-icon name="receipt" class="h-5 w-5" /></a>
                                        @else
                                            <span class="text-gray-300" aria-label="No receipt">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-1">
                                            <a href="{{ route('transactions.edit', $transaction) }}" class="rounded-lg p-2 text-gray-400 transition hover:bg-brand-50 hover:text-brand-600" aria-label="Edit {{ $transaction->title }}" title="Edit transaction"><x-ui-icon name="edit" class="h-4 w-4" /></a>
                                            <button type="submit" form="delete-transaction-{{ $transaction->id }}" class="rounded-lg p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-600" aria-label="Delete {{ $transaction->title }}" title="Delete transaction"><x-ui-icon name="trash" class="h-4 w-4" /></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="ui-empty">
                    <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"><x-ui-icon name="transactions" class="h-6 w-6" /></span>
                    <h3 class="text-base font-semibold text-gray-900">No transactions found.</h3>
                    <p class="mt-2">{{ $activeFilters ? 'Try a wider date range or clear your filters.' : 'Record your first income or expense to start your history.' }}</p>
                    <a href="{{ $activeFilters ? route('transactions.index') : route('transactions.create') }}" class="ui-button mt-5">{{ $activeFilters ? 'Clear filters' : 'Add Transaction' }}</a>
                </div>
            @endif

            @if ($transactions->hasPages())
                <div class="border-t border-gray-100 px-5 py-4 sm:px-6">{{ $transactions->links() }}</div>
            @endif
        </section>

        @foreach ($transactions as $transaction)
            <form id="delete-transaction-{{ $transaction->id }}" method="POST" action="{{ route('transactions.destroy', $transaction) }}" class="hidden"
                @submit.prevent="confirmDelete({{ $transaction->id }}, $el.dataset.transactionTitle)" data-transaction-title="{{ $transaction->title }}"
                onsubmit="if (!window.Alpine) return confirm('Delete this transaction and its receipt?');">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <x-modal name="delete-transaction" maxWidth="md" focusable>
            <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="delete-transaction-title" aria-describedby="delete-transaction-description">
                <h2 id="delete-transaction-title" class="text-lg font-bold text-gray-900">Delete transaction?</h2>
                <p id="delete-transaction-description" class="mt-2 text-sm leading-relaxed text-gray-500"><span class="font-semibold text-gray-700" x-text="deleteName"></span> and its receipt will be permanently removed.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="ui-button-secondary" @click="$dispatch('close')">Keep transaction</button>
                    <button type="button" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        @click="document.getElementById('delete-transaction-' + deleteId)?.submit()">Delete transaction</button>
                </div>
            </div>
        </x-modal>
    </div>
</x-app-layout>
