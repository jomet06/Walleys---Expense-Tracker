<div x-show="sidebarOpen && !isDesktop" x-cloak x-transition.opacity class="fixed inset-0 z-40 bg-slate-950/40 lg:hidden" aria-hidden="true" @click="closeSidebar()"></div>
<aside id="app-navigation" x-ref="sidebar" :inert="!isDesktop && !sidebarOpen" :aria-hidden="!isDesktop && !sidebarOpen" :role="isDesktop ? null : 'dialog'" :aria-modal="!isDesktop && sidebarOpen ? 'true' : null" aria-label="Walleys navigation" @keydown="trapSidebarFocus($event)" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 flex w-64 max-w-[calc(100vw-2rem)] -translate-x-full flex-col border-r border-slate-800 bg-[#172436] transition-transform duration-200 lg:translate-x-0">
    <div class="flex h-[88px] shrink-0 items-center justify-between px-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 rounded-lg"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white"><x-application-logo class="h-8 w-8" /></span><span class="text-xl font-bold tracking-tight text-white">Walleys<span class="text-brand-500">.</span></span></a>
        <button type="button" @click="closeSidebar()" class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-300 hover:bg-white/10 lg:hidden" aria-label="Close navigation"><x-ui-icon name="close" /></button>
    </div>
    <div class="min-h-0 flex-1 overflow-y-auto px-4 py-3">
        <p class="mb-3 px-3.5 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Your finances</p>
        <nav aria-label="Primary" class="space-y-1.5">
            @foreach ([['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'overview', 'label' => 'Overview'], ['route' => 'transactions.index', 'pattern' => 'transactions.*', 'icon' => 'transactions', 'label' => 'Transactions'], ['route' => 'categories.index', 'pattern' => 'categories.*', 'icon' => 'categories', 'label' => 'Category insights']] as $item)
                <a href="{{ route($item['route']) }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs($item['pattern'])]) @if(request()->routeIs($item['pattern'])) aria-current="page" @endif><x-ui-icon :name="$item['icon']" />{{ $item['label'] }}</a>
            @endforeach
        </nav>
        <a href="{{ route('transactions.create') }}" class="ui-button mt-6 w-full"><x-ui-icon name="plus" class="h-4 w-4" />New transaction</a>
    </div>
    <div class="shrink-0 border-t border-white/10 px-4 py-4">
        <a href="{{ route('profile.edit') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('profile.*')]) @if(request()->routeIs('profile.*')) aria-current="page" @endif><x-ui-icon name="settings" />Account settings</a>
        <button type="button" class="sidebar-link mt-1 w-full text-left" @click="closeSidebar(); $nextTick(() => $dispatch('open-modal', 'confirm-logout'))"><x-ui-icon name="logout" />Log out</button>
        <div class="mt-4 flex items-center gap-3 border-t border-white/10 px-2 pt-5">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
            <div class="min-w-0"><p class="truncate text-xs font-semibold text-slate-100" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</p><p class="mt-1 text-[10px] font-medium text-slate-400">Personal workspace</p></div>
        </div>
    </div>
</aside>
<x-modal name="confirm-logout" maxWidth="md" focusable>
    <form method="POST" action="{{ route('logout') }}" class="p-6" role="dialog" aria-modal="true" aria-labelledby="logout-title" aria-describedby="logout-description" x-data="{ submitting: false }" @submit="submitting = true" :aria-busy="submitting">
        @csrf
        <h2 id="logout-title" class="text-xl font-bold text-slate-900">Log out of Walleys?</h2>
        <p id="logout-description" class="mt-3 text-sm leading-relaxed text-slate-600">You will need to sign in again to view your finances. Your saved transactions will be kept.</p>
        <div class="mt-6 flex flex-wrap justify-end gap-3">
            <button type="button" class="ui-button-secondary" @click="$dispatch('close')" :disabled="submitting">Stay signed in</button>
            <button type="submit" class="ui-button" :disabled="submitting"><span x-show="!submitting">Log out</span><span x-show="submitting" x-cloak>Logging out…</span></button>
        </div>
    </form>
</x-modal>
