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
    <body class="app-surface font-sans antialiased">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[70] focus:rounded-xl focus:bg-white focus:px-4 focus:py-3">Skip to content</a>
        <div x-data="appShell" @keydown.escape.window="closeSidebar()" class="min-h-screen">
            @include('layouts.navigation')
            <div class="min-w-0 lg:pl-64">
                <div class="sticky top-0 z-20 flex h-[72px] items-center justify-between gap-3 border-b border-slate-200/70 bg-white/95 px-4 backdrop-blur-sm sm:px-6 xl:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" x-ref="menuToggle" @click="openSidebar()" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 text-slate-700 lg:hidden" aria-label="Open navigation" aria-controls="app-navigation" :aria-expanded="sidebarOpen.toString()">
                            <x-ui-icon name="menu" />
                        </button>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 lg:hidden"><x-application-logo class="h-8 w-8" /><span class="text-base font-bold">Walleys</span></a>
                        <div class="hidden items-center gap-2 text-sm sm:flex">
                            <span class="text-slate-400">Workspace</span><span class="text-slate-300" aria-hidden="true">/</span>
                            <span class="font-semibold text-slate-700">{{ request()->routeIs('transactions.*') ? 'Transactions' : (request()->routeIs('categories.*') ? 'Categories' : (request()->routeIs('profile.*') ? 'Account' : 'Overview')) }}</span>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-3">
                        <div class="hidden items-center gap-2 text-xs text-slate-500 md:flex"><x-ui-icon name="calendar" class="h-4 w-4" /><time datetime="{{ now()->format('Y-m-d') }}">{{ now()->format('D, d M Y') }}</time></div>
                        <a href="{{ route('profile.edit') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700" aria-label="Open your profile">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</a>
                    </div>
                </div>
                <main id="main-content" tabindex="-1" class="min-w-0">
                    @isset($header)
                        <header class="mx-auto max-w-[1440px] px-4 pb-6 pt-7 sm:px-6 lg:pt-9 xl:px-8">{{ $header }}</header>
                    @endisset
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
