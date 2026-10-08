<x-guest-layout>
    <header class="mb-7">
        <span class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"><x-ui-icon name="lock" class="h-6 w-6" /></span>
        <h1 class="ui-title">Confirm your password.</h1>
        <p class="ui-description mt-2">{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>
    </header>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5" x-data="{ showPassword: false }">
        @csrf
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-2">
                <x-text-input id="password" class="block w-full pr-12" type="password" x-bind:type="showPassword ? 'text' : 'password'" name="password" required autofocus autocomplete="current-password" />
                <button type="button" x-on:click="showPassword = !showPassword" aria-label="Show password" x-bind:aria-label="showPassword ? 'Hide password' : 'Show password'" x-bind:aria-pressed="showPassword" aria-controls="password" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-gray-500 transition-colors hover:text-brand-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand-600">
                    <span x-show="!showPassword"><x-ui-icon name="eye" class="h-5 w-5" /></span>
                    <span x-show="showPassword" x-cloak><x-ui-icon name="eye-off" class="h-5 w-5" /></span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <x-primary-button class="w-full justify-center py-3">{{ __('Confirm') }}</x-primary-button>
    </form>
</x-guest-layout>
