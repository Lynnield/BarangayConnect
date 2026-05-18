@extends('layouts.app')

@section('title', 'Reports')

@section('breadcrumb')
    <span class="text-slate-500">Reports</span>
@endsection

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <!-- Header Section -->
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Staff Intelligence</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Generate operational reports for document requests and community activities.</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 border border-indigo-500/20">
                <i data-lucide="bar-chart-3" class="h-6 w-6"></i>
            </div>
        </div>
    </x-card>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Report Generator -->
        <div class="lg:col-span-2 space-y-8">
            <x-card title="Generate New Report" icon="plus-circle" class="bg-slate-900/50 border-slate-800">
                <form method="POST" action="{{ route('staff.reports.generate') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Report Type</label>
                            <div class="relative">
                                <select name="type" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                    <option value="requests_summary">Document Requests Summary</option>
                                    <option value="monthly">Monthly Activity Report</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                                    <i data-lucide="chevron-down" class="h-4 w-4"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">From Date</label>
                            <input type="date" name="date_from" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">To Date</label>
                            <input type="date" name="date_to" value="{{ now()->format('Y-m-d') }}"
                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                        </div>
                    </div>

                    <div class="pt-4">
                        <x-button type="submit" variant="primary" size="md" icon="file-text" class="w-full md:w-auto shadow-indigo-600/20">
                            Generate PDF Report
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Recent Exports -->
        <div class="lg:col-span-1">
            <x-table-wrapper title="Your Recent Exports" icon="history">
                <x-slot:action>
                    <x-list-sort
                        default="created_at"
                        defaultDirection="desc"
                        :options="[
                            'created_at' => 'Date generated',
                            'report_name' => 'Report name',
                            'report_type' => 'Report type',
                            'status' => 'Status',
                        ]"
                    />
                </x-slot:action>
                <div class="divide-y divide-slate-800/50">
                    @forelse($recent as $r)
                        <div class="p-4 hover:bg-slate-800/30 transition-all group">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-black text-white text-xs group-hover:text-indigo-400 transition-colors truncate pr-4">
                                    {{ $r->report_name }}
                                </div>
                                @php
                                    $statusType = match($r->status) {
                                        'completed' => 'success',
                                        'failed' => 'danger',
                                        default => 'warning',
                                    };
                                @endphp
                                <x-badge :type="$statusType" size="xs">{{ $r->status }}</x-badge>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                                    {{ $r->created_at->format('M d, Y') }}
                                </div>
                                @if($r->status === 'completed' && $r->file_path)
                                    <a href="{{ asset('storage/'.$r->file_path) }}" target="_blank" class="text-[10px] font-black text-indigo-400 hover:text-indigo-300 transition-colors uppercase tracking-[0.2em] flex items-center gap-1.5">
                                        Download <i data-lucide="download" class="h-3 w-3"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-12 text-center">
                            <i data-lucide="file-stack" class="h-8 w-8 text-slate-700 mx-auto mb-3"></i>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">No recent exports</p>
                        </div>
                    @endforelse
                </div>
            </x-table-wrapper>
        </div>
    </div>
</div>
@endsection
