<x-app-layout>
    <x-slot name="header">
        <h1 class="ui-title">{{ __('Edit Category') }}</h1>
        <p class="ui-description">Update the name, type, or icon of your custom category.</p>
    </x-slot>

    <div class="ui-page">
        <div class="mx-auto max-w-3xl">
            <a href="{{ route('categories.index') }}" class="ui-link mb-5 inline-flex items-center gap-2">
                <x-ui-icon name="arrow-right" class="h-4 w-4 rotate-180" /> Back to categories
            </a>
            <section class="ui-panel">
                <div class="ui-panel-body">
                    <h2 class="mb-6 break-words text-base font-bold text-gray-900">Edit {{ $category->name }}</h2>
                    <form method="POST" action="{{ route('categories.update', $category) }}" data-validate-form>
                        @csrf
                        @method('PATCH')
                        <x-validation-summary :error-bag="$errors->getBag('default')" />
                        @include('categories.partials.form', ['category' => $category, 'submitLabel' => 'Save Changes'])
                    </form>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
