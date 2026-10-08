<x-guest-layout>
    <header class="mb-7">
        <span class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"><x-ui-icon name="mail" class="h-6 w-6" /></span>
        <h1 class="ui-title">Check your inbox.</h1>
        <p class="ui-description mt-2">Before getting started, verify your email using the link we just sent. You can request another email below.</p>
    </header>

    @if (session('status') == 'verification-link-sent')
        <div role="status" class="mb-6 rounded-xl border border-brand-200 bg-brand-50 p-4 text-sm leading-relaxed text-brand-700">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <x-primary-button class="w-full justify-center py-3">{{ __('Resend Verification Email') }}</x-primary-button>
    </form>
    <form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
        @csrf
        <button type="submit" class="ui-link text-sm">{{ __('Log Out') }}</button>
    </form>
</x-guest-layout>
