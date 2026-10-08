<x-app-layout>
    <x-slot name="header">
        <h1 class="ui-title">{{ __('Edit Transaction') }}</h1>
        <p class="ui-description">Update this transaction's details or upload a replacement receipt.</p>
    </x-slot>
    <div class="ui-page">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('transactions.index') }}" class="ui-link mb-5 inline-flex items-center gap-2"><x-ui-icon name="arrow-right" class="h-4 w-4 rotate-180" /> Back to transactions</a>
            <section class="ui-panel">
                <div class="ui-panel-body">
                    <h2 class="mb-6 break-words text-base font-bold text-gray-900">Edit {{ $transaction->title }}</h2>
                    <form method="POST" action="{{ route('transactions.update', $transaction) }}" enctype="multipart/form-data" data-validate-form>
                        @csrf
                        @method('PATCH')
                        <x-validation-summary :error-bag="$errors->getBag('default')" />
                        @include('transactions.partials.form', ['transaction' => $transaction, 'submitLabel' => 'Save Changes'])
                    </form>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
