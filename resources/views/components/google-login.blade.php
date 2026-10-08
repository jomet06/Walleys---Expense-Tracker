<div class="mb-6">
    <a href="{{ route('google.redirect') }}" class="flex w-full items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Continue with Google</a>
    <x-input-error :messages="$errors->get('google')" class="mt-2" />
    <p class="mt-4 text-center text-xs text-gray-500">Or continue with email and password</p>
</div>
