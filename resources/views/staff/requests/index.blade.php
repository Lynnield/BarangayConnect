@extends('layouts.app')

@section('title', 'Document Requests')

@section('breadcrumb')
    <span class="text-slate-500">Document Requests</span>
@endsection

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <!-- Header Section -->
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Document Requests</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Review, process, and manage document applications from residents.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex -space-x-2">
                    @foreach($statusCounts as $status => $count)
                        @if($count > 0)
                            <div class="h-10 px-4 flex items-center rounded-xl bg-slate-800 border border-slate-700 text-[10px] font-black uppercase tracking-widest text-white shadow-lg">
                                {{ str_replace('_', ' ', $status) }}: {{ $count }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </x-card>

    <!-- Search & Filters -->
    <x-card class="bg-slate-900/50 border-slate-800" :padding="false">
        <form method="GET" action="{{ route('staff.requests.index') }}" class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Quick Search</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                        <i data-lucide="search" class="h-4 w-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 pl-11 pr-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner" 
                        placeholder="Request # or Name...">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Filter Status</label>
                <div class="relative group">
                    <select name="status" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none shadow-inner">
                        <option value="">All Statuses</option>
                        @foreach($statusCounts->keys() as $st)
                            <option value="{{ $st }}" @selected(request('status') == $st)>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                        <i data-lucide="chevron-down" class="h-4 w-4"></i>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Document Type</label>
                <div class="relative group">
                    <select name="document_type" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none shadow-inner">
                        <option value="">All Types</option>
                        @foreach($documentTypes as $dt)
                            <option value="{{ $dt->id }}" @selected(request('document_type') == $dt->id)>{{ $dt->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                        <i data-lucide="chevron-down" class="h-4 w-4"></i>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <x-button type="submit" variant="secondary" size="md" class="flex-1" icon="filter">Filter</x-button>
                @if(request()->anyFilled(['search', 'status', 'document_type']))
                    <x-button type="button" variant="ghost" size="md" onclick="window.location.href='{{ route('staff.requests.index') }}'" icon="rotate-ccw"></x-button>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Table Section -->
    <x-table-wrapper title="Active Queue" icon="clipboard-list">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-900/50 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    <th class="px-6 py-4">Request Details</th>
                    <th class="px-6 py-4">Resident</th>
                    <th class="px-6 py-4">Document Type</th>
                    <th class="px-6 py-4">Current Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($requests as $r)
                    <tr class="hover:bg-slate-800/30 transition-all group">
                        <td class="px-6 py-5">
                            <div class="font-black text-white group-hover:text-indigo-400 transition-colors">{{ $r->request_number }}</div>
                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-tight mt-0.5">{{ $r->created_at->format('M d, Y • h:i A') }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] font-black text-slate-400 border border-slate-700">
                                    {{ substr($r->resident->full_name, 0, 1) }}
                                </div>
                                <div class="text-sm font-bold text-slate-300">{{ $r->resident->full_name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-slate-300">{{ $r->documentType->name }}</div>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $statusType = match($r->status) {
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'ready' => 'success',
                                    'completed' => 'primary',
                                    'rejected' => 'danger',
                                    default => 'neutral',
                                };
                            @endphp
                            <x-badge :type="$statusType">{{ str_replace('_', ' ', $r->status) }}</x-badge>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <x-button href="{{ route('staff.requests.show', $r) }}" variant="primary" size="xs" icon="arrow-right">
                                Process
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div class="h-20 w-20 rounded-3xl bg-slate-800/50 flex items-center justify-center text-slate-700 shadow-inner">
                                    <i data-lucide="clipboard-list" class="h-10 w-10"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-white">No Requests Found</h3>
                                    <p class="text-sm text-slate-500 max-w-xs mx-auto mt-1 font-medium italic">There are no document requests matching your current filters.</p>
                                </div>
                                <x-button href="{{ route('staff.requests.index') }}" variant="outline" size="sm" icon="rotate-ccw">
                                    Reset Queue
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($requests->hasPages())
            <x-slot:footer>
                <div class="px-2">
                    {{ $requests->links() }}
                </div>
            </x-slot:footer>
        @endif
    </x-table-wrapper>
</div>
@endsection