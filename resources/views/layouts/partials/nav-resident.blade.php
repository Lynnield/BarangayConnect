<div class="space-y-4">
    <div>
        <a href="{{ route('resident.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('resident.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <i data-lucide="home" class="h-4 w-4"></i>
            <span>My Dashboard</span>
        </a>
    </div>

    <div>
        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-2">Documents</p>
        <div class="space-y-1">
            <a href="{{ route('resident.requests.create') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('resident.requests.create') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="file-plus" class="h-4 w-4"></i>
                <span>Request Document</span>
            </a>
            <a href="{{ route('resident.requests.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('resident.requests.index') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="clipboard-list" class="h-4 w-4"></i>
                <span>My Requests</span>
            </a>
            <a href="{{ route('resident.appointments.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('resident.appointments.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="calendar-check" class="h-4 w-4"></i>
                <span>My Appointments</span>
            </a>
        </div>
    </div>

</div>
