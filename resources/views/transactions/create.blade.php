<x-app-layout>
    <x-slot name="header">
        <h1 class="ui-title">{{ __('Add Transaction') }}</h1>
        <p class="ui-description">Choose an income or expense category and enter the transaction details.</p>
    </x-slot>
    <div class="ui-page">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('transactions.index') }}" class="ui-link mb-5 inline-flex items-center gap-2"><x-ui-icon name="arrow-right" class="h-4 w-4 rotate-180" /> Back to transactions</a>
            <section class="ui-panel">
                <div class="ui-panel-body">
                    <h2 class="mb-6 text-base font-bold text-gray-900">Record a transaction</h2>
                    <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data">
                        @csrf
                        @include('transactions.partials.form', ['transaction' => null, 'submitLabel' => 'Create Transaction'])
                    </form>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
