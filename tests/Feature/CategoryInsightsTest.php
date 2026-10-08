<?php

use App\Models\Category;
use App\Models\User;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $this->travelTo(CarbonImmutable::parse('2026-10-08 12:00:00'));
    $this->user = User::factory()->create();
    $this->food = Category::create(['name' => 'Food', 'type' => 'expense', 'icon' => 'foodbeverage-icon']);
    $this->rent = Category::create(['user_id' => $this->user->id, 'name' => 'Rent', 'type' => 'expense', 'icon' => 'others-icon']);
    $this->salary = Category::create(['name' => 'Salary', 'type' => 'income', 'icon' => 'salary-icon']);
});

afterEach(function () {
    $this->travelBack();
});

function insightTransaction(User $user, Category $category, int $amount, string $date = '2026-10-08', array $extra = []): void
{
    $user->transactions()->create(array_merge([
        'category_id' => $category->id, 'title' => 'Insight transaction',
        'amount' => $amount, 'transaction_date' => $date,
    ], $extra));
}

test('category insights show integer monthly totals counts and expense shares', function () {
    insightTransaction($this->user, $this->food, 150000);
    insightTransaction($this->user, $this->food, 50000);
    insightTransaction($this->user, $this->rent, 500000);
    insightTransaction($this->user, $this->salary, 5000000);

    $this->actingAs($this->user)->get(route('categories.index'))->assertOk()
        ->assertViewHas('categorySummary', ['income' => 5000000, 'expense' => 700000, 'balance' => 4300000, 'transactionCount' => 4])
        ->assertViewHas('categoryInsights', function ($rows) {
            $food = $rows->firstWhere('id', $this->food->id);
            $rent = $rows->firstWhere('id', $this->rent->id);

            return $food['total'] === 200000 && $food['transactionCount'] === 2 && $food['share'] === 28.6
                && $rent['total'] === 500000 && $rent['share'] === 71.4;
        })->assertSee('Rp 700.000')->assertSee('Rp 500.000');
});

test('category insights exclude other users transactions and private category names', function () {
    $other = User::factory()->create();
    $private = Category::create(['user_id' => $other->id, 'name' => 'Private spending', 'type' => 'expense', 'icon' => 'others-icon']);
    insightTransaction($this->user, $this->food, 200);
    insightTransaction($other, $this->food, 9999);
    insightTransaction($other, $private, 9999);
    $this->actingAs($this->user)->get(route('categories.index'))->assertOk()
        ->assertViewHas('categorySummary', ['income' => 0, 'expense' => 200, 'balance' => -200, 'transactionCount' => 1])
        ->assertViewHas('categoryInsights', fn ($rows) => $rows->count() === 3 && $rows->firstWhere('id', $this->food->id)['total'] === 200)
        ->assertDontSee('Private spending');
});

test('category insights use transaction date and include both calendar month boundaries', function () {
    insightTransaction($this->user, $this->food, 100, '2026-10-01', ['created_at' => '2026-08-01']);
    insightTransaction($this->user, $this->food, 200, '2026-10-31');
    insightTransaction($this->user, $this->food, 999, '2026-09-30', ['created_at' => '2026-10-08']);
    insightTransaction($this->user, $this->salary, 999, '2026-11-01');
    $this->actingAs($this->user)->get(route('categories.index'))
        ->assertViewHas('categorySummary', ['income' => 0, 'expense' => 300, 'balance' => -300, 'transactionCount' => 2])
        ->assertViewHas('monthStart', '2026-10-01')->assertViewHas('monthEnd', '2026-10-31');
});

test('category insights keep zero categories and empty totals without invalid percentages', function () {
    $this->actingAs($this->user)->get(route('categories.index'))->assertOk()
        ->assertViewHas('categorySummary', ['income' => 0, 'expense' => 0, 'balance' => 0, 'transactionCount' => 0])
        ->assertViewHas('categoryInsights', fn ($rows) => $rows->count() === 3 && $rows->every(fn ($row) => $row['total'] === 0 && $row['transactionCount'] === 0 && $row['share'] === 0.0));
});

test('category insights rank expenses by amount and keep equal labels as distinct identities', function () {
    $secondFood = Category::create(['user_id' => $this->user->id, 'name' => 'Food', 'type' => 'expense', 'icon' => 'others-icon']);
    insightTransaction($this->user, $this->food, 100);
    insightTransaction($this->user, $secondFood, 300);
    insightTransaction($this->user, $this->rent, 200);
    $this->actingAs($this->user)->get(route('categories.index'))->assertViewHas('categoryInsights', function ($rows) use ($secondFood) {
        return $rows->where('type', 'expense')->pluck('id')->all() === [$secondFood->id, $this->rent->id, $this->food->id]
            && $rows->firstWhere('id', $secondFood->id)['total'] === 300
            && $rows->firstWhere('id', $this->food->id)['total'] === 100;
    });
});

test('dashboard provides income expense and positive cashflow movement datasets', function () {
    insightTransaction($this->user, $this->salary, 500);
    insightTransaction($this->user, $this->food, 700);
    insightTransaction(User::factory()->create(), $this->salary, 9999);
    $this->actingAs($this->user)->get(route('dashboard'))->assertOk()
        ->assertViewHas('incomeChart', ['labels' => ['Salary'], 'values' => [500]])
        ->assertViewHas('expenseChart', ['labels' => ['Food'], 'values' => [700]])
        ->assertViewHas('cashflowChart', ['labels' => ['Income', 'Expenses'], 'values' => [500, 700]])
        ->assertViewHas('summary', ['income' => 500, 'expense' => 700, 'balance' => -200])
        ->assertSee('Cashflow')->assertSee('Income')->assertSee('Expenses');
});

test('income only accounts still have a usable dashboard with empty expense data', function () {
    insightTransaction($this->user, $this->salary, 500);
    $this->actingAs($this->user)->get(route('dashboard'))->assertOk()
        ->assertViewHas('cashflowChart', ['labels' => ['Income', 'Expenses'], 'values' => [500, 0]])
        ->assertViewHas('expenseChart', ['labels' => [], 'values' => []])
        ->assertSee('No expenses this month.');
});

test('category insights cover leap day and provide safe monthly transaction links', function () {
    $this->travelTo(CarbonImmutable::parse('2028-02-29 12:00:00'));
    insightTransaction($this->user, $this->food, 100, '2028-02-29');
    insightTransaction($this->user, $this->food, 999, '2028-03-01');
    $this->actingAs($this->user)->get(route('categories.index'))->assertOk()
        ->assertViewHas('monthStart', '2028-02-01')->assertViewHas('monthEnd', '2028-02-29')
        ->assertViewHas('categorySummary', ['income' => 0, 'expense' => 100, 'balance' => -100, 'transactionCount' => 1])
        ->assertSee('start_date=2028-02-01', false)->assertSee('end_date=2028-02-29', false);
});
