@php
    $allCategories = $systemCategories->concat($customCategories);
    $categorySearchData = $allCategories->map(fn ($category) => [
        'name' => $category->name,
        'type' => $category->type,
        'scope' => $category->user_id === null ? 'system' : 'custom',
    ])->values();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="ui-title">{{ __('Categories') }}</h1>
                <p class="ui-description">Manage the categories used to organize your income and expenses.</p>
            </div>
            <a href="{{ route('categories.create') }}" class="ui-button">
                <x-ui-icon name="plus" class="h-4 w-4" /> Add Category
            </a>
        </div>
    </x-slot>

    <div class="ui-page space-y-6" x-data="{
        type: 'all', query: '', items: @js($categorySearchData), deleteId: null, deleteName: '',
        matches(name, type) { return (this.type === 'all' || this.type === type) && name.toLowerCase().includes(this.query.trim().toLowerCase()); },
        visibleCount(scope) { return this.items.filter(item => item.scope === scope && this.matches(item.name, item.type)).length; },
        confirmDelete(id, name) { this.deleteId = id; this.deleteName = name; this.$dispatch('open-modal', 'delete-category'); }
    }">
        @if (session('status'))
            <div role="status" class="flex items-center gap-3 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-700">
                <x-ui-icon name="check" class="h-5 w-5 shrink-0" />
                @switch(session('status'))
                    @case('category-created') Category created successfully. @break
                    @case('category-updated') Category updated successfully. @break
                    @case('category-deleted') Category deleted successfully. @break
                @endswitch
            </div>
        @endif

        @if (session('error') === 'category-in-use')
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                This category cannot be deleted because it is still used by one or more transactions.
            </div>
        @endif

        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div class="grid w-full grid-cols-3 self-start rounded-xl border border-gray-200 bg-white p-1 sm:inline-flex sm:w-auto" role="group" aria-label="Filter categories by type">
                @foreach (['all' => 'All categories', 'income' => 'Income', 'expense' => 'Expense'] as $value => $label)
                    <button type="button" @click="type = '{{ $value }}'" :aria-pressed="type === '{{ $value }}'"
                        :class="type === '{{ $value }}' ? 'bg-brand-100 text-brand-700' : 'text-gray-500 hover:bg-gray-50'"
                        class="flex items-center justify-center gap-1.5 rounded-lg px-2 py-2 text-xs font-semibold transition sm:gap-2 sm:px-3 sm:text-sm">
                        @if ($value === 'all')
                            <span class="sm:hidden">All</span><span class="hidden sm:inline">All categories</span>
                        @else
                            {{ $label }}
                        @endif
                        <span class="rounded-md bg-black/[0.04] px-1.5 py-0.5 text-[11px]">{{ $value === 'all' ? $allCategories->count() : $allCategories->where('type', $value)->count() }}</span>
                    </button>
                @endforeach
            </div>
            <div class="relative w-full sm:max-w-xs">
                <label for="category-search" class="sr-only">Search categories</label>
                <x-ui-icon name="search" class="pointer-events-none absolute left-3 top-3 h-5 w-5 text-gray-400" />
                <input id="category-search" type="search" x-model="query" class="ui-field pl-10" placeholder="Search categories" autocomplete="off">
            </div>
        </div>

        @foreach (['system' => $systemCategories, 'custom' => $customCategories] as $scope => $categoryGroup)
            <section class="ui-panel">
                <div class="ui-panel-body">
                    <div class="mb-5 flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-base font-bold text-gray-900">{{ $scope === 'system' ? 'System Categories' : 'My Categories' }}</h2>
                            <p class="mt-1 text-sm leading-relaxed text-gray-500">
                                {{ $scope === 'system' ? 'These categories are available to every user and cannot be edited or deleted.' : 'Custom categories created by you.' }}
                            </p>
                        </div>
                        <span class="rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700" x-text="visibleCount('{{ $scope }}')">{{ $categoryGroup->count() }}</span>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @forelse ($categoryGroup as $category)
                            <article class="rounded-xl border border-gray-200 p-4 transition hover:border-brand-200 hover:bg-gray-50/50"
                                data-category-name="{{ $category->name }}" data-category-type="{{ $category->type }}"
                                x-show="matches($el.dataset.categoryName, $el.dataset.categoryType)">
                                <div class="flex items-center gap-3">
                                    <span @class(['flex h-12 w-12 shrink-0 items-center justify-center rounded-xl', 'bg-brand-100 text-brand-700' => $category->type === 'income', 'bg-[#fff3eb] text-[#b56438]' => $category->type === 'expense'])>
                                        <x-category-icon :name="$category->icon" class="h-6 w-6" />
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="break-words text-sm font-semibold text-gray-900">{{ $category->name }}</h3>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-between gap-3 border-t border-gray-100 pt-3">
                                    <span class="{{ $category->type === 'income' ? 'ui-badge-income' : 'ui-badge-expense' }}">{{ ucfirst($category->type) }}</span>
                                    @if ($scope === 'custom')
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('categories.edit', $category) }}" class="ui-link inline-flex items-center gap-1.5" aria-label="Edit {{ $category->name }}">
                                            <x-ui-icon name="edit" class="h-4 w-4" /> Edit
                                        </a>
                                        <form id="delete-category-{{ $category->id }}" method="POST" action="{{ route('categories.destroy', $category) }}"
                                            @submit.prevent="confirmDelete({{ $category->id }}, $el.dataset.categoryName)" data-category-name="{{ $category->name }}"
                                            onsubmit="if (!window.Alpine) return confirm('Delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 transition hover:text-red-600" aria-label="Delete {{ $category->name }}">
                                                <x-ui-icon name="trash" class="h-4 w-4" /> Delete
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                        <span class="text-xs text-gray-400">Default category</span>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="ui-empty sm:col-span-2 xl:col-span-3">
                                <x-ui-icon name="categories" class="mx-auto mb-3 h-8 w-8 text-gray-300" />
                                <p>{{ $scope === 'system' ? 'No system categories are available.' : 'You have not created any custom categories yet.' }}</p>
                                @if ($scope === 'custom')
                                    <a href="{{ route('categories.create') }}" class="ui-link mt-3 inline-flex items-center gap-1.5">Create your first category <x-ui-icon name="arrow-right" class="h-4 w-4" /></a>
                                @endif
                            </div>
                        @endforelse
                    </div>
                    @if ($categoryGroup->isNotEmpty())
                        <div x-cloak x-show="visibleCount('{{ $scope }}') === 0" class="ui-empty">
                            <p>No categories match your filters.</p>
                            <button type="button" @click="query = ''; type = 'all'" class="ui-link mt-3">Clear filters</button>
                        </div>
                    @endif
                </div>
            </section>
        @endforeach

        <x-modal name="delete-category" maxWidth="md" focusable>
            <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="delete-category-title" aria-describedby="delete-category-description">
                <h2 id="delete-category-title" class="text-lg font-bold text-gray-900">Delete category?</h2>
                <p id="delete-category-description" class="mt-2 text-sm leading-relaxed text-gray-500"><span class="font-semibold text-gray-700" x-text="deleteName"></span> will be removed. Categories used by a transaction cannot be deleted.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="ui-button-secondary" @click="$dispatch('close')">Keep category</button>
                    <button type="button" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        @click="document.getElementById('delete-category-' + deleteId)?.submit()">Delete category</button>
                </div>
            </div>
        </x-modal>
    </div>
</x-app-layout>
