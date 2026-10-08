<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Categories') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage the categories used to organize your income and expenses.
                </p>
            </div>

            <a
                href="{{ route('categories.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                Add Category
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (session('status'))
                <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    @switch(session('status'))
                        @case('category-created')
                            Category created successfully.
                            @break

                        @case('category-updated')
                            Category updated successfully.
                            @break

                        @case('category-deleted')
                            Category deleted successfully.
                            @break
                    @endswitch
                </div>
            @endif

            @if (session('error') === 'category-in-use')
                <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    This category cannot be deleted because it is still used by one or more transactions.
                </div>
            @endif

            <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            System Categories
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            These categories are available to every user and cannot be edited or deleted.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($systemCategories as $category)
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-medium text-gray-900">
                                                {{ $category->name }}
                                            </h4>

                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                                Default
                                            </span>
                                        </div>

                                        <p class="mt-2 text-sm text-gray-500">
                                            Icon: {{ $category->icon }}
                                        </p>
                                    </div>

                                    <span
                                        @class([
                                            'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium',
                                            'bg-green-100 text-green-700' => $category->type === 'income',
                                            'bg-red-100 text-red-700' => $category->type === 'expense',
                                        ])
                                    >
                                        {{ ucfirst($category->type) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">
                                No system categories are available.
                            </p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            My Categories
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Custom categories created by you.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($customCategories as $category)
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="font-medium text-gray-900">
                                            {{ $category->name }}
                                        </h4>

                                        <p class="mt-2 text-sm text-gray-500">
                                            Icon: {{ $category->icon }}
                                        </p>
                                    </div>

                                    <span
                                        @class([
                                            'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium',
                                            'bg-green-100 text-green-700' => $category->type === 'income',
                                            'bg-red-100 text-red-700' => $category->type === 'expense',
                                        ])
                                    >
                                        {{ ucfirst($category->type) }}
                                    </span>
                                </div>

                                <div class="mt-4 flex items-center gap-3 border-t border-gray-100 pt-4">
                                    <a
                                        href="{{ route('categories.edit', $category) }}"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('categories.destroy', $category) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this category?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="text-sm font-medium text-red-600 hover:text-red-800"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="sm:col-span-2 lg:col-span-3 rounded-lg border border-dashed border-gray-300 p-8 text-center">
                                <p class="text-sm text-gray-500">
                                    You have not created any custom categories yet.
                                </p>

                                <a
                                    href="{{ route('categories.create') }}"
                                    class="mt-3 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                >
                                    Create your first category
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
