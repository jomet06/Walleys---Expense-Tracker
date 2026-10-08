<section>
    <header class="flex items-start gap-4">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><x-ui-icon name="settings" class="h-5 w-5" /></span>
        <div>
            <h2 class="text-base font-bold text-gray-900">{{ __('Profile Information') }}</h2>
            <p class="mt-1 text-sm leading-relaxed text-gray-500">{{ __("Update your account's profile information and email address.") }}</p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" data-validate-form action="{{ route('profile.update') }}" class="mt-7 max-w-xl space-y-5">
        @csrf
        @method('patch')
        <x-validation-summary :error-bag="$errors->getBag('default')" />
        <div data-field>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" maxlength="255" type="text" class="mt-2 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error id="name-error" for="name" class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div data-field>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-2 block w-full" :value="old('email', $user->email)" required autocomplete="username" :readonly="$user->google_id !== null" :aria-describedby="$user->google_id ? 'google-email-hint' : null" />
            <x-input-error id="email-error" for="email" class="mt-2" :messages="$errors->get('email')" />
            @if ($user->google_id)
                <p id="google-email-hint" class="mt-2 flex items-center gap-1.5 text-xs leading-relaxed text-gray-500"><x-ui-icon name="lock" class="h-3.5 w-3.5 shrink-0" />Your email is managed by your Google account.</p>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div id="email-verification-status" class="mt-3 rounded-lg bg-amber-50 p-3">
                    <p class="text-sm leading-relaxed text-amber-800">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="ml-1 rounded-md font-semibold underline decoration-amber-400 underline-offset-4 hover:text-amber-950 focus-visible:outline focus-visible:outline-2 focus-visible:outline-amber-700">{{ __('Click here to re-send the verification email.') }}</button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p role="status" class="mt-2 text-sm font-medium text-brand-700">{{ __('A new verification link has been sent to your email address.') }}</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 border-t border-gray-100 pt-5">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3500)" role="status" class="flex items-center gap-1.5 text-sm font-medium text-brand-700"><x-ui-icon name="check" class="h-4 w-4" />{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
