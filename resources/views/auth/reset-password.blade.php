<x-guest-layout>
    <header class="mb-7">
        <span class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"><x-ui-icon name="lock" class="h-6 w-6" /></span>
        <h1 class="ui-title">Choose a new password.</h1>
        <p class="ui-description mt-2">Use a password you don't use for other accounts.</p>
    </header>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5" x-data="{ showPassword: false }">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-2">
                <x-text-input id="password" class="block w-full pr-12" type="password" x-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" aria-describedby="password-hint" />
                <button type="button" x-on:click="showPassword = !showPassword" aria-label="Show passwords" x-bind:aria-label="showPassword ? 'Hide passwords' : 'Show passwords'" x-bind:aria-pressed="showPassword" aria-controls="password password_confirmation" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-gray-500 transition-colors hover:text-brand-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand-600">
                    <span x-show="!showPassword"><x-ui-icon name="eye" class="h-5 w-5" /></span>
                    <span x-show="showPassword" x-cloak><x-ui-icon name="eye-off" class="h-5 w-5" /></span>
                </button>
            </div>
            <p id="password-hint" class="mt-2 text-xs text-gray-500">Use at least 8 characters.</p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="mt-2 block w-full" type="password" x-bind:type="showPassword ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <x-primary-button class="w-full justify-center py-3">{{ __('Reset Password') }}</x-primary-button>
    </form>
</x-guest-layout>
