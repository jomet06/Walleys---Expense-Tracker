<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="ui-title">Financial dashboard</h1>
                <p class="ui-description">Track this month's balance, income, and expenses.</p>
            </div>
            <a href="{{ route('transactions.create') }}" class="ui-button">
                <x-ui-icon name="plus" class="h-4 w-4" />
                Add transaction
            </a>
        </div>
    </x-slot>

    <div class="ui-page space-y-6">
        <div class="flex items-center gap-2 text-sm font-medium text-slate-500">
            <x-ui-icon name="calendar" class="h-4 w-4 text-slate-400" />
            <span>{{ $monthLabel }}</span>
            <span class="text-slate-300" aria-hidden="true">/</span>
            <span>Monthly overview</span>
        </div>

        <div class="grid gap-4 xl:grid-cols-3">
            <section class="min-w-0 rounded-2xl bg-brand-700 p-6 text-white sm:p-7">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-sm font-medium text-white/90">Net Balance</h2>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10">
                        <x-ui-icon name="wallet" class="h-5 w-5" />
                    </span>
                </div>
                <p class="mt-5 break-words text-3xl font-bold tracking-tight tabular-nums sm:text-[2rem]">Rp {{ number_format($summary['balance'], 0, ',', '.') }}</p>
                <p class="mt-3 text-xs leading-relaxed text-white/80">Income minus expenses this month</p>
            </section>

            @foreach (['income' => 'Total Income', 'expense' => 'Total Expense'] as $key => $label)
                <section @class(['ui-panel min-w-0 p-6 sm:p-7', 'bg-brand-50 border-brand-100' => $key === 'income'])>
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-sm font-medium text-slate-500">{{ $label }}</h2>
                        <span @class(['flex h-10 w-10 items-center justify-center rounded-xl', 'bg-white text-brand-700' => $key === 'income', 'bg-rose-50 text-rose-600' => $key === 'expense'])>
                            <x-ui-icon :name="$key === 'income' ? 'arrow-down' : 'arrow-up'" class="h-5 w-5" />
                        </span>
                    </div>
                    <p class="mt-5 break-words text-3xl font-bold tracking-tight tabular-nums text-slate-900 sm:text-[2rem]">Rp {{ number_format($summary[$key], 0, ',', '.') }}</p>
                    <p class="mt-3 text-xs text-slate-500">{{ $key === 'income' ? 'Money coming in' : 'Money going out' }} &middot; {{ $monthLabel }}</p>
                </section>
            @endforeach
        </div>

        <div class="grid items-start gap-6 xl:grid-cols-3">
            <section class="ui-panel ui-panel-body min-w-0 xl:col-span-2">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Income &amp; expenses</h2>
                        <p class="mt-1 text-xs text-slate-500">Last six calendar months</p>
                    </div>
                    <div class="inline-flex rounded-lg bg-slate-100 p-1" role="group" aria-label="Chart display">
                        <button type="button" data-chart-type="bar" aria-pressed="true" aria-controls="monthly-chart" class="rounded-md px-3 py-1.5 text-xs font-semibold text-slate-500 transition-colors aria-pressed:bg-white aria-pressed:text-slate-900 aria-pressed:shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">Bars</button>
                        <button type="button" data-chart-type="line" aria-pressed="false" aria-controls="monthly-chart" class="rounded-md px-3 py-1.5 text-xs font-semibold text-slate-500 transition-colors aria-pressed:bg-white aria-pressed:text-slate-900 aria-pressed:shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">Lines</button>
                    </div>
                </div>
                <div class="relative mt-6 h-72 w-full sm:h-80">
                    <canvas id="monthly-chart" role="img" aria-label="Monthly income and expenses for the last six months">Monthly income and expense totals are listed below.</canvas>
                </div>
                <p class="mt-2 text-center text-[11px] text-slate-500">Select a legend label to show or hide a series.</p>
                <details class="group mt-5 border-t border-slate-100 pt-4 text-xs text-slate-500">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 font-semibold text-slate-600 [&::-webkit-details-marker]:hidden">
                        View monthly totals
                        <x-ui-icon name="chevron-down" class="h-4 w-4 transition-transform group-open:rotate-180" />
                    </summary>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full min-w-[320px] text-left">
                            <caption class="sr-only">Monthly income and expense totals</caption>
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-500">
                                    <th scope="col" class="pb-2 font-medium">Month</th>
                                    <th scope="col" class="pb-2 text-right font-medium">Income</th>
                                    <th scope="col" class="pb-2 text-right font-medium">Expenses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($monthlyChart['labels'] as $index => $label)
                                    <tr class="border-b border-slate-50 last:border-0">
                                        <th scope="row" class="py-2.5 font-medium text-slate-600">{{ $label }}</th>
                                        <td class="py-2.5 text-right tabular-nums text-slate-700">Rp {{ number_format($monthlyChart['income'][$index], 0, ',', '.') }}</td>
                                        <td class="py-2.5 text-right tabular-nums text-slate-700">Rp {{ number_format($monthlyChart['expense'][$index], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            </section>

            <section class="ui-panel ui-panel-body min-w-0">
                <h2 class="text-base font-bold text-slate-900">Expense breakdown</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $monthLabel }} by category</p>
                @if (count($expenseChart['values']))
                    <div class="relative mx-auto mt-6 h-52 max-w-[260px]">
                        <canvas id="expense-chart" role="img" aria-label="Current month expenses by category">Expense totals by category are listed below.</canvas>
                        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center px-12 text-center">
                            <span class="text-xs text-slate-500">Total expenses</span>
                            <span class="mt-1 max-w-full break-words text-base font-bold tabular-nums text-slate-900">Rp {{ number_format($summary['expense'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <ul class="mt-6 max-h-64 space-y-4 overflow-y-auto pr-1 text-sm" aria-label="Expense category totals">
                        @foreach ($expenseChart['labels'] as $index => $label)
                            <li>
                                <div class="flex items-start justify-between gap-3">
                                    <span class="flex min-w-0 items-start gap-2.5 text-slate-600">
                                        <span data-category-swatch="{{ $index }}" class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-brand-500" aria-hidden="true"></span>
                                        <span class="break-all">{{ $label }}</span>
                                    </span>
                                    <span class="shrink-0 text-xs text-slate-500">{{ $summary['expense'] > 0 ? round($expenseChart['values'][$index] / $summary['expense'] * 100) : 0 }}%</span>
                                </div>
                                <p class="mt-1 pl-[18px] text-sm font-semibold tabular-nums text-slate-800">Rp {{ number_format($expenseChart['values'][$index], 0, ',', '.') }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="ui-empty mt-5 min-h-[300px]">
                        <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-700">
                            <x-ui-icon name="categories" class="h-6 w-6" />
                        </span>
                        <p class="font-semibold text-slate-700">No expenses this month.</p>
                        <p class="mt-2 max-w-[220px] text-xs leading-relaxed text-slate-500">Your category breakdown will appear here.</p>
                    </div>
                @endif
            </section>
        </div>

        <section class="ui-panel min-w-0 overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-5 sm:p-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Recent transactions</h2>
                    <p class="mt-1 text-xs text-slate-500">Your latest five entries</p>
                </div>
                <a href="{{ route('transactions.index') }}" class="ui-link inline-flex items-center gap-2 text-xs">
                    View transaction history
                    <x-ui-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($recentTransactions as $transaction)
                    <li>
                        <a href="{{ route('transactions.edit', $transaction) }}" class="group flex items-start gap-3 p-5 transition-colors hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-brand-600 sm:items-center sm:gap-4 sm:px-6">
                            <span @class(['flex h-10 w-10 shrink-0 items-center justify-center rounded-xl', 'bg-brand-50 text-brand-700' => $transaction->category->type === 'income', 'bg-rose-50 text-rose-600' => $transaction->category->type === 'expense'])>
                                <x-ui-icon :name="$transaction->category->type === 'income' ? 'arrow-down' : 'arrow-up'" class="h-5 w-5" />
                            </span>
                            <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                                <div class="min-w-0">
                                    <p class="break-all text-sm font-semibold text-slate-800 group-hover:text-brand-700">{{ $transaction->title }}</p>
                                    <p class="mt-1 break-all text-xs text-slate-500">{{ $transaction->category->name }} <span class="mx-1 text-slate-300" aria-hidden="true">&middot;</span> <time datetime="{{ $transaction->transaction_date->format('Y-m-d') }}">{{ $transaction->transaction_date->format('d M Y') }}</time></p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2 sm:flex-col sm:items-end">
                                    <p @class(['break-words text-sm font-bold tabular-nums', 'text-brand-700' => $transaction->category->type === 'income', 'text-slate-800' => $transaction->category->type === 'expense'])>{{ $transaction->category->type === 'income' ? '+' : '−' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                    <span @class(['text-[10px] font-medium', 'text-brand-700' => $transaction->category->type === 'income', 'text-slate-500' => $transaction->category->type === 'expense'])>{{ ucfirst($transaction->category->type) }}</span>
                                </div>
                            </div>
                            <x-ui-icon name="arrow-right" class="hidden h-4 w-4 shrink-0 text-slate-300 group-hover:text-brand-700 sm:block" />
                        </a>
                    </li>
                @empty
                    <li class="ui-empty py-12">
                        <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <x-ui-icon name="receipt" class="h-6 w-6" />
                        </span>
                        <p class="text-sm font-semibold text-slate-700">No transactions yet.</p>
                        <p class="mt-2 px-4 text-xs leading-relaxed text-slate-500">Add your first transaction to start tracking your finances.</p>
                        <a href="{{ route('transactions.create') }}" class="ui-link mt-4 inline-flex items-center gap-2 text-xs">
                            <x-ui-icon name="plus" class="h-4 w-4" />
                            Add your first transaction
                        </a>
                    </li>
                @endforelse
            </ul>
        </section>
    </div>
    <script>
        window.dashboardData = {{ Illuminate\Support\Js::from(['monthly' => $monthlyChart, 'expenses' => $expenseChart]) }};
    </script>
</x-app-layout>
