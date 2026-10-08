<?php

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $this->travelTo(CarbonImmutable::parse('2026-10-08 12:00:00'));
    $this->user = User::factory()->create();
    $this->incomeCategory = Category::create(['name' => 'Salary', 'type' => 'income', 'icon' => 'salary-icon']);
    $this->expenseCategory = Category::create(['user_id' => $this->user->id, 'name' => 'Food', 'type' => 'expense', 'icon' => 'food-icon']);
});

afterEach(function () {
    $this->travelBack();
});

function dashboardTransaction(User $user, Category $category, int $amount = 100, string $date = '2026-10-08', array $overrides = []): Transaction
{
    return $user->transactions()->create(array_merge([
        'category_id' => $category->id,
        'title' => 'Dashboard transaction',
        'amount' => $amount,
        'transaction_date' => $date,
    ], $overrides));
}

test('dashboard redirects guests to login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated user can open the financial dashboard', function () {
    $this->actingAs($this->user)->get(route('dashboard'))->assertOk()
        ->assertSee('Financial dashboard')->assertSee('October 2026')
        ->assertSee(route('transactions.index'))->assertSee(route('transactions.create'));
});

test('monthly income expense and net balance use integer totals', function () {
    dashboardTransaction($this->user, $this->incomeCategory, 5000000);
    dashboardTransaction($this->user, $this->incomeCategory, 1250000);
    dashboardTransaction($this->user, $this->expenseCategory, 750000);
    dashboardTransaction($this->user, $this->expenseCategory, 50000);

    $this->actingAs($this->user)->get(route('dashboard'))->assertOk()
        ->assertViewHas('summary', ['income' => 6250000, 'expense' => 800000, 'balance' => 5450000])
        ->assertSee('Rp 6.250.000')->assertSee('Rp 800.000')->assertSee('Rp 5.450.000');
});

test('summary uses transaction dates and includes the whole calendar month', function () {
    dashboardTransaction($this->user, $this->incomeCategory, 100, '2026-10-01', ['created_at' => '2026-09-01']);
    dashboardTransaction($this->user, $this->incomeCategory, 200, '2026-10-31');
    dashboardTransaction($this->user, $this->incomeCategory, 1000, '2026-09-30');
    dashboardTransaction($this->user, $this->expenseCategory, 1000, '2026-11-01');

    $this->actingAs($this->user)->get(route('dashboard'))
        ->assertViewHas('summary', ['income' => 300, 'expense' => 0, 'balance' => 300]);
});

test('all dashboard datasets exclude other users transactions even in shared categories', function () {
    $other = User::factory()->create();
    dashboardTransaction($this->user, $this->incomeCategory, 100);
    dashboardTransaction($other, $this->incomeCategory, 9000);
    dashboardTransaction($other, $this->expenseCategory, 9000, '2026-10-08', ['title' => 'Secret transaction']);

    $this->actingAs($this->user)->get(route('dashboard'))
        ->assertViewHas('summary', ['income' => 100, 'expense' => 0, 'balance' => 100])
        ->assertViewHas('monthlyChart', fn ($data) => $data['income'] === [0, 0, 0, 0, 0, 100] && $data['expense'] === [0, 0, 0, 0, 0, 0])
        ->assertViewHas('expenseChart', ['labels' => [], 'values' => []])
        ->assertViewHas('recentTransactions', fn ($items) => $items->count() === 1 && $items->every(fn ($item) => $item->user_id === $this->user->id))
        ->assertDontSee('Secret transaction');
});

test('six month chart is chronological aggregates totals and fills missing months', function () {
    dashboardTransaction($this->user, $this->incomeCategory, 100, '2026-05-01');
    dashboardTransaction($this->user, $this->incomeCategory, 200, '2026-05-31');
    dashboardTransaction($this->user, $this->expenseCategory, 50, '2026-07-01');
    dashboardTransaction($this->user, $this->incomeCategory, 400, '2026-10-31');
    dashboardTransaction($this->user, $this->incomeCategory, 9999, '2026-04-30');
    dashboardTransaction($this->user, $this->expenseCategory, 9999, '2026-11-01');

    $this->actingAs($this->user)->get(route('dashboard'))->assertViewHas('monthlyChart', [
        'labels' => ['May 2026', 'Jun 2026', 'Jul 2026', 'Aug 2026', 'Sep 2026', 'Oct 2026'],
        'income' => [300, 0, 0, 0, 0, 400],
        'expense' => [0, 0, 50, 0, 0, 0],
    ]);
});

test('six month chart crosses year boundaries without month overflow', function () {
    $this->travelTo(CarbonImmutable::parse('2027-01-31 12:00:00'));
    dashboardTransaction($this->user, $this->incomeCategory, 100, '2026-08-01');
    dashboardTransaction($this->user, $this->expenseCategory, 50, '2027-01-31');
    $this->actingAs($this->user)->get(route('dashboard'))->assertViewHas('monthlyChart', [
        'labels' => ['Aug 2026', 'Sep 2026', 'Oct 2026', 'Nov 2026', 'Dec 2026', 'Jan 2027'],
        'income' => [100, 0, 0, 0, 0, 0], 'expense' => [0, 0, 0, 0, 0, 50],
    ]);
});

test('expense breakdown groups by category identity and excludes income and other months', function () {
    $second = Category::create(['user_id' => $this->user->id, 'name' => 'Food', 'type' => 'expense', 'icon' => 'food-icon']);
    dashboardTransaction($this->user, $this->expenseCategory, 100);
    dashboardTransaction($this->user, $this->expenseCategory, 200, '2026-10-01');
    dashboardTransaction($this->user, $second, 50);
    dashboardTransaction($this->user, $this->incomeCategory, 10000);
    dashboardTransaction($this->user, $this->expenseCategory, 900, '2026-09-30');
    $this->actingAs($this->user)->get(route('dashboard'))
        ->assertViewHas('expenseChart', ['labels' => ['Food', 'Food'], 'values' => [300, 50]]);
});

test('recent transactions are limited to five ordered by transaction date then id with eager categories', function () {
    $ids = [];
    foreach (range(1, 7) as $day) {
        $ids[] = dashboardTransaction($this->user, $this->expenseCategory, 100, sprintf('2026-10-%02d', $day))->id;
    }
    $latest = dashboardTransaction($this->user, $this->incomeCategory, 200, '2026-10-07');
    $this->actingAs($this->user)->get(route('dashboard'))->assertViewHas('recentTransactions', function ($items) use ($ids, $latest) {
        return $items->pluck('id')->all() === [$latest->id, $ids[6], $ids[5], $ids[4], $ids[3]]
            && $items->every(fn ($item) => $item->relationLoaded('category'));
    });
});

test('empty dashboard has zero totals and appropriate empty states', function () {
    $this->actingAs($this->user)->get(route('dashboard'))->assertOk()
        ->assertViewHas('summary', ['income' => 0, 'expense' => 0, 'balance' => 0])
        ->assertViewHas('monthlyChart', fn ($data) => $data['income'] === array_fill(0, 6, 0) && $data['expense'] === array_fill(0, 6, 0))
        ->assertViewHas('expenseChart', ['labels' => [], 'values' => []])
        ->assertSee('No expenses this month.')->assertSee('No transactions yet.')
        ->assertDontSee('id="expense-chart"', false);
});

test('negative net balances display correctly', function () {
    dashboardTransaction($this->user, $this->expenseCategory, 150000);
    $this->actingAs($this->user)->get(route('dashboard'))
        ->assertViewHas('summary', ['income' => 0, 'expense' => 150000, 'balance' => -150000])
        ->assertSee('Rp -150.000');
});

test('category labels and transaction titles are escaped in HTML and chart serialization', function () {
    $label = '</script><script>alert(1)';
    $this->expenseCategory->update(['name' => $label]);
    dashboardTransaction($this->user, $this->expenseCategory, 100, '2026-10-08', ['title' => '<img src=x onerror=alert(1)>']);
    $this->actingAs($this->user)->get(route('dashboard'))->assertOk()
        ->assertViewHas('expenseChart', ['labels' => [$label], 'values' => [100]])
        ->assertSee(e($label), false)->assertDontSee($label, false)
        ->assertDontSee('<img src=x onerror=alert(1)>', false)
        ->assertSee('window.dashboardData = JSON.parse', false);
});
