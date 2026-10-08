<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests cannot access categories', function () {
    $response = $this->get(route('categories.index'));

    $response->assertRedirect(route('login'));
});

test('user can see system categories and own custom categories but not another users categories', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Category::create([
        'user_id' => null,
        'name' => 'Salary',
        'type' => 'income',
        'icon' => 'salary-icon',
    ]);

    Category::create([
        'user_id' => $user->id,
        'name' => 'Freelance',
        'type' => 'income',
        'icon' => 'briefcase-icon',
    ]);

    Category::create([
        'user_id' => $otherUser->id,
        'name' => 'Secret Category',
        'type' => 'expense',
        'icon' => 'others-icon',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('categories.index'));

    $response
        ->assertOk()
        ->assertSee('Salary')
        ->assertSee('Freelance')
        ->assertDontSee('Secret Category');
});

test('user can create a custom category and ownership is assigned automatically', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('categories.store'), [
            'user_id' => $otherUser->id,
            'name' => 'Freelance',
            'type' => 'income',
            'icon' => 'briefcase-icon',
        ]);

    $response
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('status', 'category-created');

    $this->assertDatabaseHas('categories', [
        'user_id' => $user->id,
        'name' => 'Freelance',
        'type' => 'income',
        'icon' => 'briefcase-icon',
    ]);

    $this->assertDatabaseMissing('categories', [
        'user_id' => $otherUser->id,
        'name' => 'Freelance',
    ]);
});

test('category input is validated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('categories.create'))
        ->post(route('categories.store'), [
            'name' => str_repeat('a', 33),
            'type' => 'invalid-type',
            'icon' => '',
        ]);

    $response
        ->assertRedirect(route('categories.create'))
        ->assertSessionHasErrors([
            'name',
            'type',
            'icon',
        ]);

    $this->assertDatabaseCount('categories', 0);
});

test('user can edit their own category', function () {
    $user = User::factory()->create();

    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Freelance',
        'type' => 'income',
        'icon' => 'briefcase-icon',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('categories.edit', $category));

    $response
        ->assertOk()
        ->assertSee('Freelance');
});

test('user can update their own category', function () {
    $user = User::factory()->create();

    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Freelance',
        'type' => 'income',
        'icon' => 'briefcase-icon',
    ]);

    $response = $this
        ->actingAs($user)
        ->patch(route('categories.update', $category), [
            'name' => 'Side Project',
            'type' => 'income',
            'icon' => 'investments-icon',
        ]);

    $response
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('status', 'category-updated');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'user_id' => $user->id,
        'name' => 'Side Project',
        'type' => 'income',
        'icon' => 'investments-icon',
    ]);
});

test('user can delete their own category', function () {
    $user = User::factory()->create();

    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Freelance',
        'type' => 'income',
        'icon' => 'briefcase-icon',
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(route('categories.destroy', $category));

    $response
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('status', 'category-deleted');

    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});

test('user cannot modify a system category', function () {
    $user = User::factory()->create();

    $category = Category::create([
        'user_id' => null,
        'name' => 'Salary',
        'type' => 'income',
        'icon' => 'salary-icon',
    ]);

    $this
        ->actingAs($user)
        ->get(route('categories.edit', $category))
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->patch(route('categories.update', $category), [
            'name' => 'Changed Salary',
            'type' => 'income',
            'icon' => 'salary-icon',
        ])
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->delete(route('categories.destroy', $category))
        ->assertForbidden();

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Salary',
        'user_id' => null,
    ]);
});

test('user cannot modify another users category', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();

    $category = Category::create([
        'user_id' => $owner->id,
        'name' => 'Owners Category',
        'type' => 'expense',
        'icon' => 'others-icon',
    ]);

    $this
        ->actingAs($user)
        ->get(route('categories.edit', $category))
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->patch(route('categories.update', $category), [
            'name' => 'Hijacked Category',
            'type' => 'income',
            'icon' => 'salary-icon',
        ])
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->delete(route('categories.destroy', $category))
        ->assertForbidden();

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'user_id' => $owner->id,
        'name' => 'Owners Category',
    ]);
});
