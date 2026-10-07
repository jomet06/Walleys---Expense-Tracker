<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Transactions') }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Review and manage your income and expenses.
                </p>
            </div>

            <a
                href="{{ route('transactions.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                Add Transaction
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    @switch(session('status'))
                        @case('transaction-created')
                            Transaction created successfully.
                            @break

                        @case('transaction-updated')
                            Transaction updated successfully.
                            @break

                        @case('transaction-deleted')
                            Transaction deleted successfully.
                            @break
                    @endswitch
                </div>
            @endif

            <section class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Filters
                    </h3>

                    <form
                        method="GET"
                        action="{{ route('transactions.index') }}"
                        class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-4"
                    >
                        <div>
                            <x-input-label for="start_date" :value="__('Start Date')" />

                            <x-text-input
                                id="start_date"
                                name="start_date"
                                type="date"
                                class="mt-1 block w-full"
                                :value="request('start_date')"
                            />

                            <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                        </div>

                        <div>
                            <x-input-label for="end_date" :value="__('End Date')" />

                            <x-text-input
                                id="end_date"
                                name="end_date"
                                type="date"
                                class="mt-1 block w-full"
                                :value="request('end_date')"
                            />

                            <x-input-error class="mt-2" :messages="$errors->get('end_date')" />
                        </div>

                        <div>
                            <x-input-label for="type" :value="__('Type')" />

                            <select
                                id="type"
                                name="type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All Types</option>
                                <option value="income" @selected(request('type') === 'income')>
                                    Income
                                </option>
                                <option value="expense" @selected(request('type') === 'expense')>
                                    Expense
                                </option>
                            </select>

                            <x-input-error class="mt-2" :messages="$errors->get('type')" />
                        </div>

                        <div>
                            <x-input-label for="category_id" :value="__('Category')" />

                            <select
                                id="category_id"
                                name="category_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All Categories</option>

                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->id }}"
                                        @selected((string) request('category_id') === (string) $category->id)
                                    >
                                        {{ $category->name }} — {{ ucfirst($category->type) }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>

                        <div class="md:col-span-2 lg:col-span-4 flex flex-wrap gap-3">
                            <x-primary-button>
                                Apply Filters
                            </x-primary-button>

                            <a
                                href="{{ route('transactions.index') }}"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </section>

            <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Transaction
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Category
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Receipt
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ $transaction->transaction_date->format('d M Y') }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">
                                            {{ $transaction->title }}
                                        </div>

                                        @if ($transaction->description)
                                            <div class="mt-1 max-w-xs truncate text-sm text-gray-500">
                                                {{ $transaction->description }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                        <div>{{ $transaction->category->name }}</div>

                                        <div class="mt-1 text-xs text-gray-400">
                                            {{ $transaction->category->icon }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            @class([
                                                'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium',
                                                'bg-green-100 text-green-700' => $transaction->category->type === 'income',
                                                'bg-red-100 text-red-700' => $transaction->category->type === 'expense',
                                            ])
                                        >
                                            {{ ucfirst($transaction->category->type) }}
                                        </span>
                                    </td>

                                    <td
                                        @class([
                                            'whitespace-nowrap px-6 py-4 text-right text-sm font-semibold',
                                            'text-green-600' => $transaction->category->type === 'income',
                                            'text-red-600' => $transaction->category->type === 'expense',
                                        ])
                                    >
                                        {{ $transaction->category->type === 'income' ? '+' : '-' }}
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        @if ($transaction->receipt_image)
                                            <a
                                                href="{{ asset('storage/' . $transaction->receipt_image) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-block"
                                            >
                                                <img
                                                    src="{{ asset('storage/' . $transaction->receipt_image) }}"
                                                    alt="Receipt for {{ $transaction->title }}"
                                                    class="h-12 w-12 rounded-md border border-gray-200 object-cover"
                                                >
                                            </a>
                                        @else
                                            <span class="text-sm text-gray-400">—</span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a
                                                href="{{ route('transactions.edit', $transaction) }}"
                                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('transactions.destroy', $transaction) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this transaction?');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="text-sm font-medium text-red-600 hover:text-red-800"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="7"
                                        class="px-6 py-12 text-center text-sm text-gray-500"
                                    >
                                        No transactions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transactions->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
