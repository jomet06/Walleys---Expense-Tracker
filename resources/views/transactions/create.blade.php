<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold">
                            Record a transaction
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Choose an income or expense category and enter the transaction details.
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('transactions.store') }}"
                        enctype="multipart/form-data"
                    >
                        @csrf

                        @include('transactions.partials.form', [
                            'transaction' => null,
                            'submitLabel' => 'Create Transaction',
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
