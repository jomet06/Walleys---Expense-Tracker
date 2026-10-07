<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold">
                            Edit {{ $transaction->title }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Update this transaction's details or upload a replacement receipt.
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('transactions.update', $transaction) }}"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        @method('PATCH')

                        @include('transactions.partials.form', [
                            'transaction' => $transaction,
                            'submitLabel' => 'Save Changes',
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
