<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('transaction amounts fit the existing MySQL integer column', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Food', 'type' => 'expense', 'icon' => 'food-icon']);
    $this->actingAs($user)->post(route('transactions.store'), [
        'category_id' => $category->id, 'title' => 'Overflow',
        'amount' => 2147483648, 'transaction_date' => '2026-10-08',
    ])->assertSessionHasErrors('amount');
    $this->assertDatabaseCount('transactions', 0);
});

test('account deletion removes owned receipts and preserves other users files', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $other = User::factory()->create();
    $category = Category::create(['name' => 'Food', 'type' => 'expense', 'icon' => 'food-icon']);
    $ownReceipt = UploadedFile::fake()->create('own.png', 10, 'image/png')->store('receipts', 'public');
    $otherReceipt = UploadedFile::fake()->create('other.png', 10, 'image/png')->store('receipts', 'public');
    foreach ([[$user, $ownReceipt], [$other, $otherReceipt]] as [$owner, $receipt]) {
        $owner->transactions()->create([
            'category_id' => $category->id, 'title' => 'Receipt', 'amount' => 100,
            'transaction_date' => '2026-10-08', 'receipt_image' => $receipt,
        ]);
    }
    $this->actingAs($user)->delete(route('profile.destroy'), ['password' => 'password'])->assertRedirect('/');
    Storage::disk('public')->assertMissing($ownReceipt);
    Storage::disk('public')->assertExists($otherReceipt);
    $this->assertDatabaseCount('transactions', 1);
});
