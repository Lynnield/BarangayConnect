@extends('layouts.app')
@section('title', 'My Dashboard')
@section('breadcrumb')
<span class="text-slate-400 font-medium">Dashboard</span>
@endsection
@section('content')
<div class="w-full space-y-6">
    <!-- Top Row: Welcome Banner & Action Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Welcome Banner -->
        <div class="lg:col-span-2 bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden group transition-all duration-500 hover:shadow-indigo-500/20">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white text-[10px] font-black uppercase tracking-widest mb-6 backdrop-blur-md">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                    </span>
                    Official Portal
                </div>
                <h1 class="text-4xl font-black text-white mb-4 tracking-tight leading-tight">Welcome back,<br/><span class="text-indigo-200">{{ auth()->user()->name }}</span>!</h1>
                <p class="text-indigo-100/80 max-w-lg leading-relaxed text-lg font-medium">
                    Monitor your document status and schedule appointments through our digital-first community platform.
                </p>
                <div class="mt-10 flex flex-wrap items-center gap-6 text-sm text-indigo-100/60 font-black uppercase tracking-widest">
                    <span class="flex items-center gap-2">
                        <i data-lucide="map-pin" class="h-4 w-4"></i>
                        Barangay San Jose
                    </span>
                    <span class="flex items-center gap-2 border-l border-white/10 pl-6">
                        <i data-lucide="calendar" class="h-4 w-4"></i>
                        {{ now()->format('F d, Y') }}
                    </span>
                </div>
            </div>
            <!-- Decorative Elements -->
            <div class="absolute -right-16 -bottom-16 h-80 w-80 rounded-full bg-white/5 blur-3xl transition-all duration-700 group-hover:bg-white/10 group-hover:scale-125"></div>
            <i data-lucide="layout-dashboard" class="absolute -right-8 -top-8 h-64 w-64 text-white/[0.05] -rotate-12 transition-transform duration-700 group-hover:rotate-0"></i>
        </div>

        <!-- Action Card -->
        <div class="bg-slate-950 rounded-3xl p-8 border border-slate-800 shadow-xl flex flex-col items-center justify-center text-center group transition-all duration-500 hover:border-indigo-500/50 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10">
                <div class="mb-8 h-20 w-20 rounded-3xl bg-indigo-600/10 flex items-center justify-center text-indigo-500 transition-all duration-500 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white shadow-xl">
                    <i data-lucide="file-plus-2" class="h-10 w-10"></i>
                </div>
                <h3 class="text-2xl font-black text-white mb-3 tracking-tight">Request Documents</h3>
                <p class="text-slate-500 text-sm mb-8 px-4 leading-relaxed font-medium">Quickly apply for clearances and certificates online.</p>
                
                @if($resident)
                    <a href="{{ route('resident.requests.create') }}" class="inline-flex items-center justify-center gap-2 w-full bg-indigo-600 text-white font-black py-4 rounded-2xl transition-all hover:bg-indigo-500 hover:shadow-indigo-600/40 active:scale-95">
                        New Application
                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </a>
                @else
                    <a href="{{ route('resident.profile.edit') }}" class="inline-flex items-center justify-center gap-2 w-full bg-amber-500 text-amber-950 font-black py-4 rounded-2xl transition-all hover:bg-amber-400 active:scale-95">
                        Complete Profile
                        <i data-lucide="user-plus" class="h-4 w-4"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Counters Row: Summary KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach([
            ['label' => 'All Requests', 'val' => $resident ? $resident->documentRequests()->count() : 0, 'icon' => 'layers', 'color' => 'blue'],
            ['label' => 'Released', 'val' => $stats['released'], 'icon' => 'check-circle', 'color' => 'emerald'],
            ['label' => 'In Process', 'val' => $stats['active_requests'], 'icon' => 'clock', 'color' => 'amber'],
            ['label' => 'Appointments', 'val' => $stats['upcoming_appointments'], 'icon' => 'calendar-days', 'color' => 'indigo'],
        ] as $s)
        <div class="bg-slate-950 border border-slate-800 rounded-3xl p-6 transition-all duration-300 hover:border-slate-700 hover:-translate-y-1 group">
            <div class="flex items-center gap-5">
                <div class="h-14 w-14 rounded-2xl bg-{{ $s['color'] }}-500/10 text-{{ $s['color'] }}-500 flex items-center justify-center shadow-inner transition-colors group-hover:bg-{{ $s['color'] }}-500 group-hover:text-white">
                    <i data-lucide="{{ $s['icon'] }}" class="h-7 w-7"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ $s['label'] }}</p>
                    <p class="text-3xl font-black text-white mt-1 tracking-tighter">{{ number_format($s['val']) }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Data Rows: Recent Requests & Appointments -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Requests Table -->
        <div class="bg-slate-950 border border-slate-800 rounded-3xl overflow-hidden flex flex-col shadow-xl">
            <div class="px-8 py-6 border-b border-slate-800 flex items-center justify-between bg-slate-900/30">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-indigo-500/10 text-indigo-500 flex items-center justify-center">
                        <i data-lucide="history" class="h-4 w-4"></i>
                    </div>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Recent Activity</h3>
                </div>
                <a href="{{ route('resident.requests.index') }}" class="text-[10px] font-black text-indigo-400 hover:text-indigo-300 transition-colors uppercase tracking-widest">View History</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-950/50 text-[10px] font-black text-slate-600 uppercase tracking-[0.2em]">
                        <tr>
                            <th class="px-8 py-4">Reference</th>
                            <th class="px-8 py-4">Document</th>
                            <th class="px-8 py-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-900">
                        @forelse($recentRequests as $r)
                            <tr class="hover:bg-slate-900/50 transition-colors group cursor-pointer" onclick="window.location.href='{{ route('resident.requests.show', $r) }}'">
                                <td class="px-8 py-5">
                                    <span class="font-mono text-sm text-indigo-400 font-black group-hover:text-indigo-300 transition-colors">{{ $r->request_number }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="font-bold text-slate-300 text-sm group-hover:text-white transition-colors">{{ $r->documentType->name }}</div>
                                    <div class="text-[10px] text-slate-600 font-bold mt-1 uppercase tracking-tight">{{ $r->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    @php
                                        $statusClass = match($r->status) {
                                            'pending' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                            'released', 'completed' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                            'rejected' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                            default => 'bg-blue-500/10 text-blue-500 border-blue-500/20'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter border {{ $statusClass }}">
                                        {{ str_replace('_', ' ', $r->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-8 py-16 text-center text-slate-600 italic font-medium">No recent applications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upcoming Appointments List -->
        <div class="bg-slate-950 border border-slate-800 rounded-3xl overflow-hidden flex flex-col shadow-xl">
            <div class="px-8 py-6 border-b border-slate-800 flex items-center justify-between bg-slate-900/30">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                        <i data-lucide="calendar-check" class="h-4 w-4"></i>
                    </div>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Appointments</h3>
                </div>
                <i data-lucide="more-horizontal" class="h-4 w-4 text-slate-700"></i>
            </div>
            <div class="flex-1 divide-y divide-slate-900">
                @forelse($upcoming as $a)
                    <div class="px-8 py-5 flex items-center gap-5 hover:bg-slate-900/50 transition-colors group">
                        <div class="flex flex-col items-center justify-center rounded-2xl bg-slate-900 border border-slate-800 px-4 py-3 text-indigo-400 min-w-[70px] transition-all group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-500 shadow-inner">
                            <span class="text-[10px] font-black uppercase tracking-tighter">{{ $a->appointment_date->format('M') }}</span>
                            <span class="text-2xl font-black leading-none mt-1">{{ $a->appointment_date->format('d') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="truncate text-sm font-black text-white group-hover:text-indigo-400 transition-colors">{{ $a->documentRequest?->documentType->name ?? 'Office Visit' }}</p>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5 font-bold uppercase tracking-widest">
                                <i data-lucide="clock" class="h-3.5 w-3.5 text-indigo-500"></i>
                                {{ \Carbon\Carbon::parse($a->appointment_time)->format('g:i A') }}
                            </p>
                        </div>
                        <div class="text-right">
                             <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                {{ $a->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="flex-1 flex flex-col items-center justify-center px-8 py-16 text-center space-y-4">
                        <div class="h-16 w-16 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-700">
                            <i data-lucide="calendar-off" class="h-8 w-8"></i>
                        </div>
                        <p class="text-sm text-slate-500 italic font-medium">No upcoming appointments scheduled.</p>
                        <a href="{{ route('resident.appointments.create') }}" class="text-[10px] font-black text-indigo-500 hover:text-indigo-400 uppercase tracking-[0.2em] border-b-2 border-indigo-500/20 hover:border-indigo-500 transition-all pb-1">Schedule Now</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Profile Integrity Warning -->
    @if(!$resident)
    <div class="bg-amber-500/10 border border-amber-500/20 rounded-3xl p-6 flex items-center gap-6 animate-pulse">
        <div class="h-12 w-12 rounded-2xl bg-amber-500/20 text-amber-500 flex items-center justify-center shrink-0 shadow-lg">
            <i data-lucide="alert-octagon" class="h-6 w-6"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-black text-white uppercase tracking-wider">Account Identity Incomplete</p>
            <p class="text-xs text-amber-200/60 mt-1 font-medium leading-relaxed">Please update your residential information to unlock document requesting features.</p>
        </div>
        <a href="{{ route('resident.profile.edit') }}" class="bg-amber-500 text-amber-950 font-black px-6 py-3 rounded-2xl text-[10px] uppercase tracking-widest hover:bg-amber-400 transition-all active:scale-95 shadow-xl shadow-amber-900/20">Fix Identity</a>
    </div>
    @endif
</div>
@endsection
