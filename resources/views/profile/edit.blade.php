<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @if ($user->password)
                        @include('profile.partials.update-password-form')
                    @else
                        <h2 class="text-lg font-medium text-gray-900">Password and account management</h2>
                        <p class="mt-2 text-sm text-gray-600">You signed up with Google. Set a password through a reset link sent to your email before changing a password or deleting your account.</p>
                        <form method="post" action="{{ route('password.setup') }}" class="mt-3">
                            @csrf
                            <x-primary-button>Set a password via email</x-primary-button>
                            <p class="mt-2 text-sm text-gray-500">We will email a reset link and sign you out so you can open it.</p>
                            <x-input-error :messages="$errors->get('password_setup')" class="mt-2" />
                        </form>
                    @endif
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @if ($user->password)
                        @include('profile.partials.delete-user-form')
                    @else
                        <p class="text-sm text-gray-600">Account deletion requires password confirmation. Use the email reset link above to set your password first.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
