<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $currentMonth = CarbonImmutable::now()->startOfMonth();
        $firstMonth = $currentMonth->subMonths(5);
        $months = [];

        for ($offset = 0; $offset < 6; $offset++) {
            $month = $firstMonth->addMonths($offset);
            $months[$month->format('Y-m')] = [
                'label' => $month->format('M Y'),
                'income' => 0,
                'expense' => 0,
            ];
        }

        // Date/category aggregation uses SQL supported by both MySQL and SQLite.
        // Keep the user relationship constraint on every transaction query.
        $totals = $request->user()->transactions()
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->where('transaction_date', '>=', $firstMonth->toDateString())
            ->where('transaction_date', '<', $currentMonth->addMonth()->toDateString())
            ->select('transaction_date', 'category_id', 'categories.name', 'categories.type')
            ->selectRaw('SUM(transactions.amount) as total')
            ->groupBy('transaction_date', 'category_id', 'categories.name', 'categories.type')
            ->get();

        $breakdown = [];
        $currentKey = $currentMonth->format('Y-m');

        foreach ($totals as $total) {
            $key = $total->transaction_date->format('Y-m');
            $amount = (int) $total->total;
            $months[$key][$total->type] += $amount;

            if ($key === $currentKey && $total->type === 'expense') {
                $categoryId = $total->category_id;
                $breakdown[$categoryId] ??= ['label' => $total->name, 'amount' => 0];
                $breakdown[$categoryId]['amount'] += $amount;
            }
        }

        $breakdown = collect($breakdown)->sortByDesc('amount')->values();
        $income = $months[$currentKey]['income'];
        $expense = $months[$currentKey]['expense'];

        return view('dashboard', [
            'monthLabel' => $currentMonth->format('F Y'),
            'summary' => ['income' => $income, 'expense' => $expense, 'balance' => $income - $expense],
            'monthlyChart' => [
                'labels' => array_column($months, 'label'),
                'income' => array_column($months, 'income'),
                'expense' => array_column($months, 'expense'),
            ],
            'expenseChart' => [
                'labels' => $breakdown->pluck('label')->all(),
                'values' => $breakdown->pluck('amount')->all(),
            ],
            'recentTransactions' => $request->user()->transactions()
                ->with('category')
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
        ]);
    }
}
