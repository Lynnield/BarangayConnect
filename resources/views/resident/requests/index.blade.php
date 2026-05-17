@extends('layouts.app')
@section('title', 'My Requests')
@section('breadcrumb')
<span class="text-slate-900">Document Requests</span>
@endsection
@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-indigo-900 via-slate-900 to-slate-900" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Document Requests</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Track and manage your official document applications.</p>
            </div>
            <x-button href="{{ route('resident.requests.create') }}" variant="primary" size="md" icon="plus" class="shadow-indigo-600/20">
                New Request
            </x-button>
        </div>
    </x-card>

    <x-table-wrapper title="Submission History" icon="history">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-900/50 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    <th class="px-6 py-4">Reference</th>
                    <th class="px-6 py-4">Document Details</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Submitted</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($requests as $r)
                    <tr class="hover:bg-slate-800/30 transition-all group">
                        <td class="px-6 py-5">
                            <span class="font-mono text-sm font-black text-indigo-400 group-hover:text-indigo-300">{{ $r->request_number }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="font-bold text-white group-hover:text-indigo-400 transition-colors">{{ $r->documentType->name }}</div>
                            <div class="text-[10px] text-slate-500 font-medium mt-1 truncate max-w-[200px]">{{ $r->purpose ?? 'Official Use' }}</div>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $type = match($r->status) {
                                    'pending' => 'warning',
                                    'approved', 'ready_pickup', 'completed' => 'success',
                                    'rejected' => 'danger',
                                    'under_review' => 'info',
                                    default => 'neutral'
                                };
                            @endphp
                            <x-badge :type="$type">{{ str_replace('_', ' ', $r->status) }}</x-badge>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-slate-400">{{ $r->created_at->format('M d, Y') }}</div>
                            <div class="text-[10px] text-slate-600 font-medium uppercase tracking-tighter">{{ $r->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <x-button href="{{ route('resident.requests.show', $r) }}" variant="secondary" size="xs" icon="eye">
                                Details
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div class="h-20 w-20 rounded-3xl bg-slate-800/50 flex items-center justify-center text-slate-700 shadow-inner">
                                    <i data-lucide="file-x" class="h-10 w-10"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-white">No Records Found</h3>
                                    <p class="text-sm text-slate-500 max-w-xs mx-auto mt-1 font-medium italic">You haven't submitted any document requests yet.</p>
                                </div>
                                <x-button href="{{ route('resident.requests.create') }}" variant="outline" size="sm" icon="plus-circle">
                                    Start First Request
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
