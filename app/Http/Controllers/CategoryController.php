<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display the categories available to the authenticated user.
     */
    public function index(Request $request): View
    {
        $systemCategories = Category::query()
            ->whereNull('user_id')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $customCategories = $request->user()
            ->categories()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('categories.index', [
            'systemCategories' => $systemCategories,
            'customCategories' => $customCategories,
        ]);
    }

    /**
     * Show the form for creating a custom category.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a new custom category.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $request->user()
            ->categories()
            ->create($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('status', 'category-created');
    }

    /**
     * Show the form for editing a custom category.
     */
    public function edit(Request $request, Category $category): View
    {
        $this->ensureOwnedByAuthenticatedUser($request, $category);

        return view('categories.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update a custom category.
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $this->ensureOwnedByAuthenticatedUser($request, $category);

        $category->update($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('status', 'category-updated');
    }

    /**
     * Delete a custom category.
     */
    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $this->ensureOwnedByAuthenticatedUser($request, $category);

        if ($category->transactions()->exists()) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'category-in-use');
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('status', 'category-deleted');
    }

    /**
     * Ensure that only the owner of a custom category may modify it.
     */
    private function ensureOwnedByAuthenticatedUser(Request $request, Category $category): void
    {
        abort_if(
            $category->user_id === null
            || (int) $category->user_id !== (int) $request->user()->id,
            403
        );
    }
}
