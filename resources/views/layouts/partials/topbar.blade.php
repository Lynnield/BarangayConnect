<header class="h-16 border-b border-slate-800 flex items-center justify-between px-6 bg-slate-900/50 backdrop-blur-md z-40 shrink-0">
    @php 
        $user = auth()->user();
        $notifications = collect();
        
        // 1. Get Personal Notifications (Database)
        $dbNotifications = $user->unreadNotifications->map(fn($n) => [
            'title' => $n->data['title'] ?? 'Notification',
            'message' => $n->data['message'] ?? '',
            'link' => $n->data['link'] ?? '#',
            'created_at' => $n->created_at,
            'is_system' => false,
            'id' => $n->id
        ]);
        $notifications = $notifications->concat($dbNotifications);

        // 2. Get System Warnings (Only for Admin/Staff)
        if ($user->isAdmin() || $user->isStaff()) {
            $warningService = app(\App\Services\SystemWarningService::class);
            $warnings = $warningService->getAllWarnings()->map(fn($w) => [
                'title' => $w['title'],
                'message' => $w['message'],
                'link' => $w['link'],
                'created_at' => now(), // Warnings are real-time
                'is_system' => true,
                'severity' => $w['severity']
            ]);
            $notifications = $notifications->concat($warnings);
        }

        $totalCount = $notifications->count();
        $notifications = $notifications->sortByDesc('created_at');
    @endphp
    <div class="flex items-center gap-4">
        <button id="sidebarToggle" class="lg:hidden p-2.5 rounded-lg bg-slate-800 hover:bg-indigo-600 text-slate-400 hover:text-white transition-colors duration-200 active:scale-95" title="Open menu">
            <i data-lucide="menu" class="h-5 w-5"></i>
        </button>

        <div class="lg:hidden flex items-center gap-2 flex-shrink-0">
            <x-barangay-logo size="sm" class="ring-1 ring-slate-700" />
            <span class="text-xs font-black text-white hidden sm:block">{{ explode(' ', \App\Models\SystemSetting::get('barangay_name', 'Barangay Connect'))[0] }}</span>
        </div>

        <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em]">
            <span class="text-slate-500">System</span>
            <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
            @yield('breadcrumb')
        </div>
    </div>

    <!-- Right Side Actions -->
    <div class="flex items-center gap-4">
        <!-- Notifications -->
        <div class="relative">
            <button id="notifBtn" 
                    data-toggle="dropdown" 
                    data-target="notification-dropdown"
                    class="relative rounded-xl p-2.5 text-slate-500 hover:bg-slate-800 hover:text-white transition-all active:scale-95">
                <i data-lucide="bell" class="h-5 w-5"></i>
                <span id="notifDot" class="absolute top-2 right-2 h-2 w-2 rounded-full bg-rose-500 border-2 border-slate-900 {{ $totalCount > 0 ? '' : 'hidden' }}"></span>
            </button>
            
            <div id="notification-dropdown" class="absolute right-0 mt-2 w-80 origin-top-right rounded-2xl border border-slate-800 bg-slate-950 shadow-2xl ring-1 ring-black ring-opacity-5 hidden z-50">
                <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest">Notifications</h3>
                    <span id="notifCount" class="text-[10px] font-bold text-slate-500">{{ $totalCount }} New</span>
                </div>
                <div class="max-h-96 overflow-y-auto py-2">
                    @forelse($notifications->take(10) as $n)
                        <a href="{{ $n['link'] }}" 
                           onclick="markAsRead(event, '{{ $n['id'] ?? '' }}')"
                           class="block px-4 py-3 hover:bg-slate-900 transition-colors border-l-2 {{ $n['is_system'] ? 'border-amber-500/50' : 'border-indigo-500/50' }}">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-black text-white">{{ $n['title'] }}</p>
                                @if($n['is_system'])
                                    <span class="text-[8px] font-black text-amber-500 uppercase tracking-widest">System</span>
                                @endif
                            </div>
                            <p class="text-[10px] text-slate-500 leading-relaxed">{{ $n['message'] }}</p>
                            <p class="text-[8px] text-slate-600 font-bold uppercase tracking-tight mt-2">{{ $n['created_at']->diffForHumans() }}</p>
                        </a>
                    @empty
                        <div class="px-4 py-12 text-center">
                            <div class="h-12 w-12 rounded-full bg-slate-900 flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="bell-off" class="h-6 w-6 text-slate-700"></i>
                            </div>
                            <p class="text-slate-600 text-[10px] font-bold uppercase tracking-widest">No new notifications</p>
                        </div>
                    @endforelse
                </div>
                @if($totalCount > 0)
                    <div class="p-3 border-t border-slate-800">
                        <button onclick="markAllRead(event)" class="w-full py-2 rounded-xl bg-slate-900 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-white hover:bg-slate-800 transition-all">
                            Mark all as read
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- User Menu -->
        <div class="relative">
            <button id="userMenuBtn"
                    data-toggle="dropdown" 
                    data-target="user-dropdown"
                    class="flex items-center gap-3 rounded-2xl p-1.5 pr-4 bg-slate-900 border border-slate-800 hover:border-indigo-500/50 transition-all active:scale-95 group">
                <img src="{{ auth()->user()->avatar_url }}" class="h-8 w-8 rounded-xl border border-slate-700 object-cover shadow-lg group-hover:border-indigo-500/30 transition-colors">
                <div class="hidden md:flex flex-col text-left">
                    <div class="flex items-center gap-2">
                        <p class="text-[10px] font-black text-white leading-none tracking-tight">{{ auth()->user()->name }}</p>
                        @php
                            $roleClass = match(true) {
                                auth()->user()->isAdmin() => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                auth()->user()->isStaff() => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                default => 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20',
                            };
                            $roleName = match(true) {
                                auth()->user()->isAdmin() => 'Admin',
                                auth()->user()->isStaff() => 'Staff',
                                default => 'Resident',
                            };
                        @endphp
                        <span class="px-1.5 py-0.5 rounded-md border {{ $roleClass }} text-[8px] font-black uppercase tracking-widest">{{ $roleName }}</span>
                    </div>
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-1">Active Session</p>
                </div>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 text-slate-600 group-hover:text-slate-400 transition-colors"></i>
            </button>

            <div id="user-dropdown" class="absolute right-0 mt-2 w-52 origin-top-right rounded-2xl border border-slate-800 bg-slate-950 shadow-2xl ring-1 ring-black ring-opacity-5 hidden z-50 overflow-hidden">
                <div class="py-2">
                    <div class="px-4 py-2 border-b border-slate-800 mb-2">
                        <p class="text-[10px] font-black text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] font-bold text-slate-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                    
                    @if(auth()->user()->isResident())
                        <a href="{{ route('resident.profile.show') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-400 hover:text-white hover:bg-slate-900 transition-colors">
                            <i data-lucide="user" class="h-4 w-4"></i>
                            My Profile
                        </a>
                    @endif
                    
                    <a href="{{ route('account.security.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-400 hover:text-white hover:bg-slate-900 transition-colors">
                        <i data-lucide="shield-check" class="h-4 w-4"></i>
                        Security Settings
                    </a>
                    
                    <div class="h-px bg-slate-800 my-2"></div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-xs font-bold text-rose-500 hover:bg-rose-500/5 transition-colors">
                            <i data-lucide="log-out" class="h-4 w-4"></i>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script nonce="{{ $cspNonce }}">
function markAllRead(e) {
    e.preventDefault();
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('{{ route('notifications.read-all-global') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    }).then(() => {
        // Refresh the dropdown or hide badges
        window.location.reload();
    });
}

function markAsRead(e, id) {
    if (!id || id.length <= 10) {
        return;
    }

    e.preventDefault();
    const link = e.currentTarget;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        keepalive: true,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    }).then(response => response.json())
      .then(payload => {
          updateUnreadUi(payload.count ?? 0);
          link.remove();
          window.location.href = link.href;
      })
      .catch(() => {
          window.location.href = link.href;
      });
}

function updateUnreadUi(count) {
    const label = document.getElementById('notifCount');
    const dot = document.getElementById('notifDot');
    if (label) label.textContent = `${count} New`;
    if (dot) dot.classList.toggle('hidden', count < 1);
}

setInterval(() => {
    fetch('{{ route('notifications.count') }}')
        .then(response => response.json())
        .then(payload => {
            updateUnreadUi(payload.count ?? 0);
        });
}, 30000);
</script>
