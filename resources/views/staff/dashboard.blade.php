@extends('layouts.app')
@section('title', 'Staff Dashboard')
@section('breadcrumb')
<span class="text-slate-900">Dashboard</span>
@endsection
@section('content')
<div class="w-full space-y-6">
    <!-- Header Section -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-800">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white leading-tight">Staff Overview</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage daily requests and resident appointments efficiently.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">
        <x-stat-tile label="Pending" :value="$stats['pending_requests']" icon="clock" color="amber" />
        <x-stat-tile label="Under Review" :value="$stats['under_review']" icon="search" color="blue" />
        <x-stat-tile label="Ready for Pickup" :value="$stats['ready_pickup']" icon="package-check" color="indigo" />
        <x-stat-tile label="Today's Appts" :value="$stats['today_appointments']" icon="calendar-days" color="rose" />
        <x-stat-tile label="Residents" :value="$stats['residents']" icon="users" color="emerald" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Requests -->
        <div class="lg:col-span-2">
            <x-table-wrapper title="Recent Requests">
                <x-slot:action>
                    <a href="{{ route('staff.requests.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">View All</a>
                </x-slot:action>
                
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/30 text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold tracking-widest">
                            <th class="px-6 py-3">Reference #</th>
                            <th class="px-6 py-3">Resident</th>
                            <th class="px-6 py-3">Document Type</th>
                            <th class="px-6 py-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentRequests as $r)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 font-mono font-medium text-indigo-600 dark:text-indigo-400">
                                    <a href="{{ route('staff.requests.show', $r) }}">{{ $r->request_number }}</a>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">{{ $r->resident->full_name }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400">{{ $r->documentType->name }}</td>
                                <td class="px-6 py-4 text-right">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/50',
                                            'under_review' => 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800/50',
                                            'ready_pickup' => 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/50',
                                            'completed' => 'bg-indigo-100 text-indigo-700 border-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-400 dark:border-indigo-800/50',
                                            'rejected' => 'bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-800/50',
                                        ];
                                        $statusColor = $statusColors[$r->status] ?? 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700';
                                    @endphp
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-tight {{ $statusColor }}">
                                        {{ str_replace('_', ' ', strtoupper($r->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                    <i data-lucide="inbox" class="mx-auto h-8 w-8 text-slate-200 dark:text-slate-800 mb-2"></i>
                                    <p>No requests found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-table-wrapper>
        </div>

        <!-- Activity -->
        <div>
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col h-full">
                <div class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">System Activity</h3>
                    <i data-lucide="pulse" class="h-4 w-4 text-indigo-500"></i>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800 flex-1">
                    @forelse($recentActivities as $a)
                        <div class="px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-snug">{{ $a->description }}</p>
                            <p class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                                <i data-lucide="clock" class="h-3.5 w-3.5"></i>
                                {{ $a->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-slate-500 text-sm">No recent activity.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
