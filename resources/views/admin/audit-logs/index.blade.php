@extends('layouts.app')

@section('title', 'Audit Logs')

@section('breadcrumb')
    <span class="text-slate-500">Audit Logs</span>
@endsection

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <!-- Header Section -->
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">System Audit Trail</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Track all system activities, modifications, and administrative actions.</p>
            </div>
            <x-button href="{{ route('admin.audit-logs.export', request()->query()) }}" variant="secondary" size="md" icon="download">
                Export Log History
            </x-button>
        </div>
    </x-card>

    <!-- Advanced Filters -->
    <x-card class="bg-slate-900/50 border-slate-800" :padding="false">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="p-6 grid grid-cols-1 md:grid-cols-5 gap-6 items-end">
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Search Logs</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                        <i data-lucide="search" class="h-4 w-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 pl-11 pr-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner" 
                        placeholder="Search by user, action, or description...">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Module</label>
                <div class="relative group">
                    <select name="module" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none shadow-inner">
                        <option value="">All Modules</option>
                        @foreach($modules as $m)
                            <option value="{{ $m }}" @selected(request('module') == $m)>{{ $m }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                        <i data-lucide="chevron-down" class="h-4 w-4"></i>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Date Range</label>
                <div class="flex gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-3 text-[10px] text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-3 text-[10px] text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner">
                </div>
            </div>
            @php
                $filtersActive = request()->anyFilled(['search', 'module', 'date_from', 'date_to'])
                    || request()->has('sort')
                    || request()->has('direction');
            @endphp
            <div class="flex items-end gap-3 flex-nowrap">
                <x-list-sort
                    inline
                    class="min-w-0 flex-1"
                    default="created_at"
                    defaultDirection="desc"
                    :options="[
                        'created_at' => 'Timestamp',
                        'module' => 'Module',
                        'action' => 'Action',
                    ]"
                />
                <x-button type="submit" variant="secondary" size="md" class="shrink-0" icon="filter">Filter</x-button>
                <x-filter-reset :route="route('admin.audit-logs.index')" :active="$filtersActive" />
            </div>
        </form>
    </x-card>

    <!-- Table Section -->
    <x-table-wrapper title="Activity Log" icon="scroll-text">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-900/50 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    <th class="px-6 py-4">Timestamp</th>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Module</th>
                    <th class="px-6 py-4">Action</th>
                    <th class="px-6 py-4">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-800/30 transition-all group">
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-slate-300">{{ $log->created_at->format('M d, Y') }}</div>
                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $log->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] font-black text-slate-400 border border-slate-700">
                                    {{ substr($log->user_name, 0, 1) }}
                                </div>
                                <div class="text-sm font-bold text-slate-300">{{ $log->user_name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <x-badge type="neutral">{{ $log->module }}</x-badge>
                        </td>
                        <td class="px-6 py-5">
                            <span @class([
                                'text-[10px] font-black uppercase tracking-widest',
                                'text-indigo-400' => in_array($log->action, ['create', 'store']),
                                'text-blue-400' => in_array($log->action, ['update', 'edit']),
                                'text-rose-400' => in_array($log->action, ['delete', 'destroy']),
                                'text-slate-400' => !in_array($log->action, ['create', 'store', 'update', 'edit', 'delete', 'destroy']),
                            ])>
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-xs text-slate-400 font-medium max-w-md line-clamp-1 group-hover:text-slate-200 transition-colors" title="{{ $log->description }}">
                                {{ $log->description }}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div class="h-20 w-20 rounded-3xl bg-slate-800/50 flex items-center justify-center text-slate-700 shadow-inner">
                                    <i data-lucide="history" class="h-10 w-10"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-white">No Logs Found</h3>
                                    <p class="text-sm text-slate-500 max-w-xs mx-auto mt-1 font-medium italic">We couldn't find any activity logs matching your search criteria.</p>
                                </div>
                                <x-button href="{{ route('admin.audit-logs.index') }}" variant="outline" size="sm" icon="rotate-ccw">
                                    Clear Filters
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
            <x-slot:footer>
                <div class="px-2">
                    {{ $logs->links() }}
                </div>
            </x-slot:footer>
        @endif
    </x-table-wrapper>
</div>
@endsection