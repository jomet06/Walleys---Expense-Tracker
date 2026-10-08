<section x-data="{ showPasswords: false }">
    <header class="flex items-start gap-4">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700"><x-ui-icon name="lock" class="h-5 w-5" /></span>
        <div>
            <h2 class="text-base font-bold text-gray-900">{{ __('Update Password') }}</h2>
            <p class="mt-1 text-sm leading-relaxed text-gray-500">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-7 max-w-xl space-y-5">
        @csrf
        @method('put')

        <div class="flex justify-end">
            <button type="button" x-on:click="showPasswords = !showPasswords" x-bind:aria-pressed="showPasswords" aria-controls="update_password_current_password update_password_password update_password_password_confirmation" class="ui-link flex min-h-11 items-center gap-2 text-xs">
                <span x-show="!showPasswords"><x-ui-icon name="eye" class="h-4 w-4" /></span>
                <span x-show="showPasswords" x-cloak><x-ui-icon name="eye-off" class="h-4 w-4" /></span>
                <span x-text="showPasswords ? 'Hide passwords' : 'Show passwords'">Show passwords</span>
            </button>
        </div>
        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" x-bind:type="showPasswords ? 'text' : 'password'" class="mt-2 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" x-bind:type="showPasswords ? 'text' : 'password'" class="mt-2 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" x-bind:type="showPasswords ? 'text' : 'password'" class="mt-2 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 border-t border-gray-100 pt-5">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3500)" role="status" class="flex items-center gap-1.5 text-sm font-medium text-brand-700"><x-ui-icon name="check" class="h-4 w-4" />{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
