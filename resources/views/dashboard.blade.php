<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Financial dashboard</h2>
                <p class="mt-1 text-sm text-gray-500">Your money at a glance · {{ $monthLabel }}</p>
            </div>
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Add transaction</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-3">
                @foreach (['income' => 'Total Income', 'expense' => 'Total Expense', 'balance' => 'Net Balance'] as $key => $label)
                    <section class="min-w-0 rounded-xl bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500">{{ $label }}</h3>
                        <p @class(['mt-3 break-words text-2xl font-semibold tabular-nums', 'text-emerald-700' => $key === 'income', 'text-rose-700' => $key === 'expense' || ($key === 'balance' && $summary[$key] < 0), 'text-indigo-700' => $key === 'balance' && $summary[$key] >= 0])>Rp {{ number_format($summary[$key], 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs text-gray-500">{{ $monthLabel }}</p>
                    </section>
                @endforeach
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <section class="min-w-0 rounded-xl bg-white p-4 shadow-sm sm:p-6 lg:col-span-2">
                    <h3 class="font-semibold text-gray-900">Income &amp; expenses</h3>
                    <p class="mt-1 text-sm text-gray-500">Last six calendar months</p>
                    <div class="relative mt-6 h-72 w-full">
                        <canvas id="monthly-chart" role="img" aria-label="Monthly income and expenses for the last six months">Monthly income and expense totals are listed below.</canvas>
                    </div>
                    <details class="mt-4 text-sm text-gray-600">
                        <summary class="cursor-pointer">View monthly totals</summary>
                        <ul class="mt-3 space-y-2">
                            @foreach ($monthlyChart['labels'] as $index => $label)
                                <li class="break-words">{{ $label }}: Income Rp {{ number_format($monthlyChart['income'][$index], 0, ',', '.') }} · Expenses Rp {{ number_format($monthlyChart['expense'][$index], 0, ',', '.') }}</li>
                            @endforeach
                        </ul>
                    </details>
                </section>
                <section class="min-w-0 rounded-xl bg-white p-4 shadow-sm sm:p-6">
                    <h3 class="font-semibold text-gray-900">Expense breakdown</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $monthLabel }} by category</p>
                    @if (count($expenseChart['values']))
                        <div class="relative mt-6 h-64 w-full">
                            <canvas id="expense-chart" role="img" aria-label="Current month expenses by category">Expense totals by category are listed below.</canvas>
                        </div>
                        <ul class="mt-4 space-y-2 text-sm">
                            @foreach ($expenseChart['labels'] as $index => $label)
                                <li class="flex flex-wrap justify-between gap-2"><span class="min-w-0 break-all text-gray-600">{{ $label }}</span><span class="text-gray-900">Rp {{ number_format($expenseChart['values'][$index], 0, ',', '.') }}</span></li>
                            @endforeach
                        </ul>
                    @else
                        <div class="flex h-64 items-center justify-center text-center text-sm text-gray-500">No expenses this month. Your category breakdown will appear here.</div>
                    @endif
                </section>
            </div>

            <section class="min-w-0 overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-900">Recent transactions</h3>
                    <a href="{{ route('transactions.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">View transaction history &rarr;</a>
                </div>
                <ul class="divide-y divide-gray-100">
                    @forelse ($recentTransactions as $transaction)
                        <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                            <div class="min-w-0">
                                <p class="break-all font-medium text-gray-900">{{ $transaction->title }}</p>
                                <p class="mt-1 break-all text-sm text-gray-500"><time datetime="{{ $transaction->transaction_date->format('Y-m-d') }}">{{ $transaction->transaction_date->format('d M Y') }}</time> · {{ $transaction->category->name }}</p>
                            </div>
                            <div class="shrink-0">
                                <span @class(['inline-flex rounded-full px-2 py-1 text-xs font-medium', 'bg-emerald-50 text-emerald-700' => $transaction->category->type === 'income', 'bg-rose-50 text-rose-700' => $transaction->category->type === 'expense'])>{{ ucfirst($transaction->category->type) }}</span>
                                <p class="mt-1 break-words text-sm font-semibold tabular-nums text-gray-900">{{ $transaction->category->type === 'income' ? '+' : '−' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="p-8 text-center text-sm text-gray-500">No transactions yet. Add your first transaction to start tracking your finances.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
    <script>
        window.dashboardData = {{ Illuminate\Support\Js::from(['monthly' => $monthlyChart, 'expenses' => $expenseChart]) }};
    </script>
</x-app-layout>
