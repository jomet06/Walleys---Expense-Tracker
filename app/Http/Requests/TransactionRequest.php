<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    /**
     * Determine whether the request is authorized.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        $transaction = $this->route('transaction');

        if (! $transaction) {
            return true;
        }

        return (int) $transaction->user_id === (int) $user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(function (Builder $query) {
                    $query->where(function (Builder $categoryQuery) {
                        $categoryQuery
                            ->whereNull('user_id')
                            ->orWhere('user_id', $this->user()->id);
                    });
                }),
            ],
            'title' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'transaction_date' => ['required', 'date_format:Y-m-d'],
            'receipt_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }
}
