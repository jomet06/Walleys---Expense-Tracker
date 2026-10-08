<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Track your income and expenses, organize your transactions, and understand your finances with Walleys.">
    <title>Walleys — Take Control of Your Money</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html { scroll-behavior: smooth; }
        .landing-surface { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        .landing-surface ::selection { background: #b1e8e4; color: #124546; }
        .landing-surface :focus-visible { outline: 2px solid #138080; outline-offset: 4px; }
        .landing-surface section[id], .landing-surface footer[id] { scroll-margin-top: 6rem; }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .landing-surface *, .landing-surface *::before, .landing-surface *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
@php
    $landingPrimaryHref = auth()->check() ? route('dashboard') : route('register');
    $landingPrimaryLabel = auth()->check() ? 'Open dashboard' : 'Get Started';
@endphp

<body class="landing-surface min-h-screen bg-[#F9FAFB] text-slate-900 antialiased" x-data="{ mobileOpen: false }" @keydown.escape.window="if (mobileOpen) { mobileOpen = false; $refs.mobileMenuButton.focus() }">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-white focus:px-4 focus:py-3">Skip to content</a>

    <header class="sticky top-0 z-50 border-b border-slate-200/70 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8" aria-label="Main navigation">
            <a href="/" class="flex shrink-0 items-center gap-2.5 rounded">
                <img src="{{ asset('images/logo.png') }}" alt="" class="h-9 w-auto">
                <span class="text-xl font-extrabold tracking-tight">Walleys<span class="text-brand-500">.</span></span>
            </a>

            <div class="hidden items-center gap-6 text-sm font-semibold text-slate-500 md:flex lg:gap-8">
                <a href="#features" class="rounded transition-colors hover:text-brand-700">Features</a>
                <a href="#how-it-works" class="rounded transition-colors hover:text-brand-700">How It Works</a>
                <a href="#about" class="rounded transition-colors hover:text-brand-700">About</a>
            </div>

            <div class="hidden shrink-0 items-center gap-4 md:flex">
                @guest
                    <a href="{{ route('login') }}" class="rounded text-sm font-semibold text-slate-600 transition-colors hover:text-brand-700">Login</a>
                @endguest
                <a href="{{ $landingPrimaryHref }}" class="ui-button">{{ $landingPrimaryLabel }} <x-ui-icon name="arrow-right" class="h-4 w-4" /></a>
            </div>

            <button type="button" x-ref="mobileMenuButton" @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen.toString()" aria-expanded="false" aria-controls="landing-mobile-menu" :aria-label="mobileOpen ? 'Close navigation' : 'Open navigation'" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 text-slate-700 md:hidden">
                <span x-show="!mobileOpen"><x-ui-icon name="menu" class="h-5 w-5" /></span>
                <span x-show="mobileOpen" x-cloak><x-ui-icon name="close" class="h-5 w-5" /></span>
            </button>
        </nav>

        <nav id="landing-mobile-menu" x-show="mobileOpen" x-cloak @click.outside="mobileOpen = false" class="border-t border-slate-100 bg-white px-4 pb-5 pt-2 md:hidden" aria-label="Mobile navigation">
            <div class="mx-auto flex max-w-7xl flex-col gap-1 text-sm font-semibold text-slate-600">
                <a href="#features" @click="mobileOpen = false" class="rounded-lg px-3 py-3 hover:bg-brand-50 hover:text-brand-700">Features</a>
                <a href="#how-it-works" @click="mobileOpen = false" class="rounded-lg px-3 py-3 hover:bg-brand-50 hover:text-brand-700">How It Works</a>
                <a href="#about" @click="mobileOpen = false" class="rounded-lg px-3 py-3 hover:bg-brand-50 hover:text-brand-700">About</a>
                <div class="mt-3 flex flex-wrap gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="ui-button-secondary flex-1">Login</a>
                    @endguest
                    <a href="{{ $landingPrimaryHref }}" class="ui-button flex-1">{{ $landingPrimaryLabel }} <x-ui-icon name="arrow-right" class="h-4 w-4" /></a>
                </div>
            </div>
        </nav>
    </header>

    <main id="main-content" tabindex="-1">
        <section class="border-b border-slate-200/70">
            <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 pb-16 pt-14 sm:px-6 sm:pt-20 lg:grid-cols-2 lg:gap-14 lg:px-8 lg:pb-24 lg:pt-24">
                <div>
                    <p class="flex items-center gap-2.5 text-xs font-bold uppercase tracking-[0.14em] text-brand-700"><span class="h-2 w-2 rounded-full bg-brand-500" aria-hidden="true"></span> Personal Finance, Simplified</p>
                    <h1 class="mt-6 max-w-xl text-[2.75rem] font-extrabold leading-[1.12] tracking-[-0.045em] sm:text-6xl lg:text-[4.25rem]">Take Control of <span class="text-brand-600">Your Money</span></h1>
                    <p class="mt-6 max-w-md text-base leading-relaxed text-slate-500 sm:text-lg">Track your income and expenses, understand your spending habits, and manage your finances with ease.</p>
                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a href="{{ $landingPrimaryHref }}" class="ui-button min-h-12 px-6">{{ $landingPrimaryLabel }} <x-ui-icon name="arrow-right" class="h-4 w-4" /></a>
                        <a href="#how-it-works" class="ui-button-secondary min-h-12 px-6">Learn More <x-ui-icon name="chevron-down" class="h-4 w-4" /></a>
                    </div>
                    <div class="mt-10 flex max-w-md flex-wrap gap-x-5 gap-y-3 border-t border-slate-200 pt-5 text-xs font-medium text-slate-500">
                        <span class="flex items-center gap-1.5"><x-ui-icon name="check" class="h-4 w-4 text-brand-600" /> Income &amp; expenses</span>
                        <span class="flex items-center gap-1.5"><x-ui-icon name="check" class="h-4 w-4 text-brand-600" /> Your categories</span>
                        <span class="flex items-center gap-1.5"><x-ui-icon name="check" class="h-4 w-4 text-brand-600" /> A clear overview</span>
                    </div>
                </div>

                <figure class="min-w-0">
                    <div class="rounded-[1.5rem] border border-brand-200 bg-brand-100 p-3 sm:p-5">
                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                            <div class="flex items-center justify-between gap-2 border-b border-slate-100 px-5 py-4">
                                <span class="flex items-center gap-2 text-sm font-bold"><x-ui-icon name="overview" class="h-4 w-4 text-brand-600" /> Overview</span>
                                <span class="rounded-md bg-slate-100 px-2 py-1 text-[10px] font-semibold text-slate-500">October</span>
                            </div>
                            <div class="p-4 sm:p-5">
                                <div class="rounded-xl bg-brand-700 p-5 text-white">
                                    <div class="flex items-center justify-between gap-2"><span class="text-xs text-white/80">Net Balance</span><x-ui-icon name="wallet" class="h-5 w-5 text-white/80" /></div>
                                    <p class="mt-4 text-[1.75rem] font-bold tracking-tight tabular-nums sm:text-3xl">Rp 4.250.000</p>
                                    <p class="mt-2 text-[10px] text-white/80">Income minus expenses this month</p>
                                </div>
                                <div class="mt-3 grid grid-cols-2 gap-3">
                                    <div class="rounded-xl bg-brand-50 p-3.5"><p class="flex items-center gap-1.5 text-[10px] font-medium text-brand-700"><x-ui-icon name="arrow-down" class="h-3 w-3" /> Income</p><p class="mt-2 break-words text-sm font-bold tabular-nums sm:text-base">Rp 6.100.000</p></div>
                                    <div class="rounded-xl bg-rose-50 p-3.5"><p class="flex items-center gap-1.5 text-[10px] font-medium text-rose-700"><x-ui-icon name="arrow-up" class="h-3 w-3" /> Expenses</p><p class="mt-2 break-words text-sm font-bold tabular-nums sm:text-base">Rp 1.850.000</p></div>
                                </div>
                                <div class="mt-5 flex items-center justify-between gap-3"><p class="text-xs font-bold">Income &amp; expenses</p><span class="text-[10px] text-slate-500">6 months</span></div>
                                <div class="mt-4 flex h-28 items-end justify-around gap-3 border-b border-slate-200 pb-0" aria-hidden="true">
                                    @foreach ([[45, 24], [63, 32], [57, 19], [79, 35], [67, 29], [90, 27]] as $bars)
                                        <div class="flex h-full flex-1 items-end justify-center gap-1.5"><span class="w-3 rounded-t bg-brand-500 sm:w-5" style="height: {{ $bars[0] }}%"></span><span class="w-3 rounded-t bg-slate-300 sm:w-5" style="height: {{ $bars[1] }}%"></span></div>
                                    @endforeach
                                </div>
                                <div class="mt-2 grid grid-cols-6 text-center text-[9px] text-slate-500" aria-hidden="true"><span>May</span><span>Jun</span><span>Jul</span><span>Aug</span><span>Sep</span><span>Oct</span></div>
                                <div class="mt-4 flex justify-center gap-4 text-[10px] text-slate-500"><span class="flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-brand-500" aria-hidden="true"></span>Income</span><span class="flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-slate-300" aria-hidden="true"></span>Expenses</span></div>
                            </div>
                        </div>
                    </div>
                    <figcaption class="mt-3 text-center text-[11px] text-slate-500">Example dashboard. Amounts are illustrative.</figcaption>
                </figure>
            </div>
        </section>

        <section id="features" class="bg-white py-16 sm:py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                    <div class="max-w-2xl"><p class="text-xs font-bold uppercase tracking-[0.14em] text-brand-700">Made for the everyday</p><h2 class="mt-3 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">Everything You Need to Manage Your Money</h2></div>
                    <a href="#dashboard-preview" class="ui-link shrink-0 text-xs">Take a closer look <x-ui-icon name="arrow-right" class="h-4 w-4" /></a>
                </div>

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-12">
                    <article class="flex flex-col justify-between rounded-2xl bg-brand-700 p-6 text-white sm:p-8 lg:col-span-7">
                        <div class="flex items-start justify-between gap-6"><div><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/10"><x-ui-icon name="receipt" class="h-6 w-6" /></span><h3 class="mt-6 text-xl font-bold">Expense Tracking</h3><p class="mt-2 max-w-sm text-sm leading-relaxed text-white/80">Record and organize your daily expenses easily.</p></div><span class="text-xs font-semibold text-white/60">01</span></div>
                        <div class="mt-8 flex flex-wrap gap-2 text-xs font-medium"><span class="flex items-center gap-2 rounded-lg border border-white/20 px-3 py-2"><x-ui-icon name="coffee" class="h-4 w-4" /> Food &amp; drinks</span><span class="flex items-center gap-2 rounded-lg border border-white/20 px-3 py-2"><x-ui-icon name="car" class="h-4 w-4" /> Transport</span><span class="flex items-center gap-2 rounded-lg border border-white/20 px-3 py-2"><x-ui-icon name="shopping" class="h-4 w-4" /> Shopping</span></div>
                    </article>

                    <article class="rounded-2xl border border-brand-100 bg-brand-50 p-6 sm:p-8 lg:col-span-5">
                        <div class="flex items-start justify-between"><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-brand-700"><x-ui-icon name="arrow-down" class="h-6 w-6" /></span><span class="text-xs font-semibold text-brand-700/70">02</span></div>
                        <h3 class="mt-6 text-xl font-bold">Income Tracking</h3><p class="mt-2 max-w-sm text-sm leading-relaxed text-slate-600">Keep track of your income and know exactly how much you earn.</p>
                        <div class="mt-8 flex items-center gap-2 text-xs font-semibold text-brand-700"><x-ui-icon name="briefcase" class="h-4 w-4" /> Salary, freelance, and everything in between.</div>
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-[#F9FAFB] p-6 sm:p-8 lg:col-span-5">
                        <div class="flex items-start justify-between"><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700"><x-ui-icon name="categories" class="h-6 w-6" /></span><span class="text-xs font-semibold text-slate-400">03</span></div>
                        <h3 class="mt-6 text-xl font-bold">Smart Categories</h3><p class="mt-2 max-w-sm text-sm leading-relaxed text-slate-600">Organize your transactions with customizable categories.</p>
                        <div class="mt-7 flex items-center gap-2"><span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-brand-700"><x-ui-icon name="coffee" class="h-4 w-4" /></span><span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-brand-700"><x-ui-icon name="car" class="h-4 w-4" /></span><span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-brand-700"><x-ui-icon name="book" class="h-4 w-4" /></span><span class="ml-1 text-xs text-slate-500">Give every entry a place.</span></div>
                    </article>

                    <article class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 lg:col-span-7">
                        <div class="flex items-start justify-between"><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 text-brand-700"><x-ui-icon name="overview" class="h-6 w-6" /></span><span class="text-xs font-semibold text-slate-400">04</span></div>
                        <h3 class="mt-6 text-xl font-bold">Financial Overview</h3><p class="mt-2 max-w-sm text-sm leading-relaxed text-slate-600">Get a clear overview of your financial activity.</p>
                        <div class="mt-7 flex flex-wrap gap-2 text-xs font-medium text-slate-600"><span class="rounded-lg bg-slate-100 px-3 py-2">Monthly balance</span><span class="rounded-lg bg-slate-100 px-3 py-2">Six-month charts</span><span class="rounded-lg bg-slate-100 px-3 py-2">Category breakdown</span></div>
                    </article>
                </div>
            </div>
        </section>

        <section id="how-it-works" class="border-y border-slate-200/70 py-16 sm:py-20">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[1fr_1.3fr] lg:gap-24 lg:px-8">
                <div><p class="text-xs font-bold uppercase tracking-[0.14em] text-brand-700">A simple routine</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">How Walleys Works</h2><p class="mt-5 max-w-sm text-sm leading-relaxed text-slate-500">From your first entry to a clearer picture of your finances.</p><a href="{{ $landingPrimaryHref }}" class="ui-link mt-7">{{ $landingPrimaryLabel }} <x-ui-icon name="arrow-right" class="h-4 w-4" /></a></div>
                <ol class="space-y-7">
                    @foreach ([
                        ['number' => '01', 'title' => 'Create Your Account', 'copy' => 'Sign up and start managing your finances.'],
                        ['number' => '02', 'title' => 'Track Your Transactions', 'copy' => 'Record your income and expenses as they happen.'],
                        ['number' => '03', 'title' => 'Understand Your Finances', 'copy' => 'Use your dashboard to understand your spending habits.'],
                    ] as $step)
                        <li class="flex gap-5 sm:gap-7"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-brand-200 bg-brand-50 text-base font-bold text-brand-700">{{ $step['number'] }}</span><div class="flex-1 border-b border-slate-200 pb-7"><h3 class="text-lg font-bold">{{ $step['title'] }}</h3><p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $step['copy'] }}</p></div></li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section id="dashboard-preview" class="bg-white py-16 sm:py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto mb-10 max-w-2xl text-center"><p class="text-xs font-bold uppercase tracking-[0.14em] text-brand-700">Inside Walleys</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Your Finances, at a Glance</h2><p class="mt-4 text-sm leading-relaxed text-slate-500">A quick look at what your Walleys dashboard will feel like. Explore the example below.</p></div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-[#F7F9FA]" x-data="{ preview: 'overview' }">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-white px-5 py-4 sm:px-7"><span class="flex items-center gap-2 text-sm font-bold"><img src="{{ asset('images/logo.png') }}" alt="" class="h-6 w-auto"> Walleys</span><span class="rounded-lg bg-brand-50 px-2.5 py-1.5 text-[10px] font-semibold text-brand-700">Example dashboard · illustrative data</span></div>
                    <div class="grid lg:grid-cols-[200px_1fr]">
                        <div class="border-b border-slate-200 bg-white p-3 lg:border-b-0 lg:border-r lg:p-4">
                            <p class="mb-4 hidden px-3 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 lg:block">Workspace</p>
                            <div class="grid grid-cols-3 gap-1.5 lg:grid-cols-1" role="tablist" aria-label="Example dashboard views">
                                @foreach (['overview' => ['Overview', 'overview'], 'transactions' => ['Transactions', 'transactions'], 'categories' => ['Categories', 'categories']] as $previewId => $previewTab)
                                    <button type="button" id="preview-tab-{{ $previewId }}" role="tab" :aria-selected="(preview === '{{ $previewId }}').toString()" aria-selected="{{ $previewId === 'overview' ? 'true' : 'false' }}" aria-controls="preview-panel-{{ $previewId }}" @click="preview = '{{ $previewId }}'" @keydown.right.prevent="const tabs = Array.from($el.parentElement.querySelectorAll('[role=tab]')); const next = tabs[(tabs.indexOf($el) + 1) % tabs.length]; next.focus(); next.click()" @keydown.left.prevent="const tabs = Array.from($el.parentElement.querySelectorAll('[role=tab]')); const next = tabs[(tabs.indexOf($el) + tabs.length - 1) % tabs.length]; next.focus(); next.click()" :tabindex="preview === '{{ $previewId }}' ? 0 : -1" tabindex="{{ $previewId === 'overview' ? '0' : '-1' }}" :class="preview === '{{ $previewId }}' ? 'bg-brand-600 text-white' : 'text-slate-500 hover:bg-brand-50 hover:text-brand-700'" class="flex min-h-11 items-center justify-center gap-2 rounded-xl px-2 py-2.5 text-[10px] font-semibold transition-colors sm:text-xs lg:justify-start lg:px-3.5"><x-ui-icon :name="$previewTab[1]" class="hidden h-4 w-4 min-[390px]:block" />{{ $previewTab[0] }}</button>
                                @endforeach
                            </div>
                            <p class="mt-24 hidden px-3 text-[10px] leading-relaxed text-slate-400 lg:block">Personal finance,<br>one entry at a time.</p>
                        </div>
                        <div class="min-w-0 p-4 sm:p-6 lg:p-7">
                            <section id="preview-panel-overview" role="tabpanel" aria-labelledby="preview-tab-overview" tabindex="0" x-show="preview === 'overview'">
                                <div class="flex items-center justify-between gap-3"><h3 class="text-lg font-bold">Financial dashboard</h3><span class="text-[10px] text-slate-500">October</span></div>
                                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                    <div class="rounded-xl bg-brand-700 p-4 text-white"><p class="text-[10px] font-medium text-white/80">Net Balance</p><p class="mt-3 break-words text-lg font-bold tabular-nums">Rp 4.250.000</p></div>
                                    <div class="rounded-xl border border-brand-100 bg-brand-50 p-4"><p class="text-[10px] font-medium text-brand-700">Total Income</p><p class="mt-3 break-words text-lg font-bold tabular-nums">Rp 6.100.000</p></div>
                                    <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="text-[10px] font-medium text-slate-500">Total Expense</p><p class="mt-3 break-words text-lg font-bold tabular-nums">Rp 1.850.000</p></div>
                                </div>
                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                    <div class="rounded-xl border border-slate-200 bg-white p-5"><h4 class="text-xs font-bold">Income &amp; expenses</h4><p class="mt-1 text-[10px] text-slate-500">Last six calendar months</p><div class="mt-5 flex h-32 items-end gap-3 border-b border-slate-100" aria-hidden="true">@foreach ([[45, 24], [63, 32], [57, 19], [79, 35], [67, 29], [90, 27]] as $bars)<div class="flex h-full flex-1 items-end justify-center gap-1"><span class="w-3 rounded-t bg-brand-500" style="height: {{ $bars[0] }}%"></span><span class="w-3 rounded-t bg-slate-300" style="height: {{ $bars[1] }}%"></span></div>@endforeach</div><div class="mt-2 grid grid-cols-6 text-center text-[9px] text-slate-500"><span>May</span><span>Jun</span><span>Jul</span><span>Aug</span><span>Sep</span><span>Oct</span></div><div class="mt-4 flex justify-center gap-4 text-[10px] text-slate-500"><span class="flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-brand-500" aria-hidden="true"></span>Income</span><span class="flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-slate-300" aria-hidden="true"></span>Expenses</span></div></div>
                                    <div class="rounded-xl border border-slate-200 bg-white p-5"><h4 class="text-xs font-bold">Recent transactions</h4><ul class="mt-4 divide-y divide-slate-100 text-[11px]">@foreach ([['Groceries', 'Food', 'shopping', '−Rp 150.000', false], ['Freelance Payment', 'Freelance', 'briefcase', '+Rp 1.200.000', true], ['Electricity Bill', 'Bills', 'receipt', '−Rp 320.000', false], ['Coffee', 'Food', 'coffee', '−Rp 45.000', false]] as $example)<li class="flex items-center gap-2 py-3"><span @class(['flex h-8 w-8 shrink-0 items-center justify-center rounded-lg', 'bg-brand-50 text-brand-700' => $example[4], 'bg-slate-100 text-slate-500' => !$example[4]])><x-ui-icon :name="$example[2]" class="h-3.5 w-3.5" /></span><div class="min-w-0 flex-1"><p class="truncate font-semibold">{{ $example[0] }}</p><p class="mt-0.5 text-[9px] text-slate-500">{{ $example[1] }}</p></div><span @class(['shrink-0 text-[10px] font-semibold tabular-nums', 'text-brand-700' => $example[4], 'text-slate-700' => !$example[4]])>{{ $example[3] }}</span></li>@endforeach</ul></div>
                                </div>
                            </section>

                            <section id="preview-panel-transactions" role="tabpanel" aria-labelledby="preview-tab-transactions" tabindex="0" x-show="preview === 'transactions'" x-cloak>
                                <h3 class="text-lg font-bold">Transactions</h3><p class="mt-1 text-xs text-slate-500">Income and expenses, all in one place.</p>
                                <ul class="mt-5 divide-y divide-slate-100 rounded-xl border border-slate-200 bg-white">
                                    @foreach ([['Groceries', 'Food', 'shopping', '−Rp 150.000', false], ['Freelance Payment', 'Freelance', 'briefcase', '+Rp 1.200.000', true], ['Electricity Bill', 'Bills', 'receipt', '−Rp 320.000', false], ['Coffee', 'Food', 'coffee', '−Rp 45.000', false]] as $example)
                                        <li class="flex items-start gap-3 p-4 sm:items-center sm:p-5"><span @class(['flex h-10 w-10 shrink-0 items-center justify-center rounded-xl', 'bg-brand-50 text-brand-700' => $example[4], 'bg-rose-50 text-rose-600' => !$example[4]])><x-ui-icon :name="$example[2]" class="h-4 w-4" /></span><div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><div class="min-w-0"><p class="break-words text-xs font-semibold sm:text-sm">{{ $example[0] }}</p><p class="mt-1 text-[10px] text-slate-500">{{ $example[1] }}</p></div><span @class(['break-words text-xs font-bold tabular-nums sm:shrink-0 sm:text-sm', 'text-brand-700' => $example[4], 'text-slate-800' => !$example[4]])>{{ $example[3] }}</span></div></li>
                                    @endforeach
                                </ul>
                            </section>

                            <section id="preview-panel-categories" role="tabpanel" aria-labelledby="preview-tab-categories" tabindex="0" x-show="preview === 'categories'" x-cloak>
                                <h3 class="text-lg font-bold">Categories</h3><p class="mt-1 text-xs text-slate-500">Group your entries with categories.</p>
                                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                    @foreach ([['Food', 'coffee', false], ['Salary', 'briefcase', true], ['Transport', 'car', false], ['Shopping', 'shopping', false], ['Freelance', 'wallet', true], ['Bills', 'receipt', false]] as $exampleCategory)
                                        <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-5"><span @class(['mb-5 flex h-10 w-10 items-center justify-center rounded-xl', 'bg-brand-50 text-brand-700' => $exampleCategory[2], 'bg-amber-50 text-amber-700' => !$exampleCategory[2]])><x-ui-icon :name="$exampleCategory[1]" class="h-5 w-5" /></span><p class="text-sm font-bold">{{ $exampleCategory[0] }}</p><p class="mt-1 text-[10px] text-slate-500">{{ $exampleCategory[2] ? 'Income' : 'Expense' }}</p></div>
                                    @endforeach
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
                <p class="mt-4 text-center text-[11px] text-slate-500">This is an interactive preview. Your account starts with your own entries.</p>
            </div>
        </section>

        <section class="px-4 pb-16 sm:px-6 sm:pb-20 lg:px-8">
            <div class="mx-auto grid max-w-7xl items-center gap-8 rounded-[1.5rem] bg-brand-700 px-6 py-12 text-white sm:px-10 lg:grid-cols-[1fr_auto] lg:px-14 lg:py-14"><div class="max-w-2xl"><p class="text-xs font-bold uppercase tracking-[0.14em] text-brand-100">Start with one entry</p><h2 class="mt-4 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">Start Managing Your Money Today</h2><p class="mt-4 text-sm leading-relaxed text-white/80">Build better financial habits with Walleys.</p></div><a href="{{ $landingPrimaryHref }}" class="inline-flex min-h-12 w-fit items-center justify-center gap-3 rounded-xl bg-white px-6 py-3 text-sm font-bold text-brand-700 transition-colors hover:bg-brand-50">{{ $landingPrimaryLabel }} <x-ui-icon name="arrow-right" class="h-4 w-4" /></a></div>
        </section>
    </main>

    <footer id="about" class="border-t border-slate-200 bg-white py-12">
        <div class="mx-auto flex max-w-7xl flex-col justify-between gap-10 px-4 sm:px-6 md:flex-row lg:px-8">
            <div class="max-w-xs"><a href="/" class="inline-flex items-center gap-2.5 rounded"><img src="{{ asset('images/logo.png') }}" alt="" class="h-8 w-auto"><span class="text-xl font-extrabold tracking-tight">Walleys<span class="text-brand-500">.</span></span></a><p class="mt-4 text-sm leading-relaxed text-slate-500">A simple way to track your income, expenses, and everyday financial habits.</p></div>
            <div class="flex flex-wrap gap-x-16 gap-y-8 text-sm">
                <div class="flex flex-col gap-3"><span class="mb-1 font-bold">Product</span><a href="#features" class="rounded text-slate-500 transition-colors hover:text-brand-700">Features</a><a href="#how-it-works" class="rounded text-slate-500 transition-colors hover:text-brand-700">How It Works</a></div>
                <div class="flex flex-col gap-3"><span class="mb-1 font-bold">Account</span>@auth<a href="{{ route('dashboard') }}" class="rounded text-slate-500 transition-colors hover:text-brand-700">Dashboard</a><a href="{{ route('profile.edit') }}" class="rounded text-slate-500 transition-colors hover:text-brand-700">Account settings</a>@else<a href="{{ route('login') }}" class="rounded text-slate-500 transition-colors hover:text-brand-700">Login</a><a href="{{ route('register') }}" class="rounded text-slate-500 transition-colors hover:text-brand-700">Register</a>@endauth</div>
            </div>
        </div>
        <div class="mx-auto mt-10 max-w-7xl px-4 sm:px-6 lg:px-8"><div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-6 text-xs text-slate-500"><p>&copy; {{ date('Y') }} Walleys. All rights reserved.</p><p>Personal Finance, Simplified.</p></div></div>
    </footer>
</body>
</html>
