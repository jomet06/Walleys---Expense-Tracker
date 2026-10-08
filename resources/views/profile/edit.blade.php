<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.16em] text-brand-600">Your account</p>
            <h1 class="ui-title">{{ __('Profile') }}</h1>
            <p class="ui-description mt-2">Manage your details, sign-in options, and account.</p>
        </div>
    </x-slot>

    <div class="ui-page" x-data="{ activeSection: 'profile-info' }">
        <div class="grid items-start gap-6 xl:grid-cols-[250px_minmax(0,1fr)]">
            <aside class="ui-panel xl:sticky xl:top-24">
                <div class="border-b border-gray-100 p-5">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-lg font-bold text-brand-700">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
                    <p class="break-words text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                    <p class="mt-1 break-all text-xs leading-relaxed text-gray-500">{{ $user->email }}</p>
                    <span class="mt-3 inline-flex items-center gap-1.5 rounded-md bg-gray-100 px-2 py-1 text-[11px] font-medium text-gray-600">
                        <x-ui-icon name="{{ $user->google_id ? 'check' : 'mail' }}" class="h-3.5 w-3.5" />
                        {{ $user->google_id ? 'Google connected' : 'Email account' }}
                    </span>
                </div>
                <nav class="flex flex-wrap gap-1 p-3 xl:flex-col" aria-label="Account settings sections">
                    <a href="#profile-info" x-on:click="activeSection = 'profile-info'" x-bind:class="activeSection === 'profile-info' ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50'" class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand-600"><x-ui-icon name="settings" class="h-4 w-4" />Profile details</a>
                    <a href="#account-security" x-on:click="activeSection = 'account-security'" x-bind:class="activeSection === 'account-security' ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50'" class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand-600"><x-ui-icon name="lock" class="h-4 w-4" />Password &amp; security</a>
                    <a href="#account-data" x-on:click="activeSection = 'account-data'" x-bind:class="activeSection === 'account-data' ? 'bg-brand-50 text-brand-700' : 'text-gray-600 hover:bg-gray-50'" class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand-600"><x-ui-icon name="close" class="h-4 w-4" />Delete account</a>
                </nav>
            </aside>

            <div class="min-w-0 space-y-6">
                <div id="profile-info" class="ui-panel scroll-mt-24">
                    <div class="ui-panel-body">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div id="account-security" class="ui-panel scroll-mt-24">
                    <div class="ui-panel-body">
                        @if ($user->password)
                            @include('profile.partials.update-password-form')
                        @else
                            <div class="flex items-start gap-4">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700"><x-ui-icon name="lock" class="h-5 w-5" /></span>
                                <div>
                                    <h2 class="text-base font-bold text-gray-900">Password and account management</h2>
                                    <p class="mt-1 text-sm leading-relaxed text-gray-500">You signed up with Google. Set a password through a reset link sent to your email before changing a password or deleting your account.</p>
                                </div>
                            </div>
                            <div class="mt-6 rounded-xl border border-brand-100 bg-brand-50 p-4">
                                <div class="flex items-center gap-2 text-sm font-semibold text-brand-700"><x-ui-icon name="check" class="h-4 w-4" />Google sign-in is connected</div>
                                <p class="mt-1 text-sm leading-relaxed text-brand-700">You can continue to sign in with your Google account.</p>
                            </div>
                            <form method="post" action="{{ route('password.setup') }}" class="mt-6">
                                @csrf
                                <x-primary-button>Set a password via email</x-primary-button>
                                <p class="mt-3 text-xs leading-relaxed text-gray-500">We will email a reset link and sign you out so you can open it.</p>
                                <x-input-error :messages="$errors->get('password_setup')" class="mt-2" />
                            </form>
                        @endif
                    </div>
                </div>

                <div id="account-data" class="ui-panel scroll-mt-24 border-rose-100">
                    <div class="ui-panel-body">
                        @if ($user->password)
                            @include('profile.partials.delete-user-form')
                        @else
                            <div class="flex items-start gap-4">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-700"><x-ui-icon name="close" class="h-5 w-5" /></span>
                                <div>
                                    <h2 class="text-base font-bold text-gray-900">{{ __('Delete Account') }}</h2>
                                    <p class="mt-1 text-sm leading-relaxed text-gray-500">Account deletion requires password confirmation. Use the email reset link above to set your password first.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
