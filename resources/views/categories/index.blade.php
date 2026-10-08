@php
    $allCategories = $systemCategories->concat($customCategories)->sortBy('name');
    $insightSearchData = $categoryInsights->map(fn ($category) => [
        'name' => $category['name'],
        'type' => $category['type'],
        'total' => $category['total'],
    ])->values();
    $periodStart = $monthStart ?? now()->startOfMonth()->format('Y-m-d');
    $periodEnd = $monthEnd ?? now()->endOfMonth()->format('Y-m-d');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="ui-title">Category insights</h1>
                <p class="ui-description">Monthly income and spending, category by category.</p>
            </div>
            <a href="{{ route('transactions.create') }}" class="ui-button">
                <x-ui-icon name="plus" class="h-4 w-4" /> Add Transaction
            </a>
        </div>
    </x-slot>

    <div class="ui-page space-y-6" x-data="{
        type: 'expense', query: '', showUnused: false, items: @js($insightSearchData), deleteId: null, deleteName: '',
        matches(name, type, total) { return this.type === type && (this.showUnused || Number(total) > 0) && name.toLowerCase().includes(this.query.trim().toLowerCase()); },
        visibleCount(type) { return this.items.filter(item => item.type === type && this.matches(item.name, item.type, item.total)).length; },
        confirmDelete(id, name) { this.deleteId = id; this.deleteName = name; this.$dispatch('open-modal', 'delete-category'); }
    }">
        @if (session('status'))
            <div role="status" class="flex items-center gap-3 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-700">
                <x-ui-icon name="check" class="h-5 w-5 shrink-0" />
                @switch(session('status'))
                    @case('category-created') Category created successfully. @break
                    @case('category-updated') Category updated successfully. @break
                    @case('category-deleted') Category deleted successfully. @break
                @endswitch
            </div>
        @endif

        @if (session('error') === 'category-in-use')
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                This category cannot be deleted because it is still used by one or more transactions.
            </div>
        @endif

        <section class="ui-panel overflow-hidden" aria-labelledby="category-insights-title">
            <div class="border-b border-slate-100 px-5 py-6 sm:px-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 id="category-insights-title" class="text-base font-bold text-slate-900">Category breakdown</h2>
                        <p class="mt-1 text-sm text-slate-500">This calendar month · {{ $monthLabel }}</p>
                    </div>
                    <div class="inline-flex rounded-xl border border-slate-200 p-1" role="group" aria-label="Choose a category breakdown">
                        <button type="button" @click="type = 'expense'" :aria-pressed="type === 'expense'" aria-controls="expense-breakdown"
                            :class="type === 'expense' ? 'bg-rose-600 text-white' : 'text-slate-500 hover:bg-slate-50'"
                            class="rounded-lg px-4 py-2 text-sm font-semibold transition">Expense</button>
                        <button type="button" @click="type = 'income'" :aria-pressed="type === 'income'" aria-controls="income-breakdown"
                            :class="type === 'income' ? 'bg-emerald-700 text-white' : 'text-slate-500 hover:bg-slate-50'"
                            class="rounded-lg px-4 py-2 text-sm font-semibold transition">Income</button>
                    </div>
                </div>

                @foreach (['expense' => 'Total expenses', 'income' => 'Total income'] as $type => $label)
                    @php
                        $typeCategories = $categoryInsights->where('type', $type);
                        $usedCategoryCount = $typeCategories->where('total', '>', 0)->count();
                        $typeTransactionCount = (int) $typeCategories->sum('transactionCount');
                    @endphp
                    <div class="mt-6" x-show="type === '{{ $type }}'" @if ($type === 'income') style="display: none;" @endif>
                        <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
                        <p @class(['mt-2 break-words text-3xl font-bold tracking-tight tabular-nums sm:text-4xl', 'text-rose-600' => $type === 'expense', 'text-emerald-600' => $type === 'income'])>Rp {{ number_format($categorySummary[$type], 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs text-slate-500">{{ $typeTransactionCount }} {{ $typeTransactionCount === 1 ? 'transaction' : 'transactions' }} across {{ $usedCategoryCount }} {{ $usedCategoryCount === 1 ? 'category' : 'categories' }}</p>
                    </div>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="relative w-full sm:max-w-xs">
                    <label for="category-search" class="sr-only">Search category breakdown</label>
                    <x-ui-icon name="search" class="pointer-events-none absolute left-3 top-3 h-5 w-5 text-slate-400" />
                    <input id="category-search" type="search" x-model="query" class="ui-field pl-10" placeholder="Search categories" autocomplete="off">
                </div>
                <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-600">
                    <input type="checkbox" x-model="showUnused" class="h-4 w-4"> Include unused categories
                </label>
            </div>

            @foreach (['expense' => 'expenses', 'income' => 'income'] as $type => $typeLabel)
                @php
                    $rankedCategories = $categoryInsights->where('type', $type)->sortByDesc('total')->values();
                    $typeTotal = $categorySummary[$type];
                @endphp
                <div id="{{ $type }}-breakdown" x-show="type === '{{ $type }}'" @if ($type === 'income') style="display: none;" @endif>
                    <div class="flex flex-wrap items-center justify-between gap-2 px-5 pt-5 sm:px-6">
                        <p class="text-xs font-semibold text-slate-600">Highest {{ $type === 'expense' ? 'spending' : 'income' }} first</p>
                        <p class="text-xs text-slate-400" aria-live="polite"><span x-text="visibleCount('{{ $type }}')">{{ $rankedCategories->where('total', '>', 0)->count() }}</span> categories shown</p>
                    </div>
                    <ol class="divide-y divide-slate-100 px-5 sm:px-6" aria-label="{{ ucfirst($typeLabel) }} by category, highest first">
                        @foreach ($rankedCategories as $category)
                            <li class="py-5" data-category-name="{{ $category['name'] }}" data-category-total="{{ $category['total'] }}"
                                x-show="matches($el.dataset.categoryName, '{{ $type }}', $el.dataset.categoryTotal)"
                                @if ($category['total'] === 0) style="display: none;" @endif>
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <span class="w-5 shrink-0 pt-0.5 text-xs font-medium tabular-nums text-slate-400" aria-hidden="true">{{ $category['total'] > 0 ? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) : '—' }}</span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2">
                                            <a href="{{ route('transactions.index', ['category_id' => $category['id'], 'type' => $type, 'start_date' => $periodStart, 'end_date' => $periodEnd]) }}" class="min-w-0 break-words text-sm font-semibold text-slate-800 transition hover:text-brand-700">{{ $category['name'] }}</a>
                                            <p @class(['min-w-0 max-w-full break-words text-sm font-bold tabular-nums sm:text-base', 'text-rose-600' => $type === 'expense' && $category['total'] > 0, 'text-emerald-700' => $type === 'income' && $category['total'] > 0, 'text-slate-400' => $category['total'] === 0])>Rp {{ number_format($category['total'], 0, ',', '.') }}</p>
                                        </div>
                                        <div class="mt-2 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                                            <span>{{ $category['transactionCount'] }} {{ $category['transactionCount'] === 1 ? 'transaction' : 'transactions' }}</span>
                                            <span class="tabular-nums">{{ number_format($category['share'], 1, ',', '.') }}% of {{ $typeLabel }}</span>
                                        </div>
                                        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-slate-100" aria-hidden="true">
                                            <div @class(['h-full rounded-full', 'bg-rose-500' => $type === 'expense', 'bg-emerald-500' => $type === 'income']) style="width: {{ min(100, max(0, $category['share'])) }}%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ol>

                    <div x-show="visibleCount('{{ $type }}') === 0" class="px-5 py-10 text-center sm:px-6">
                        @if ($typeTotal === 0)
                            <h3 class="text-sm font-semibold text-slate-800">No {{ $type === 'expense' ? 'expenses' : 'income' }} recorded this month.</h3>
                            <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-slate-500">Add a transaction to see your {{ $type === 'expense' ? 'spending' : 'income' }} by category for {{ $monthLabel }}.</p>
                            <a href="{{ route('transactions.create') }}" class="ui-button mt-5"><x-ui-icon name="plus" class="h-4 w-4" /> Add Transaction</a>
                        @else
                            <h3 class="text-sm font-semibold text-slate-800">No categories match your search.</h3>
                            <button type="button" @click="query = ''" class="ui-link mt-3">Clear search</button>
                        @endif
                    </div>

                    @if ($typeTotal > 0)
                        <p class="border-t border-slate-100 px-5 py-4 text-xs leading-relaxed text-slate-400 sm:px-6">Percentages use all {{ $typeLabel }} recorded in {{ $monthLabel }}. Searching only changes the list.</p>
                    @endif
                </div>
            @endforeach
        </section>

        <details class="ui-panel group" @if (session('status') || session('error')) open @endif>
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 rounded-2xl px-5 py-5 [&::-webkit-details-marker]:hidden sm:px-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Manage categories</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ $systemCategories->count() }} default · {{ $customCategories->count() }} custom</p>
                </div>
                <x-ui-icon name="chevron-down" class="h-5 w-5 text-slate-400 transition-transform group-open:rotate-180" />
            </summary>
            <div class="border-t border-slate-100 px-5 pb-2 sm:px-6">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 py-4">
                    <p class="max-w-lg text-xs leading-relaxed text-slate-500">Default categories are available to every user and cannot be edited or deleted. Custom categories belong to your account.</p>
                    <a href="{{ route('categories.create') }}" class="ui-button-secondary"><x-ui-icon name="plus" class="h-4 w-4" /> Add Category</a>
                </div>
                <ul class="divide-y divide-slate-100" aria-label="Available categories">
                    @forelse ($allCategories as $category)
                        <li class="flex flex-wrap items-center justify-between gap-3 py-4">
                            <div class="min-w-0 flex-1">
                                <h3 class="break-words text-sm font-semibold text-slate-800">{{ $category->name }}</h3>
                                <p class="mt-1 text-xs text-slate-400">{{ $category->user_id === null ? 'Default' : 'Custom' }} · {{ ucfirst($category->type) }}</p>
                            </div>
                            @if ($category->user_id !== null)
                                <div class="flex shrink-0 items-center gap-3">
                                    <a href="{{ route('categories.edit', $category) }}" class="ui-link" aria-label="Edit {{ $category->name }}"><x-ui-icon name="edit" class="h-4 w-4" /> Edit</a>
                                    <form id="delete-category-{{ $category->id }}" method="POST" action="{{ route('categories.destroy', $category) }}"
                                        @submit.prevent="confirmDelete({{ $category->id }}, $el.dataset.categoryName)" data-category-name="{{ $category->name }}"
                                        onsubmit="if (!window.Alpine) return confirm('Delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 rounded text-sm font-semibold text-slate-500 transition hover:text-red-600" aria-label="Delete {{ $category->name }}"><x-ui-icon name="trash" class="h-4 w-4" /> Delete</button>
                                    </form>
                                </div>
                            @endif
                        </li>
                    @empty
                        <li class="py-8 text-center text-sm text-slate-500">No categories are available.</li>
                    @endforelse
                </ul>
            </div>
        </details>

        <x-modal name="delete-category" maxWidth="md" focusable>
            <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="delete-category-title" aria-describedby="delete-category-description">
                <h2 id="delete-category-title" class="text-lg font-bold text-slate-900">Delete category?</h2>
                <p id="delete-category-description" class="mt-2 text-sm leading-relaxed text-slate-500"><span class="font-semibold text-slate-700" x-text="deleteName"></span> will be removed. Categories used by a transaction cannot be deleted.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="ui-button-secondary" @click="$dispatch('close')">Keep category</button>
                    <button type="button" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        @click="document.getElementById('delete-category-' + deleteId)?.submit()">Delete category</button>
                </div>
            </div>
        </x-modal>
    </div>
</x-app-layout>
