@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('breadcrumb')
<span class="text-slate-900">Dashboard</span>
@endsection
@section('content')
<div class="w-full space-y-6">
    <!-- Top Overview & Filter -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white leading-tight">System Overview</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Welcome back, {{ auth()->user()->name }}. Here's what's happening today.</p>
        </div>
        <form method="get" class="flex items-center gap-2">
            <div class="flex items-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-1.5 shadow-sm">
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="border-0 bg-transparent p-0 text-xs font-semibold text-slate-700 dark:text-slate-300 focus:ring-0">
                <span class="text-slate-300 dark:text-slate-600">—</span>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="border-0 bg-transparent p-0 text-xs font-semibold text-slate-700 dark:text-slate-300 focus:ring-0">
            </div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-500 transition-all active:scale-95">
                <i data-lucide="filter" class="h-3.5 w-3.5"></i>
                Filter
            </button>
        </form>
    </div>

    <!-- System Warnings -->
    @if($warnings->count() > 0)
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                <i data-lucide="alert-circle" class="h-4 w-4 text-rose-500"></i>
                System Warnings
                <span class="ml-2 inline-flex items-center rounded-full bg-rose-100 dark:bg-rose-900/30 px-2 py-0.5 text-[10px] font-bold text-rose-700 dark:text-rose-400">
                    {{ $warningCounts['total'] }}
                </span>
            </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($warnings->take(6) as $w)
                @php
                    $severityClasses = [
                        'critical' => 'border-rose-100 bg-rose-50/50 dark:bg-rose-900/10 text-rose-900 dark:text-rose-300',
                        'warning' => 'border-amber-100 bg-amber-50/50 dark:bg-amber-900/10 text-amber-900 dark:text-amber-300',
                        'info' => 'border-blue-100 bg-blue-50/50 dark:bg-blue-900/10 text-blue-900 dark:text-blue-300',
                    ][$w['severity']] ?? 'border-slate-100 bg-white dark:bg-slate-900 text-slate-900 dark:text-white';
                    
                    $icon = [
                        'critical' => 'shield-alert',
                        'warning' => 'alert-triangle',
                        'info' => 'info',
                    ][$w['severity']] ?? 'alert-circle';

                    $iconColor = [
                        'critical' => 'text-rose-600 dark:text-rose-400',
                        'warning' => 'text-amber-600 dark:text-amber-400',
                        'info' => 'text-blue-600 dark:text-blue-400',
                    ][$w['severity']] ?? 'text-slate-600 dark:text-slate-400';
                @endphp
                <a href="{{ $w['link'] }}" class="group block rounded-xl border p-4 shadow-sm transition-all hover:shadow-md hover:scale-[1.01] {{ $severityClasses }}">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 {{ $iconColor }}">
                            <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold truncate">{{ $w['title'] }}</h4>
                            <p class="mt-1 text-xs opacity-75 leading-relaxed line-clamp-2">{{ $w['message'] }}</p>
                        </div>
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <x-stat-tile label="Residents" :value="$stats['total_residents']" icon="users" color="indigo" />
        <x-stat-tile label="Requests" :value="$stats['total_requests']" icon="file-text" color="blue" />
        <x-stat-tile label="Pending" :value="$stats['pending_requests']" icon="clock" color="amber" />
        <x-stat-tile label="Approved" :value="$stats['approved_requests']" icon="check-circle" color="emerald" />
        <x-stat-tile label="Rejected" :value="$stats['rejected_requests']" icon="x-circle" color="rose" />
        <x-stat-tile label="Upcoming" :value="$stats['upcoming_appointments']" icon="calendar" color="slate" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Charts Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Monthly Trend -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Monthly Request Trend</h3>
                    <div class="flex items-center gap-2">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500 uppercase">
                            <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
                            Requests
                        </span>
                    </div>
                </div>
                <div class="h-[300px]">
                    <canvas id="chartMonthly"></canvas>
                </div>
            </div>

            <!-- Status Breakdown -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6">Request Status Breakdown</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="h-[240px]">
                        <canvas id="chartStatus"></canvas>
                    </div>
                    <div id="chartLegend" class="space-y-3">
                        <!-- Legend will be populated by JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity & Appts Column -->
        <div class="space-y-6">
            <!-- Recent Activity -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
                <div class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Recent Activity</h3>
                    <i data-lucide="activity" class="h-4 w-4 text-indigo-500"></i>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentActivities as $a)
                        <div class="px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ $a->description }}</p>
                            <p class="mt-2 text-[10px] font-bold text-slate-400 flex items-center gap-1.5 uppercase">
                                <i data-lucide="clock" class="h-3.5 w-3.5"></i>
                                {{ $a->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <i data-lucide="inbox" class="mx-auto h-8 w-8 text-slate-200 dark:text-slate-800 mb-2"></i>
                            <p class="text-sm text-slate-500">No activity yet.</p>
                        </div>
                    @endforelse
                </div>
                @if($recentActivities->count() > 0)
                <div class="border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30 px-6 py-3">
                    <a href="{{ route('admin.audit-logs.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 flex items-center justify-center gap-2">
                        View Full Audit Log
                        <i data-lucide="arrow-right" class="h-3 w-3"></i>
                    </a>
                </div>
                @endif
            </div>

            <!-- Upcoming Appointments -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
                <div class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">Upcoming Appointments</h3>
                    <i data-lucide="calendar-clock" class="h-4 w-4 text-indigo-500"></i>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($upcomingAppointments as $ap)
                        <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex flex-col items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/30 px-3 py-2 text-indigo-700 dark:text-indigo-400 min-w-[60px]">
                                <span class="text-[10px] font-bold uppercase tracking-tight">{{ $ap->appointment_date->format('M') }}</span>
                                <span class="text-xl font-black leading-none">{{ $ap->appointment_date->format('d') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ $ap->resident->full_name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5">
                                    <i data-lucide="clock" class="h-3.5 w-3.5 text-indigo-500"></i>
                                    {{ \Carbon\Carbon::parse($ap->appointment_time)->format('g:i A') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <i data-lucide="calendar-off" class="mx-auto h-8 w-8 text-slate-200 dark:text-slate-800 mb-2"></i>
                            <p class="text-sm text-slate-500">None scheduled.</p>
                        </div>
                    @endforelse
                </div>
                <div class="border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30 px-6 py-3">
                    <a href="{{ route('admin.appointments.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 flex items-center justify-center gap-2">
                        View Calendar
                        <i data-lucide="arrow-right" class="h-3 w-3"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js" nonce="{{ $cspNonce }}"></script>
<script nonce="{{ $cspNonce }}">
if (typeof Chart === 'undefined') {
    document.getElementById('chartMonthly')?.insertAdjacentHTML('afterend', '<p class="mt-4 text-sm font-bold text-slate-500">Charts are unavailable.</p>');
    document.getElementById('chartStatus')?.insertAdjacentHTML('afterend', '<p class="mt-4 text-sm font-bold text-slate-500">Charts are unavailable.</p>');
} else {
// Chart Defaults
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#64748b';

// Monthly Trend
fetch("{{ route('admin.dashboard.chart-data') }}?type=monthly")
    .then(r => r.json()).then(rows => {
        rows = Array.isArray(rows) ? rows : [];
        const labels = rows.length ? rows.map(r => r.period ?? '') : ['No data'];
        const totals = rows.length ? rows.map(r => Number(r.total ?? 0)) : [0];
        new Chart(document.getElementById('chartMonthly'), {
            type: 'line',
            data: { 
                labels, 
                datasets: [{ 
                    label: 'Requests', 
                    data: totals, 
                    borderColor: '#4f46e5', 
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }] 
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }).catch(() => {});

// Status Breakdown
fetch("{{ route('admin.dashboard.chart-data') }}?type=status")
    .then(r => r.json()).then(rows => {
        rows = Array.isArray(rows) ? rows : [];
        const colors = ['#f59e0b', '#0ea5e9', '#64748b', '#10b981', '#f43f5e', '#6366f1', '#1e293b'];
        const labels = rows.length ? rows.map(r => r.status ?? 'unknown') : ['No data'];
        const counts = rows.length ? rows.map(r => Number(r.count ?? 0)) : [0];
        const chart = new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ 
                    data: counts, 
                    backgroundColor: colors,
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: { legend: { display: false } }
            }
        });

        // Generate Custom Legend
        const legendContainer = document.getElementById('chartLegend');
        legendContainer.innerHTML = '';
        if (!rows.length) {
            legendContainer.innerHTML = '<p class="text-sm font-bold text-slate-500">No request status data yet.</p>';
            return;
        }
        rows.forEach((row, i) => {
            const item = document.createElement('div');
            item.className = 'flex items-center justify-between text-sm';
            item.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: ${colors[i]}"></span>
                    <span class="text-slate-600 font-medium">${row.status}</span>
                </div>
                <span class="font-bold text-slate-900">${row.count}</span>
            `;
            legendContainer.appendChild(item);
        });
    });
}
</script>
@endpush
