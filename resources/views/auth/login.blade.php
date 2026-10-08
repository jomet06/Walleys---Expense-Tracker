<x-guest-layout>
    <header class="mb-8">
        <h1 class="ui-title">Welcome back.</h1>
        <p class="ui-description mt-2">Sign in to view your transactions and monthly overview.</p>
    </header>

    <x-auth-session-status class="mb-5" :status="session('status')" />
    <x-google-login />

    <form method="POST" action="{{ route('login') }}" class="space-y-5" data-validate-form>
        @csrf
        <x-validation-summary :error-bag="$errors->getBag('default')" />
        <div data-field>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error id="email-error" for="email" :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div data-field x-data="{ showPassword: false }">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-2">
                <x-text-input id="password" class="block w-full pr-12" type="password" x-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" />
                <button type="button" x-on:click="showPassword = !showPassword" aria-label="Show password" x-bind:aria-label="showPassword ? 'Hide password' : 'Show password'" x-bind:aria-pressed="showPassword" aria-controls="password" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-gray-500 transition-colors hover:text-brand-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand-600">
                    <span x-show="!showPassword"><x-ui-icon name="eye" class="h-5 w-5" /></span>
                    <span x-show="showPassword" x-cloak><x-ui-icon name="eye-off" class="h-5 w-5" /></span>
                </button>
            </div>
            <x-input-error id="password-error" for="password" :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2.5 text-sm text-gray-600">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" name="remember" @checked(old('remember'))>
                <span>{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="ui-link text-sm" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center py-3">
            {{ __('Log in') }}
            <x-ui-icon name="arrow-right" class="ml-2 h-4 w-4" />
        </x-primary-button>
    </form>

    <p class="mt-7 text-center text-sm text-gray-600">New to Walleys? <a href="{{ route('register') }}" class="ui-link ml-1">Create an account</a></p>
</x-guest-layout>
