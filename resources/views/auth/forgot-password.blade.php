<x-guest-layout>
    <header class="mb-7">
        <span class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"><x-ui-icon name="mail" class="h-6 w-6" /></span>
        <h1 class="ui-title">Forgot your password?</h1>
        <p class="ui-description mt-2">Enter your account email and we'll send you a link to choose a new password.</p>
    </header>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <x-primary-button class="w-full justify-center py-3">{{ __('Email Password Reset Link') }}</x-primary-button>
    </form>

    <p class="mt-7 text-center text-sm"><a href="{{ route('login') }}" class="ui-link">Back to log in</a></p>
</x-guest-layout>
