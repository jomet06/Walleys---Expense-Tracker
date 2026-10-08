<?php

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function createTestCategory(
    ?int $userId = null,
    string $name = 'Food',
    string $type = 'expense'
): Category {
    return Category::create([
        'user_id' => $userId,
        'name' => $name,
        'type' => $type,
        'icon' => 'others-icon',
    ]);
}

function createTestTransaction(
    User $user,
    Category $category,
    array $overrides = []
): Transaction {
    return Transaction::create(array_merge([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Test Transaction',
        'description' => null,
        'amount' => 50000,
        'transaction_date' => '2026-10-01',
        'receipt_image' => null,
    ], $overrides));
}

test('guests cannot access transactions', function () {
    $this
        ->get(route('transactions.index'))
        ->assertRedirect(route('login'));
});

test('user only sees their own transactions', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $category = createTestCategory();

    createTestTransaction($user, $category, [
        'title' => 'Visible Transaction',
    ]);

    createTestTransaction($otherUser, $category, [
        'title' => 'Hidden Transaction',
    ]);

    $this
        ->actingAs($user)
        ->get(route('transactions.index'))
        ->assertOk()
        ->assertSee('Visible Transaction')
        ->assertDontSee('Hidden Transaction');
});

test('user can create a transaction with a system category', function () {
    $user = User::factory()->create();
    $category = createTestCategory(null, 'Salary', 'income');

    $response = $this
        ->actingAs($user)
        ->post(route('transactions.store'), [
            'category_id' => $category->id,
            'title' => 'Monthly Salary',
            'description' => 'October salary',
            'amount' => 5000000,
            'transaction_date' => '2026-10-01',
        ]);

    $response
        ->assertRedirect(route('transactions.index'))
        ->assertSessionHas('status', 'transaction-created');

    $this->assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Monthly Salary',
        'amount' => 5000000,
    ]);

    $transaction = Transaction::query()
        ->where('user_id', $user->id)
        ->where('title', 'Monthly Salary')
        ->firstOrFail();

    expect($transaction->transaction_date->format('Y-m-d'))
        ->toBe('2026-10-01');
});

test('user can create a transaction with a receipt image', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = createTestCategory();

    $receipt = UploadedFile::fake()->create(
        'receipt.png',
        100,
        'image/png'
    );

    $this
        ->actingAs($user)
        ->post(route('transactions.store'), [
            'category_id' => $category->id,
            'title' => 'Lunch',
            'description' => null,
            'amount' => 45000,
            'transaction_date' => '2026-10-02',
            'receipt_image' => $receipt,
        ])
        ->assertRedirect(route('transactions.index'));

    $transaction = Transaction::where('user_id', $user->id)->firstOrFail();

    expect($transaction->receipt_image)->not->toBeNull();

    Storage::disk('public')->assertExists($transaction->receipt_image);
});

test('user cannot use another users custom category', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $otherCategory = createTestCategory(
        $otherUser->id,
        'Private Category',
        'expense'
    );

    $response = $this
        ->actingAs($user)
        ->post(route('transactions.store'), [
            'category_id' => $otherCategory->id,
            'title' => 'Invalid Transaction',
            'amount' => 10000,
            'transaction_date' => '2026-10-03',
        ]);

    $response->assertSessionHasErrors('category_id');

    $this->assertDatabaseCount('transactions', 0);
});

test('transaction input is validated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('transactions.store'), [
            'category_id' => 999999,
            'title' => str_repeat('a', 51),
            'amount' => 0,
            'transaction_date' => 'not-a-date',
        ]);

    $response->assertSessionHasErrors([
        'category_id',
        'title',
        'amount',
        'transaction_date',
    ]);

    $this->assertDatabaseCount('transactions', 0);
});

test('user can edit their own transaction', function () {
    $user = User::factory()->create();
    $category = createTestCategory();

    $transaction = createTestTransaction($user, $category);

    $this
        ->actingAs($user)
        ->get(route('transactions.edit', $transaction))
        ->assertOk()
        ->assertSee('Test Transaction');
});

test('user can update their own transaction', function () {
    $user = User::factory()->create();
    $category = createTestCategory();

    $transaction = createTestTransaction($user, $category);

    $this
        ->actingAs($user)
        ->patch(route('transactions.update', $transaction), [
            'category_id' => $category->id,
            'title' => 'Updated Transaction',
            'description' => 'Updated description',
            'amount' => 75000,
            'transaction_date' => '2026-10-04',
        ])
        ->assertRedirect(route('transactions.index'))
        ->assertSessionHas('status', 'transaction-updated');

    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'user_id' => $user->id,
        'title' => 'Updated Transaction',
        'amount' => 75000,
    ]);

    $transaction->refresh();

    expect($transaction->transaction_date->format('Y-m-d'))
        ->toBe('2026-10-04');
});

test('replacing a receipt deletes the old receipt', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = createTestCategory();

    $oldPath = UploadedFile::fake()
        ->create('old.png', 100, 'image/png')
        ->store('receipts', 'public');

    $transaction = createTestTransaction($user, $category, [
        'receipt_image' => $oldPath,
    ]);

    $newReceipt = UploadedFile::fake()->create(
        'new.png',
        100,
        'image/png'
    );

    $this
        ->actingAs($user)
        ->patch(route('transactions.update', $transaction), [
            'category_id' => $category->id,
            'title' => 'Updated Receipt',
            'amount' => 50000,
            'transaction_date' => '2026-10-05',
            'receipt_image' => $newReceipt,
        ])
        ->assertRedirect(route('transactions.index'));

    $transaction->refresh();

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($transaction->receipt_image);
});

test('deleting a transaction also deletes its receipt', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = createTestCategory();

    $receiptPath = UploadedFile::fake()
        ->create('receipt.png', 100, 'image/png')
        ->store('receipts', 'public');

    $transaction = createTestTransaction($user, $category, [
        'receipt_image' => $receiptPath,
    ]);

    $this
        ->actingAs($user)
        ->delete(route('transactions.destroy', $transaction))
        ->assertRedirect(route('transactions.index'))
        ->assertSessionHas('status', 'transaction-deleted');

    $this->assertDatabaseMissing('transactions', [
        'id' => $transaction->id,
    ]);

    Storage::disk('public')->assertMissing($receiptPath);
});

test('user cannot edit update or delete another users transaction', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();

    $category = createTestCategory();

    $transaction = createTestTransaction($owner, $category, [
        'title' => 'Owners Transaction',
    ]);

    $this
        ->actingAs($user)
        ->get(route('transactions.edit', $transaction))
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->patch(route('transactions.update', $transaction), [
            'category_id' => $category->id,
            'title' => 'Hijacked Transaction',
            'amount' => 10000,
            'transaction_date' => '2026-10-06',
        ])
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->delete(route('transactions.destroy', $transaction))
        ->assertForbidden();

    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'user_id' => $owner->id,
        'title' => 'Owners Transaction',
    ]);
});

test('transactions can be filtered by date range', function () {
    $user = User::factory()->create();
    $category = createTestCategory();

    createTestTransaction($user, $category, [
        'title' => 'Old Transaction',
        'transaction_date' => '2026-09-01',
    ]);

    createTestTransaction($user, $category, [
        'title' => 'October Transaction',
        'transaction_date' => '2026-10-05',
    ]);

    $this
        ->actingAs($user)
        ->get(route('transactions.index', [
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
        ]))
        ->assertOk()
        ->assertSee('October Transaction')
        ->assertDontSee('Old Transaction');
});

test('transactions can be filtered by type and category', function () {
    $user = User::factory()->create();

    $incomeCategory = createTestCategory(null, 'Salary', 'income');
    $expenseCategory = createTestCategory(null, 'Food', 'expense');

    createTestTransaction($user, $incomeCategory, [
        'title' => 'Salary Payment',
        'amount' => 5000000,
    ]);

    createTestTransaction($user, $expenseCategory, [
        'title' => 'Dinner',
        'amount' => 100000,
    ]);

    $this
        ->actingAs($user)
        ->get(route('transactions.index', [
            'type' => 'income',
        ]))
        ->assertOk()
        ->assertSee('Salary Payment')
        ->assertDontSee('Dinner');

    $this
        ->actingAs($user)
        ->get(route('transactions.index', [
            'category_id' => $expenseCategory->id,
        ]))
        ->assertOk()
        ->assertSee('Dinner')
        ->assertDontSee('Salary Payment');
});

test('transaction pagination preserves filter query string', function () {
    $user = User::factory()->create();
    $category = createTestCategory(null, 'Salary', 'income');

    foreach (range(1, 11) as $number) {
        createTestTransaction($user, $category, [
            'title' => 'Income '.$number,
            'transaction_date' => '2026-10-01',
        ]);
    }

    $response = $this
        ->actingAs($user)
        ->get(route('transactions.index', [
            'type' => 'income',
        ]));

    $response
        ->assertOk()
        ->assertSee('type=income', false);
});

test('category cannot be deleted while it is used by a transaction', function () {
    $user = User::factory()->create();

    $category = createTestCategory(
        $user->id,
        'Custom Expense',
        'expense'
    );

    createTestTransaction($user, $category);

    $this
        ->actingAs($user)
        ->delete(route('categories.destroy', $category))
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('error', 'category-in-use');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
    ]);

    $this->assertDatabaseHas('transactions', [
        'category_id' => $category->id,
    ]);
});
