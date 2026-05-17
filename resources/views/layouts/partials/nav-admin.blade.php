<div class="space-y-4">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
            <span>Dashboard</span>
        </a>
    </div>

    <div>
        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-2">Management</p>
        <div class="space-y-1">
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="users" class="h-4 w-4"></i>
                <span>User Management</span>
            </a>
            <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.roles.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="shield-check" class="h-4 w-4"></i>
                <span>Roles & Permissions</span>
            </a>
            <a href="{{ route('admin.residents.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.residents.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="contact-2" class="h-4 w-4"></i>
                <span>Residents</span>
            </a>
            <a href="{{ route('admin.document-types.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.document-types.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="file-text" class="h-4 w-4"></i>
                <span>Document Types</span>
            </a>
        </div>
    </div>

    <div>
        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-2">Operations</p>
        <div class="space-y-1">
            <a href="{{ route('admin.requests.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.requests.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="clipboard-list" class="h-4 w-4"></i>
                <span>All Requests</span>
                @php $pending = \App\Models\DocumentRequest::where('status','pending')->count(); @endphp
                @if($pending > 0)
                    <span class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white">{{ $pending }}</span>
                @endif
            </a>
            <a href="{{ route('admin.appointments.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.appointments.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="calendar-check" class="h-4 w-4"></i>
                <span>Appointments</span>
            </a>
        </div>
    </div>

    <div>
        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-2">Reports & Logs</p>
        <div class="space-y-1">
            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="bar-chart-3" class="h-4 w-4"></i>
                <span>Reports</span>
            </a>
            <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.audit-logs.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="scroll-text" class="h-4 w-4"></i>
                <span>Audit Logs</span>
            </a>
        </div>
    </div>

    <div>
        <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-2">System</p>
        <div class="space-y-1">
            <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="settings" class="h-4 w-4"></i>
                <span>Settings</span>
            </a>
            <a href="{{ route('admin.backups.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.backups.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="database" class="h-4 w-4"></i>
                <span>Backups</span>
            </a>
            <a href="{{ route('admin.trash.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.trash.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="archive-restore" class="h-4 w-4"></i>
                <span>Trash & Restore</span>
            </a>
        </div>
    </div>
</div>
