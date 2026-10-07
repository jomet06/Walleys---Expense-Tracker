<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walleys — Take Control of Your Money</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-display { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>

<body class="min-h-screen bg-[#F9FAFB] text-[#111827] antialiased" x-data="{ mobileOpen: false }">

    {{-- Navbar --}}
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur border-b border-gray-100">
        <nav class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 lg:px-8 py-3">

            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2 shrink-0">
                <img src="{{ asset('images/WalleysLogo.png') }}" alt="Walleys Logo" class="h-9 w-auto">
                <span class="font-display font-bold text-lg text-[#111827]">Walleys</span>
            </a>

            {{-- Desktop menu --}}
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="#features" class="hover:text-[#20B8B8] transition-colors">Features</a>
                <a href="#how-it-works" class="hover:text-[#20B8B8] transition-colors">How It Works</a>
                <a href="#about" class="hover:text-[#20B8B8] transition-colors">About</a>
            </div>

            {{-- Desktop buttons --}}
            <div class="hidden md:flex items-center gap-3">
                <a href="/login" class="text-sm font-semibold text-gray-700 hover:text-[#20B8B8] transition-colors px-3 py-2">
                    Login
                </a>
                <a href="/register"
                   class="text-sm font-semibold rounded-lg bg-[#20B8B8] px-5 py-2.5 text-white shadow-sm hover:bg-[#1AA3A3] transition-colors">
                    Get Started
                </a>
            </div>

            {{-- Mobile menu button --}}
            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-gray-700" aria-label="Toggle menu">
                <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </nav>

        {{-- Mobile menu panel --}}
        <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-gray-100 bg-white px-4 pb-4 pt-2" style="display:none">
            <div class="flex flex-col gap-1 text-sm font-medium text-gray-600">
                <a href="#features" class="py-2 hover:text-[#20B8B8]">Features</a>
                <a href="#how-it-works" class="py-2 hover:text-[#20B8B8]">How It Works</a>
                <a href="#about" class="py-2 hover:text-[#20B8B8]">About</a>
                <div class="flex items-center gap-3 mt-3">
                    <a href="/login" class="flex-1 text-center text-sm font-semibold text-gray-700 border border-gray-200 rounded-lg px-4 py-2">
                        Login
                    </a>
                    <a href="/register" class="flex-1 text-center text-sm font-semibold rounded-lg bg-[#20B8B8] text-white px-4 py-2">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20 lg:pt-24 lg:pb-28 grid lg:grid-cols-2 gap-12 items-center">

            {{-- Copy --}}
            <div>
                <span class="inline-flex items-center gap-2 text-xs font-semibold text-[#20B8B8] bg-[#20B8B8]/10 rounded-full px-3 py-1">
                    Personal Finance, Simplified
                </span>

                <h1 class="font-display mt-5 text-4xl sm:text-5xl lg:text-[3.25rem] font-extrabold leading-[1.1] text-[#111827]">
                    Take Control of Your Money
                </h1>

                <p class="mt-5 text-lg text-gray-600 max-w-lg leading-relaxed">
                    Track your income and expenses, understand your spending habits, and manage your finances with ease.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a href="/register"
                       class="rounded-lg bg-[#20B8B8] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#1AA3A3] transition-colors">
                        Get Started
                    </a>
                    <a href="#how-it-works"
                       class="rounded-lg px-6 py-3 text-sm font-semibold text-gray-700 border border-gray-200 hover:border-[#20B8B8] hover:text-[#20B8B8] transition-colors">
                        Learn More
                    </a>
                </div>
            </div>

            {{-- Dashboard mockup placeholder --}}
            <div class="relative">
                <div class="absolute -inset-6 bg-[#20B8B8]/10 rounded-3xl blur-2xl"></div>
                <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <p class="text-xs text-gray-400">Current Balance</p>
                            <p class="font-display text-2xl font-bold text-[#111827]">Rp 4.250.000</p>
                        </div>
                        <div class="h-9 w-9 rounded-full bg-[#20B8B8]/10 flex items-center justify-center">
                            <svg class="h-5 w-5 text-[#20B8B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v2" />
                            </svg>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <div class="rounded-xl bg-[#F9FAFB] p-3">
                            <p class="text-xs text-gray-400">Income</p>
                            <p class="font-semibold text-[#111827]">+Rp 6.100.000</p>
                        </div>
                        <div class="rounded-xl bg-[#F9FAFB] p-3">
                            <p class="text-xs text-gray-400">Expenses</p>
                            <p class="font-semibold text-[#111827]">-Rp 1.850.000</p>
                        </div>
                    </div>

                    {{-- simple bar chart --}}
                    <div class="flex items-end gap-2 h-24">
                        <div class="flex-1 bg-[#20B8B8]/20 rounded-t-md" style="height:45%"></div>
                        <div class="flex-1 bg-[#20B8B8]/30 rounded-t-md" style="height:70%"></div>
                        <div class="flex-1 bg-[#20B8B8] rounded-t-md" style="height:90%"></div>
                        <div class="flex-1 bg-[#20B8B8]/40 rounded-t-md" style="height:55%"></div>
                        <div class="flex-1 bg-[#20B8B8]/60 rounded-t-md" style="height:75%"></div>
                        <div class="flex-1 bg-[#20B8B8]/25 rounded-t-md" style="height:35%"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="bg-white py-20 lg:py-24 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-[#111827]">
                    Everything You Need to Manage Your Money
                </h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">

                {{-- Card 1 --}}
                <div class="rounded-2xl border border-gray-100 p-6 hover:shadow-md hover:-translate-y-0.5 transition-all">
                    <div class="h-11 w-11 rounded-xl bg-[#20B8B8]/10 flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-[#20B8B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m-6 4h6m-6 4h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-[#111827] mb-1.5">Expense Tracking</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Record and organize your daily expenses easily.</p>
                </div>

                {{-- Card 2 --}}
                <div class="rounded-2xl border border-gray-100 p-6 hover:shadow-md hover:-translate-y-0.5 transition-all">
                    <div class="h-11 w-11 rounded-xl bg-[#20B8B8]/10 flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-[#20B8B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-5l4-3 4 3M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-[#111827] mb-1.5">Income Tracking</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Keep track of your income and know exactly how much you earn.</p>
                </div>

                {{-- Card 3 --}}
                <div class="rounded-2xl border border-gray-100 p-6 hover:shadow-md hover:-translate-y-0.5 transition-all">
                    <div class="h-11 w-11 rounded-xl bg-[#20B8B8]/10 flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-[#20B8B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-[#111827] mb-1.5">Smart Categories</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Organize your transactions with customizable categories.</p>
                </div>

                {{-- Card 4 --}}
                <div class="rounded-2xl border border-gray-100 p-6 hover:shadow-md hover:-translate-y-0.5 transition-all">
                    <div class="h-11 w-11 rounded-xl bg-[#20B8B8]/10 flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-[#20B8B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-[#111827] mb-1.5">Financial Overview</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Get a clear overview of your financial activity.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- How It Works Section --}}
    <section id="how-it-works" class="py-20 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-[#111827]">
                    How Walleys Works
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">

                <div class="relative">
                    <span class="font-display text-5xl font-extrabold text-[#20B8B8]/20">01</span>
                    <h3 class="font-display font-bold text-lg text-[#111827] mt-2 mb-2">Create Your Account</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Sign up and start managing your finances.</p>
                </div>

                <div class="relative">
                    <span class="font-display text-5xl font-extrabold text-[#20B8B8]/20">02</span>
                    <h3 class="font-display font-bold text-lg text-[#111827] mt-2 mb-2">Track Your Transactions</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Record your income and expenses as they happen.</p>
                </div>

                <div class="relative">
                    <span class="font-display text-5xl font-extrabold text-[#20B8B8]/20">03</span>
                    <h3 class="font-display font-bold text-lg text-[#111827] mt-2 mb-2">Understand Your Finances</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Use your dashboard to understand your spending habits.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- Dashboard Preview Section --}}
    <section class="bg-white py-20 lg:py-24 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-[#111827]">
                    Your Finances, at a Glance
                </h2>
                <p class="mt-3 text-gray-600">A quick look at what your Walleys dashboard will feel like.</p>
            </div>

            <div class="max-w-4xl mx-auto rounded-2xl border border-gray-100 shadow-lg overflow-hidden">
                {{-- top stats bar --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 bg-[#F9FAFB]">
                    <div class="p-5">
                        <p class="text-xs text-gray-400">Current Balance</p>
                        <p class="font-display text-xl font-bold text-[#111827]">Rp 4.250.000</p>
                    </div>
                    <div class="p-5">
                        <p class="text-xs text-gray-400">Income (this month)</p>
                        <p class="font-display text-xl font-bold text-emerald-600">+Rp 6.100.000</p>
                    </div>
                    <div class="p-5">
                        <p class="text-xs text-gray-400">Expenses (this month)</p>
                        <p class="font-display text-xl font-bold text-rose-500">-Rp 1.850.000</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-0 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                    {{-- chart --}}
                    <div class="p-6">
                        <p class="text-sm font-semibold text-[#111827] mb-4">Spending Overview</p>
                        <div class="flex items-end gap-3 h-32">
                            <div class="flex-1 bg-[#20B8B8]/20 rounded-t-md" style="height:40%"></div>
                            <div class="flex-1 bg-[#20B8B8]/30 rounded-t-md" style="height:65%"></div>
                            <div class="flex-1 bg-[#20B8B8] rounded-t-md" style="height:95%"></div>
                            <div class="flex-1 bg-[#20B8B8]/40 rounded-t-md" style="height:50%"></div>
                            <div class="flex-1 bg-[#20B8B8]/60 rounded-t-md" style="height:80%"></div>
                            <div class="flex-1 bg-[#20B8B8]/25 rounded-t-md" style="height:30%"></div>
                            <div class="flex-1 bg-[#20B8B8]/45 rounded-t-md" style="height:60%"></div>
                        </div>
                        <div class="flex justify-between text-[11px] text-gray-400 mt-2">
                            <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                        </div>
                    </div>

                    {{-- recent transactions --}}
                    <div class="p-6">
                        <p class="text-sm font-semibold text-[#111827] mb-4">Recent Transactions</p>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-center justify-between">
                                <span class="text-gray-600">Groceries</span>
                                <span class="font-medium text-rose-500">-Rp 150.000</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span class="text-gray-600">Freelance Payment</span>
                                <span class="font-medium text-emerald-600">+Rp 1.200.000</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span class="text-gray-600">Electricity Bill</span>
                                <span class="font-medium text-rose-500">-Rp 320.000</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span class="text-gray-600">Coffee</span>
                                <span class="font-medium text-rose-500">-Rp 45.000</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Final CTA Section --}}
    <section class="py-20 lg:py-24">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 text-center rounded-3xl bg-[#111827] py-16 px-8">
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-white">
                Start Managing Your Money Today
            </h2>
            <p class="mt-3 text-gray-300">
                Build better financial habits with Walleys.
            </p>
            <a href="/register"
               class="mt-8 inline-block rounded-lg bg-[#20B8B8] px-7 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#1AA3A3] transition-colors">
                Get Started
            </a>
        </div>
    </section>

    {{-- Footer --}}
    <footer id="about" class="bg-white border-t border-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-start justify-between gap-8">

            <div class="max-w-xs">
                <div class="flex items-center gap-2 mb-3">
                    <img src="{{ asset('images/WalleysLogo.png') }}" alt="Walleys Logo" class="h-7 w-auto">
                    <span class="font-display font-bold text-[#111827]">Walleys</span>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed">
                    A simple way to track your income, expenses, and everyday financial habits.
                </p>
            </div>

            <div class="flex gap-16 text-sm">
                <div class="flex flex-col gap-2">
                    <span class="font-semibold text-[#111827] mb-1">Product</span>
                    <a href="#features" class="text-gray-500 hover:text-[#20B8B8] transition-colors">Features</a>
                    <a href="#how-it-works" class="text-gray-500 hover:text-[#20B8B8] transition-colors">How It Works</a>
                </div>
                <div class="flex flex-col gap-2">
                    <span class="font-semibold text-[#111827] mb-1">Account</span>
                    <a href="/login" class="text-gray-500 hover:text-[#20B8B8] transition-colors">Login</a>
                    <a href="/register" class="text-gray-500 hover:text-[#20B8B8] transition-colors">Register</a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 pt-6 border-t border-gray-100">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} Walleys. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>