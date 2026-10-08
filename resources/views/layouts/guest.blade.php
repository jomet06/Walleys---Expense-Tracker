<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Walleys</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="app-surface bg-white font-sans antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
            <aside class="hidden flex-col bg-brand-900 p-10 text-white lg:flex xl:p-14">
                <a href="{{ url('/') }}" class="flex w-fit items-center gap-3 rounded-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-200" aria-label="Walleys home">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white"><img src="{{ asset('images/logo.png') }}" alt="" class="h-8 w-auto"></span>
                    <span class="text-xl font-extrabold tracking-tight">Walleys</span>
                </a>

                <div class="my-auto max-w-md py-16">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brand-200">Personal finance</p>
                    <h2 class="mt-5 text-4xl font-extrabold leading-[1.2] tracking-tight xl:text-5xl">Your money.<br>In one place.</h2>
                    <p class="mt-5 max-w-sm text-base leading-relaxed text-brand-100">Keep a record of the everyday. See where your money comes from, and where it goes.</p>

                    <div class="mt-10 space-y-5 border-t border-white/15 pt-7">
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-brand-200"><x-ui-icon name="transactions" class="h-5 w-5" /></span>
                            <div><p class="text-sm font-semibold">Keep track</p><p class="mt-1 text-sm text-brand-100">Record income and expenses as they happen.</p></div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-brand-200"><x-ui-icon name="categories" class="h-5 w-5" /></span>
                            <div><p class="text-sm font-semibold">Stay organized</p><p class="mt-1 text-sm text-brand-100">Give every transaction a category.</p></div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-brand-200"><x-ui-icon name="overview" class="h-5 w-5" /></span>
                            <div><p class="text-sm font-semibold">See the month clearly</p><p class="mt-1 text-sm text-brand-100">Review your balance and spending together.</p></div>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-brand-200">&copy; {{ date('Y') }} Walleys</p>
            </aside>

            <main class="flex min-h-screen flex-col px-6 py-7 sm:px-10 lg:px-12">
                <a href="{{ url('/') }}" class="mb-10 flex w-fit items-center gap-2.5 rounded-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-600 lg:hidden" aria-label="Walleys home">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="h-8 w-auto">
                    <span class="text-lg font-extrabold tracking-tight">Walleys</span>
                </a>
                <div class="mx-auto my-auto w-full max-w-[420px] py-6 lg:py-12">
                    {{ $slot }}
                </div>
                <div class="mt-8 flex items-center justify-center gap-2 text-xs text-gray-500">
                    <a href="{{ url('/') }}" class="rounded-md px-2 py-1 transition-colors hover:text-brand-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand-600">Back to Walleys</a>
                </div>
            </main>
        </div>
    </body>
</html>
