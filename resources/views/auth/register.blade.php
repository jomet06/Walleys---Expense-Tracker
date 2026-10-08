<x-guest-layout>
    <header class="mb-7">
        <h1 class="ui-title">Create your account.</h1>
        <p class="ui-description mt-2">Start with a place to keep your income and expenses.</p>
    </header>

    <x-google-login />

    <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ showPassword: false }" data-validate-form>
        @csrf
        <x-validation-summary :error-bag="$errors->getBag('default')" />
        <div data-field>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="mt-2 block w-full" type="text" maxlength="255" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Your name" />
            <x-input-error id="name-error" for="name" :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div data-field>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-input-error id="email-error" for="email" :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div data-field>
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-2">
                <x-text-input id="password" class="block w-full pr-12" type="password" x-bind:type="showPassword ? 'text' : 'password'" name="password" required minlength="8" autocomplete="new-password" aria-describedby="password-hint" />
                <button type="button" x-on:click="showPassword = !showPassword" aria-label="Show passwords" x-bind:aria-label="showPassword ? 'Hide passwords' : 'Show passwords'" x-bind:aria-pressed="showPassword" aria-controls="password password_confirmation" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-gray-500 transition-colors hover:text-brand-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand-600">
                    <span x-show="!showPassword"><x-ui-icon name="eye" class="h-5 w-5" /></span>
                    <span x-show="showPassword" x-cloak><x-ui-icon name="eye-off" class="h-5 w-5" /></span>
                </button>
            </div>
            <p id="password-hint" class="mt-2 text-xs text-gray-500">Use at least 8 characters.</p>
            <x-input-error id="password-error" for="password" :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div data-field>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="mt-2 block w-full" type="password" x-bind:type="showPassword ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error id="password_confirmation-error" for="password_confirmation" :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <x-primary-button class="w-full justify-center py-3">
            {{ __('Register') }}
            <x-ui-icon name="arrow-right" class="ml-2 h-4 w-4" />
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">{{ __('Already registered?') }} <a href="{{ route('login') }}" class="ui-link ml-1">{{ __('Log in') }}</a></p>
</x-guest-layout>
