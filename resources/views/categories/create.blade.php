<x-app-layout>
    <x-slot name="header">
        <h1 class="ui-title">{{ __('Add Category') }}</h1>
        <p class="ui-description">This category will only be available to your account.</p>
    </x-slot>

    <div class="ui-page">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('categories.index') }}" class="ui-link mb-5 inline-flex items-center gap-2">
                <x-ui-icon name="arrow-right" class="h-4 w-4 rotate-180" /> Back to categories
            </a>
            <section class="ui-panel">
                <div class="ui-panel-body">
                    <h2 class="text-base font-bold text-gray-900">Create a custom category</h2>
                    <p class="mt-1 mb-6 text-sm text-gray-500">Give it a name and choose an icon you can recognize at a glance.</p>
                    <form method="POST" action="{{ route('categories.store') }}" data-validate-form>
                        @csrf
                        <x-validation-summary :error-bag="$errors->getBag('default')" />
                        @include('categories.partials.form', ['category' => null, 'submitLabel' => 'Create Category'])
                    </form>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
