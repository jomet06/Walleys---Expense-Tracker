<section class="space-y-6">
    <header class="flex items-start gap-4">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-700"><x-ui-icon name="close" class="h-5 w-5" /></span>
        <div>
            <h2 class="text-base font-bold text-gray-900">{{ __('Delete Account') }}</h2>
            <p class="mt-1 max-w-2xl text-sm leading-relaxed text-gray-500">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>
        </div>
    </header>

    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 transition-colors hover:border-rose-300 hover:bg-rose-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600">{{ __('Delete Account') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" maxWidth="lg" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8" role="dialog" aria-modal="true" aria-labelledby="delete-account-title" aria-describedby="delete-account-description">
            @csrf
            @method('delete')

            <span class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-700"><x-ui-icon name="close" class="h-6 w-6" /></span>
            <h2 id="delete-account-title" class="text-xl font-bold tracking-tight text-gray-900">{{ __('Are you sure you want to delete your account?') }}</h2>
            <p id="delete-account-description" class="mt-3 text-sm leading-relaxed text-gray-600">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>

            <div class="mt-6">
                <x-input-label for="delete_account_password" value="{{ __('Password') }}" />
                <x-text-input id="delete_account_password" name="password" type="password" class="mt-2 block w-full" autocomplete="current-password" placeholder="{{ __('Password') }}" required />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-7 flex flex-wrap justify-end gap-3 border-t border-gray-100 pt-5">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-transparent bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-rose-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600">{{ __('Delete Account') }}</button>
            </div>
        </form>
    </x-modal>
</section>
