<aside id="sidebar" class="w-64 flex-shrink-0 bg-slate-950 border-r border-slate-800 flex flex-col justify-between p-4 transition-transform duration-300 lg:translate-x-0 -translate-x-full fixed lg:static inset-y-0 left-0 z-50 h-full">
    <div class="flex flex-col h-full space-y-8">
        <!-- Brand Logo & Mobile Close Button -->
        <div class="flex items-center justify-between px-2 shrink-0">
            <div class="flex items-center gap-3">
                <x-barangay-logo size="lg" class="shadow-lg shadow-indigo-900/40" />
                <div class="flex flex-col">
                    <span class="text-sm font-black text-white tracking-tight leading-none text-lg">{{ \App\Models\SystemSetting::get('barangay_name', 'San Jose') }}</span>
                    <span class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em] mt-1">Connect</span>
                </div>
            </div>
            <!-- Mobile Close Button -->
            <button id="sidebarClose" class="lg:hidden p-2.5 rounded-lg bg-slate-800 hover:bg-rose-600 text-slate-400 hover:text-white transition-colors duration-200 active:scale-95" title="Close menu">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-1 px-2 overflow-y-auto scrollbar-none">
            @if(auth()->user()->isAdmin())
                @include('layouts.partials.nav-admin')
            @elseif(auth()->user()->isStaff())
                @include('layouts.partials.nav-staff')
            @else
                @include('layouts.partials.nav-resident')
            @endif
        </nav>

        <!-- Sidebar Footer -->
        <div class="pt-4 border-t border-slate-800 px-2 shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-black text-slate-400 hover:bg-rose-500/10 hover:text-rose-500 transition-all group">
                    <i data-lucide="log-out" class="h-5 w-5 transition-transform group-hover:-translate-x-1"></i>
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</aside>
