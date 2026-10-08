<?php

namespace App\Http\Controllers;

use App\Services\MonthlyCategorySummary;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, MonthlyCategorySummary $analytics): View
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
            ->select('transaction_date', 'categories.type')
            ->selectRaw('SUM(transactions.amount) as total')
            ->groupBy('transaction_date', 'categories.type')
            ->get();

        $currentKey = $currentMonth->format('Y-m');

        foreach ($totals as $total) {
            $key = $total->transaction_date->format('Y-m');
            $amount = (int) $total->total;
            $months[$key][$total->type] += $amount;
        }

        $insights = $analytics->forMonth($request->user(), $currentMonth);
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
            'expenseChart' => $insights['expenseChart'],
            'incomeChart' => $insights['incomeChart'],
            'cashflowChart' => $insights['cashflowChart'],
            'recentTransactions' => $request->user()->transactions()
                ->with('category')
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
        ]);
    }
}
