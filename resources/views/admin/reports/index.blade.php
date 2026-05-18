@extends('layouts.app')

@section('title', 'System Reports')

@section('breadcrumb')
    <span class="text-slate-500">Reports</span>
@endsection

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <!-- Header Section -->
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Intelligence & Reports</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Generate comprehensive analytical reports for community data and operations.</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 border border-indigo-500/20">
                <i data-lucide="bar-chart-3" class="h-6 w-6"></i>
            </div>
        </div>
    </x-card>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Report Generator -->
        <div class="lg:col-span-2 space-y-8">
            <x-card title="Generate New Report" class="bg-slate-900/50 border-slate-800">
                <form method="POST" action="{{ route('admin.reports.generate') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Report Type</label>
                            <div class="relative">
                                <select name="type" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                    @foreach(['daily'=>'Daily Activity','weekly'=>'Weekly Overview','monthly'=>'Monthly Summary','requests_summary'=>'Document Requests Summary','residents'=>'Resident Demographics'] as $k=>$label)
                                        <option value="{{ $k }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                                    <i data-lucide="chevron-down" class="h-4 w-4"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Output Format</label>
                            <div class="relative">
                                <select name="format" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                    <option value="pdf">Professional PDF Document</option>
                                    <option value="csv">Raw CSV Spreadsheet</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                                    <i data-lucide="chevron-down" class="h-4 w-4"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Date Range (From)</label>
                            <input type="date" name="date_from" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Date Range (To)</label>
                            <input type="date" name="date_to" value="{{ now()->format('Y-m-d') }}"
                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-5">
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <div>
                                <h3 class="text-xs font-black text-white uppercase tracking-widest">Custom Report Builder</h3>
                                <p class="text-[10px] text-slate-500 mt-1">Choose columns and optionally save this configuration.</p>
                            </div>
                            <i data-lucide="sliders-horizontal" class="h-4 w-4 text-indigo-400"></i>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach(['request_number','status','type','created_at','resident_number','full_name','gender','address','contact'] as $column)
                                <label class="flex items-center gap-2 rounded-xl border border-slate-800 bg-slate-900 px-3 py-2 text-[10px] font-bold text-slate-300">
                                    <input type="checkbox" name="columns[]" value="{{ $column }}" class="rounded border-slate-600 bg-slate-800 text-indigo-600" @checked(in_array($column, ['request_number','status','type','created_at']))>
                                    {{ str_replace('_', ' ', $column) }}
                                </label>
                            @endforeach
                        </div>
                        <input type="text" name="favorite_name" placeholder="Favorite name (optional)"
                            class="mt-4 block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                    </div>

                    <div class="pt-4">
                        <x-button type="submit" variant="primary" size="md" icon="file-plus" class="w-full md:w-auto shadow-indigo-600/20">
                            Process & Generate Report
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Recent Reports -->
        <div class="lg:col-span-1">
            @if(!empty($favorites))
                <x-card title="Favorite Builders" icon="star" class="mb-8 bg-slate-900/50 border-slate-800">
                    <div class="space-y-3">
                        @foreach($favorites as $favorite)
                            <div class="rounded-xl border border-slate-800 bg-slate-950/50 p-3">
                                <p class="text-xs font-black text-white">{{ $favorite['name'] ?? 'Saved report' }}</p>
                                <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                                    {{ str_replace('_', ' ', $favorite['type'] ?? '') }} / {{ strtoupper($favorite['format'] ?? '') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif

            <x-table-wrapper title="Recent Exports" icon="history">
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
                    @forelse($recent as $rep)
                        <div class="p-4 hover:bg-slate-800/30 transition-all group">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-black text-white text-xs group-hover:text-indigo-400 transition-colors truncate pr-4">
                                    {{ $rep->report_name }}
                                </div>
                                <x-badge type="{{ $rep->status === 'completed' ? 'success' : 'warning' }}">
                                    {{ $rep->status }}
                                </x-badge>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                                    {{ str_replace('_', ' ', $rep->report_type) }}
                                </div>
                                @if($rep->status === 'completed' && $rep->file_path)
                                    <a href="{{ route('admin.reports.download', $rep) }}" class="text-[10px] font-black text-indigo-400 hover:text-indigo-300 transition-colors uppercase tracking-[0.2em] flex items-center gap-1.5">
                                        Download <i data-lucide="download" class="h-3 w-3"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-12 text-center">
                            <i data-lucide="file-stack" class="h-8 w-8 text-slate-700 mx-auto mb-3"></i>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">No recent reports</p>
                        </div>
                    @endforelse
                </div>
            </x-table-wrapper>
        </div>
    </div>
</div>
@endsection
