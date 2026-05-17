<div class="space-y-4">
    <div>
        <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('staff.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
            <span>Dashboard</span>
        </a>
    </div>

    <div>
        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-2">Requests</p>
        <div class="space-y-1">
            <a href="{{ route('staff.requests.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('staff.requests.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="clipboard-list" class="h-4 w-4"></i>
                <span>Document Requests</span>
                @php $p = \App\Models\DocumentRequest::where('status','pending')->count(); @endphp
                @if($p > 0)
                    <span class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white">{{ $p }}</span>
                @endif
            </a>
            <a href="{{ route('staff.appointments.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('staff.appointments.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="calendar-check" class="h-4 w-4"></i>
                <span>Appointments</span>
            </a>
            <a href="{{ route('staff.residents.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('staff.residents.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="users" class="h-4 w-4"></i>
                <span>Residents</span>
            </a>
        </div>
    </div>

    <div>
        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-2">Reports</p>
        <div class="space-y-1">
            <a href="{{ route('staff.reports.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('staff.reports.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="bar-chart-3" class="h-4 w-4"></i>
                <span>Reports</span>
            </a>
        </div>
    </div>

</div>
