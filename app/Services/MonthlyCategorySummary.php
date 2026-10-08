<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class MonthlyCategorySummary
{
    public function forMonth(User $user, CarbonImmutable $month): array
    {
        $month = $month->startOfMonth();
        $categories = Category::query()
            ->where(fn ($query) => $query->whereNull('user_id')->orWhere('user_id', $user->id))
            ->orderBy('name')->orderBy('id')->get();

        $totals = $user->transactions()
            ->whereIn('category_id', $categories->modelKeys())
            ->where('transaction_date', '>=', $month->toDateString())
            ->where('transaction_date', '<', $month->addMonth()->toDateString())
            ->select('category_id')
            ->selectRaw('SUM(amount) as total, COUNT(*) as transaction_count')
            ->groupBy('category_id')->get()->keyBy('category_id');

        $rows = $categories->map(fn (Category $category) => [
            'id' => $category->id,
            'name' => $category->name,
            'type' => $category->type,
            'icon' => $category->icon,
            'total' => (int) ($totals->get($category->id)?->total ?? 0),
            'transactionCount' => (int) ($totals->get($category->id)?->transaction_count ?? 0),
            'isSystem' => $category->user_id === null,
        ])->sort(fn ($a, $b) => $b['total'] <=> $a['total']
            ?: strcmp($a['name'], $b['name']) ?: $a['id'] <=> $b['id'])->values();

        $income = (int) $rows->where('type', 'income')->sum('total');
        $expense = (int) $rows->where('type', 'expense')->sum('total');
        $rows = $rows->map(function (array $row) use ($income, $expense) {
            $typeTotal = $row['type'] === 'income' ? $income : $expense;
            $row['share'] = $typeTotal > 0 ? round($row['total'] / $typeTotal * 100, 1) : 0.0;

            return $row;
        });

        return [
            'monthLabel' => $month->format('F Y'),
            'monthStart' => $month->toDateString(),
            'monthEnd' => $month->endOfMonth()->toDateString(),
            'rows' => $rows,
            'summary' => [
                'income' => $income, 'expense' => $expense, 'balance' => $income - $expense,
                'transactionCount' => (int) $rows->sum('transactionCount'),
            ],
            'incomeChart' => $this->chart($rows, 'income'),
            'expenseChart' => $this->chart($rows, 'expense'),
            // Cashflow compares positive incoming/outgoing movement, never signed net balance.
            'cashflowChart' => ['labels' => ['Income', 'Expenses'], 'values' => [$income, $expense]],
            'categories' => $categories,
        ];
    }

    private function chart(Collection $rows, string $type): array
    {
        $data = $rows->filter(fn ($row) => $row['type'] === $type && $row['total'] > 0)->values();

        return ['labels' => $data->pluck('name')->all(), 'values' => $data->pluck('total')->all()];
    }
}
