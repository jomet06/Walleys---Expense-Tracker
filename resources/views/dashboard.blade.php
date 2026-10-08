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

    @php
        $breakdownModes = [
            'cashflow' => ['label' => 'Cashflow', 'chart' => $cashflowChart, 'totalLabel' => 'Total movement', 'description' => 'Income and expenses as shares of their combined total.', 'empty' => 'No cashflow this month.', 'colors' => ['#059669', '#e11d48']],
            'income' => ['label' => 'Income', 'chart' => $incomeChart, 'totalLabel' => 'Total income', 'description' => 'Income this month, grouped by category.', 'empty' => 'No income this month.', 'colors' => ['#059669', '#34d399', '#065f46', '#6ee7b7', '#0d9488', '#84cc16']],
            'expenses' => ['label' => 'Expenses', 'chart' => $expenseChart, 'totalLabel' => 'Total expenses', 'description' => 'Expenses this month, grouped by category.', 'empty' => 'No expenses this month.', 'colors' => ['#e11d48', '#fb7185', '#9f1239', '#fda4af', '#be123c', '#f43f5e']],
        ];
        $cashflowTotal = array_sum($cashflowChart['values']);
    @endphp

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
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10"><x-ui-icon name="wallet" class="h-5 w-5" /></span>
                </div>
                <p class="mt-5 break-words text-3xl font-bold tracking-tight tabular-nums sm:text-[2rem]">Rp {{ number_format($summary['balance'], 0, ',', '.') }}</p>
                <p class="mt-3 text-xs leading-relaxed text-white/80">Income minus expenses this month</p>
            </section>

            @foreach (['income' => 'Total Income', 'expense' => 'Total Expense'] as $key => $label)
                <section @class(['ui-panel min-w-0 border-t-[3px] p-6 sm:p-7', 'border-t-emerald-500' => $key === 'income', 'border-t-rose-500' => $key === 'expense'])>
                    <h2 class="text-sm font-medium text-slate-500">{{ $label }}</h2>
                    <p @class(['mt-5 break-words text-3xl font-bold tracking-tight tabular-nums sm:text-[2rem]', 'text-emerald-700' => $key === 'income', 'text-rose-700' => $key === 'expense'])>Rp {{ number_format($summary[$key], 0, ',', '.') }}</p>
                    <p class="mt-3 text-xs text-slate-500">{{ $key === 'income' ? 'Money coming in' : 'Money going out' }} &middot; {{ $monthLabel }}</p>
                </section>
            @endforeach
        </div>

        <div class="grid items-start gap-6 xl:grid-cols-3">
            <section class="ui-panel ui-panel-body min-w-0 xl:col-span-2">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Expenses over time</h2>
                        <p class="mt-1 text-xs text-slate-500">Last six calendar months</p>
                    </div>
                    <div class="inline-flex rounded-lg bg-slate-100 p-1" role="group" aria-label="Expense chart display">
                        <button type="button" data-chart-type="bar" aria-pressed="true" aria-controls="monthly-chart" class="rounded-md px-3 py-1.5 text-xs font-semibold text-slate-500 transition-colors aria-pressed:bg-white aria-pressed:text-slate-900 aria-pressed:shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">Bars</button>
                        <button type="button" data-chart-type="line" aria-pressed="false" aria-controls="monthly-chart" class="rounded-md px-3 py-1.5 text-xs font-semibold text-slate-500 transition-colors aria-pressed:bg-white aria-pressed:text-slate-900 aria-pressed:shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">Lines</button>
                    </div>
                </div>
                <div class="relative mt-6 h-72 w-full sm:h-80">
                    <canvas id="monthly-chart" role="img" aria-label="Monthly expenses for the last six months">Monthly expense totals are listed below.</canvas>
                </div>
                @if (array_sum($monthlyChart['expense']) === 0)
                    <p class="mt-2 text-center text-xs text-slate-500">No expenses in the last six months.</p>
                @endif
                <details class="group mt-5 border-t border-slate-100 pt-4 text-xs text-slate-500">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 font-semibold text-slate-600 [&::-webkit-details-marker]:hidden">
                        View expense totals
                        <x-ui-icon name="chevron-down" class="h-4 w-4 transition-transform group-open:rotate-180" />
                    </summary>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-left">
                            <caption class="sr-only">Monthly expense totals</caption>
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-500">
                                    <th scope="col" class="pb-2 font-medium">Month</th>
                                    <th scope="col" class="pb-2 text-right font-medium">Expenses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($monthlyChart['labels'] as $index => $label)
                                    <tr class="border-b border-slate-50 last:border-0">
                                        <th scope="row" class="py-2.5 font-medium text-slate-600">{{ $label }}</th>
                                        <td class="py-2.5 text-right tabular-nums text-slate-700">Rp {{ number_format($monthlyChart['expense'][$index], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            </section>

            <section class="ui-panel ui-panel-body min-w-0" aria-labelledby="breakdown-title">
                <h2 id="breakdown-title" class="text-base font-bold text-slate-900">Monthly breakdown</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $monthLabel }}</p>
                <div class="mt-5 grid grid-cols-3 gap-1 rounded-lg bg-slate-100 p-1" role="tablist" aria-label="Monthly breakdown">
                    @foreach ($breakdownModes as $mode => $details)
                        <button type="button" id="breakdown-tab-{{ $mode }}" data-breakdown-mode="{{ $mode }}" role="tab" aria-selected="{{ $mode === 'cashflow' ? 'true' : 'false' }}" aria-controls="breakdown-panel-{{ $mode }}" tabindex="{{ $mode === 'cashflow' ? '0' : '-1' }}" class="min-h-10 rounded-md px-2 py-2 text-[11px] font-semibold text-slate-500 transition-colors aria-selected:bg-white aria-selected:text-slate-900 aria-selected:shadow-sm">{{ $details['label'] }}</button>
                    @endforeach
                </div>
                @if ($cashflowTotal > 0)
                    <div data-breakdown-chart-region class="relative mx-auto mt-6 h-52 max-w-[260px]">
                        <canvas id="expense-chart" role="img" aria-label="Current month cashflow: income and expense movement">Current month breakdown totals are listed below.</canvas>
                        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center px-12 text-center">
                            <span data-breakdown-total-label class="text-xs text-slate-500">Total movement</span>
                            <span data-breakdown-total class="mt-1 max-w-full break-words text-base font-bold tabular-nums text-slate-900">Rp {{ number_format($cashflowTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif
                @foreach ($breakdownModes as $mode => $details)
                    @php $modeTotal = array_sum($details['chart']['values']); @endphp
                    <div id="breakdown-panel-{{ $mode }}" data-breakdown-panel="{{ $mode }}" role="tabpanel" aria-labelledby="breakdown-tab-{{ $mode }}" tabindex="0" @if ($mode !== 'cashflow') hidden @endif>
                        <p class="mt-5 text-xs leading-relaxed text-slate-500">{{ $details['description'] }}</p>
                        @if ($modeTotal > 0)
                            <ul class="mt-5 max-h-64 divide-y divide-slate-100 overflow-y-auto pr-1 text-sm" aria-label="{{ $details['label'] }} breakdown totals">
                                @foreach ($details['chart']['labels'] as $index => $label)
                                    <li class="py-3 first:pt-0">
                                        <div class="flex items-start justify-between gap-3">
                                            <span class="flex min-w-0 items-start gap-2.5 text-slate-600">
                                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full" style="background-color: {{ $details['colors'][$index % count($details['colors'])] }}" aria-hidden="true"></span>
                                                <span class="break-words [overflow-wrap:anywhere]">{{ $label }}</span>
                                            </span>
                                            <span class="shrink-0 text-xs text-slate-500">{{ round($details['chart']['values'][$index] / $modeTotal * 100) }}%</span>
                                        </div>
                                        <p class="mt-1 pl-[18px] text-sm font-semibold tabular-nums text-slate-800">Rp {{ number_format($details['chart']['values'][$index], 0, ',', '.') }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="mt-6 flex min-h-52 flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 px-5 py-8 text-center">
                                <p class="text-sm font-semibold text-slate-700">{{ $details['empty'] }}</p>
                                <p class="mt-2 max-w-[220px] text-xs leading-relaxed text-slate-500">{{ $mode === 'cashflow' ? 'Add a transaction to see money coming in and going out.' : 'Your category breakdown will appear here.' }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
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
                            <span @class(['flex h-10 w-10 shrink-0 items-center justify-center rounded-xl', 'bg-emerald-50 text-emerald-700' => $transaction->category->type === 'income', 'bg-rose-50 text-rose-700' => $transaction->category->type === 'expense'])>
                                <x-category-icon :name="$transaction->category->icon" class="h-5 w-5" />
                            </span>
                            <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                                <div class="min-w-0">
                                    <p class="break-words text-sm font-semibold text-slate-800 [overflow-wrap:anywhere] group-hover:text-brand-700">{{ $transaction->title }}</p>
                                    <p class="mt-1 break-words text-xs text-slate-500 [overflow-wrap:anywhere]">{{ $transaction->category->display_name }} <span class="mx-1 text-slate-300" aria-hidden="true">&middot;</span> <time datetime="{{ $transaction->transaction_date->format('Y-m-d') }}">{{ $transaction->transaction_date->format('d M Y') }}</time></p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2 sm:flex-col sm:items-end">
                                    <p @class(['break-words text-sm font-bold tabular-nums', 'text-emerald-700' => $transaction->category->type === 'income', 'text-rose-700' => $transaction->category->type === 'expense'])>{{ $transaction->category->type === 'income' ? '+' : '−' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                    <span class="text-[10px] font-medium text-slate-500">{{ ucfirst($transaction->category->type) }}</span>
                                </div>
                            </div>
                            <x-ui-icon name="arrow-right" class="hidden h-4 w-4 shrink-0 text-slate-300 group-hover:text-brand-700 sm:block" />
                        </a>
                    </li>
                @empty
                    <li class="px-5 py-12 text-center">
                        <p class="text-sm font-semibold text-slate-700">No transactions yet.</p>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500">Add your first transaction to start tracking your finances.</p>
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
        window.dashboardData = {{ Illuminate\Support\Js::from(['monthly' => $monthlyChart, 'expenses' => $expenseChart, 'income' => $incomeChart, 'cashflow' => $cashflowChart]) }};
    </script>
</x-app-layout>
