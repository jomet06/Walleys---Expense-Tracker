<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class TransactionController extends Controller
{
    /**
     * Display the authenticated user's transactions.
     */
    public function index(Request $request): View
    {
        $categories = $this->availableCategories($request);

        $filters = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'type' => ['nullable', Rule::in(['income', 'expense'])],
            'category_id' => [
                'nullable',
                'integer',
                Rule::in($categories->pluck('id')->all()),
            ],
        ]);

        $transactions = $request->user()
            ->transactions()
            ->with('category')
            ->when(
                $filters['start_date'] ?? null,
                fn ($query, $startDate) => $query->whereDate('transaction_date', '>=', $startDate)
            )
            ->when(
                $filters['end_date'] ?? null,
                fn ($query, $endDate) => $query->whereDate('transaction_date', '<=', $endDate)
            )
            ->when(
                $filters['type'] ?? null,
                fn ($query, $type) => $query->whereHas(
                    'category',
                    fn ($categoryQuery) => $categoryQuery->where('type', $type)
                )
            )
            ->when(
                $filters['category_id'] ?? null,
                fn ($query, $categoryId) => $query->where('category_id', $categoryId)
            )
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('transactions.index', [
            'transactions' => $transactions,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a transaction.
     */
    public function create(Request $request): View
    {
        return view('transactions.create', [
            'categories' => $this->availableCategories($request),
        ]);
    }

    /**
     * Store a new transaction.
     */
    public function store(TransactionRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('receipt_image');

        if ($request->hasFile('receipt_image')) {
            $data['receipt_image'] = $request
                ->file('receipt_image')
                ->store('receipts', 'public');
        }

        $request->user()
            ->transactions()
            ->create($data);

        return redirect()
            ->route('transactions.index')
            ->with('status', 'transaction-created');
    }

    /**
     * Show the form for editing a transaction.
     */
    public function edit(Request $request, Transaction $transaction): View
    {
        $this->ensureOwnedByAuthenticatedUser($request, $transaction);

        return view('transactions.edit', [
            'transaction' => $transaction,
            'categories' => $this->availableCategories($request),
        ]);
    }

    /**
     * Update a transaction.
     */
    public function update(
        TransactionRequest $request,
        Transaction $transaction
    ): RedirectResponse {
        $data = $request->safe()->except('receipt_image');

        $oldReceipt = $transaction->receipt_image;
        $newReceipt = null;

        if ($request->hasFile('receipt_image')) {
            $newReceipt = $request
                ->file('receipt_image')
                ->store('receipts', 'public');

            $data['receipt_image'] = $newReceipt;
        }

        try {
            $transaction->update($data);
        } catch (Throwable $exception) {
            if ($newReceipt) {
                Storage::disk('public')->delete($newReceipt);
            }

            throw $exception;
        }

        if ($newReceipt && $oldReceipt) {
            Storage::disk('public')->delete($oldReceipt);
        }

        return redirect()
            ->route('transactions.index')
            ->with('status', 'transaction-updated');
    }

    /**
     * Delete a transaction and its receipt file.
     */
    public function destroy(
        Request $request,
        Transaction $transaction
    ): RedirectResponse {
        $this->ensureOwnedByAuthenticatedUser($request, $transaction);

        $receipt = $transaction->receipt_image;

        $transaction->delete();

        if ($receipt) {
            Storage::disk('public')->delete($receipt);
        }

        return redirect()
            ->route('transactions.index')
            ->with('status', 'transaction-deleted');
    }

    /**
     * Get system categories and custom categories owned by the current user.
     */
    private function availableCategories(Request $request)
    {
        return Category::query()
            ->where(function ($query) use ($request) {
                $query
                    ->whereNull('user_id')
                    ->orWhere('user_id', $request->user()->id);
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }

    /**
     * Prevent users from modifying transactions that they do not own.
     */
    private function ensureOwnedByAuthenticatedUser(
        Request $request,
        Transaction $transaction
    ): void {
        abort_if(
            (int) $transaction->user_id !== (int) $request->user()->id,
            403
        );
    }
}
