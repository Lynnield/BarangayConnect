@extends('layouts.app')

@section('title', 'Request #' . $documentRequest->request_number)

@section('breadcrumb')
    <a href="{{ route('admin.requests.index') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">Requests</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <span class="text-slate-300">Details</span>
@endsection

@section('content')
<div class="w-full max-w-5xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Header -->
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.requests.index') }}" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-400 hover:text-white hover:border-slate-600 transition-all">
                <i data-lucide="arrow-left" class="h-5 w-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Request Details</h1>
                <p class="text-sm text-slate-500 font-medium mt-1">Full overview of document application #{{ $documentRequest->request_number }}</p>
            </div>
        </div>
        <div>
            @php
                $statusType = match($documentRequest->status) {
                    'pending' => 'warning',
                    'under_review' => 'info',
                    'approved' => 'success',
                    'released' => 'primary',
                    'rejected' => 'danger',
                    default => 'neutral',
                };
            @endphp
            <x-badge :type="$statusType" class="px-4 py-2 text-[10px]">{{ str_replace('_', ' ', $documentRequest->status) }}</x-badge>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-8">
            <x-card title="Application Information" icon="file-text" class="bg-slate-900/50 border-slate-800">
                <div class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Document Type</span>
                            <p class="text-sm font-black text-white">{{ $documentRequest->documentType->name }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Date Applied</span>
                            <p class="text-sm font-black text-white">{{ $documentRequest->created_at->format('F d, Y • h:i A') }}</p>
                        </div>
                    </div>

                    <div class="h-px bg-slate-800"></div>

                    <div class="space-y-2">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Purpose of Request</span>
                        <div class="bg-slate-950/50 border border-slate-800 rounded-2xl p-6 text-sm text-slate-300 leading-relaxed italic">
                            "{{ $documentRequest->purpose }}"
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Admin Note -->
            <x-card class="bg-indigo-600/5 border-indigo-500/20">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 shrink-0">
                        <i data-lucide="info" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-white uppercase tracking-widest">Administrative View</h4>
                        <p class="text-[11px] text-slate-400 mt-1 leading-relaxed italic">
                            This view provides a read-only overview of the request. Status management and document processing are handled through the dedicated Staff and Resident dashboards to ensure proper workflow separation.
                        </p>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Resident Sidebar -->
        <div class="lg:col-span-1 space-y-8">
            <x-card title="Resident Profile" icon="user" class="bg-slate-900/50 border-slate-800">
                <div class="space-y-6">
                    <div class="flex flex-col items-center text-center pb-6 border-b border-slate-800">
                        <div class="h-20 w-20 rounded-3xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500 mb-4 shadow-xl">
                            <i data-lucide="user" class="h-10 w-10"></i>
                        </div>
                        <h3 class="text-lg font-black text-white">{{ $documentRequest->resident->full_name }}</h3>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-1">Official ID: {{ $documentRequest->resident->resident_number }}</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Gender</span>
                            <span class="text-xs font-bold text-slate-300 capitalize">{{ $documentRequest->resident->gender }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Civil Status</span>
                            <span class="text-xs font-bold text-slate-300 capitalize">{{ $documentRequest->resident->civil_status }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Age</span>
                            <span class="text-xs font-bold text-slate-300">{{ $documentRequest->resident->birthdate?->age }} Years</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.residents.show', $documentRequest->resident) }}" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-slate-800 border border-slate-700 text-[10px] font-black text-white uppercase tracking-widest hover:bg-slate-700 transition-all">
                        View Full Resident File
                        <i data-lucide="external-link" class="h-3 w-3"></i>
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
