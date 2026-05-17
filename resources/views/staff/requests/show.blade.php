@extends('layouts.app')

@section('title', 'Process Request #' . $documentRequest->request_number)

@section('breadcrumb')
    <a href="{{ route('staff.requests.index') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">Requests</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <span class="text-slate-300">Processing</span>
@endsection

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('staff.requests.index') }}" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-400 hover:text-white hover:border-slate-600 transition-all">
                <i data-lucide="arrow-left" class="h-5 w-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Request Processing</h1>
                <p class="text-sm text-slate-500 font-medium mt-1">{{ $documentRequest->documentType->name }} for {{ $documentRequest->resident->full_name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <x-button href="{{ route('staff.requests.pdf', $documentRequest) }}" target="_blank" variant="secondary" size="md" icon="file-down">
                Generate PDF
            </x-button>
            @php
                $statusType = match($documentRequest->status) {
                    'pending' => 'warning',
                    'under_review' => 'info',
                    'approved', 'ready_for_pickup' => 'success',
                    'released' => 'primary',
                    'rejected' => 'danger',
                    default => 'neutral',
                };
            @endphp
            <x-badge :type="$statusType" class="px-4 py-2 text-[10px]">{{ str_replace('_', ' ', $documentRequest->status) }}</x-badge>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Content Area -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Request Details -->
            <x-card title="Application Details" icon="file-text" class="bg-slate-900/50 border-slate-800">
                <div class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Application Purpose</span>
                            <p class="text-sm font-bold text-white leading-relaxed">{{ $documentRequest->purpose }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Resident Identity</span>
                            <p class="text-sm font-bold text-white">{{ $documentRequest->resident->full_name }}</p>
                            <p class="text-[10px] text-slate-500 font-bold uppercase">{{ $documentRequest->resident->resident_number }}</p>
                        </div>
                    </div>

                    @if($documentRequest->remarks || $documentRequest->rejection_reason || $documentRequest->revision_notes)
                        <div class="h-px bg-slate-800"></div>
                        <div class="grid grid-cols-1 gap-6">
                            @if($documentRequest->remarks)
                                <div class="space-y-1">
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Internal Remarks</span>
                                    <p class="text-xs text-slate-400 font-medium italic">{{ $documentRequest->remarks }}</p>
                                </div>
                            @endif
                            @if($documentRequest->rejection_reason)
                                <div class="space-y-1">
                                    <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Rejection Reason</span>
                                    <p class="text-xs text-rose-400 font-medium italic bg-rose-500/5 p-3 rounded-xl border border-rose-500/10">{{ $documentRequest->rejection_reason }}</p>
                                </div>
                            @endif
                            @if($documentRequest->revision_notes)
                                <div class="space-y-1">
                                    <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Revision Notes</span>
                                    <p class="text-xs text-amber-400 font-medium italic bg-amber-500/5 p-3 rounded-xl border border-amber-500/10">{{ $documentRequest->revision_notes }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Attachments -->
            <x-card title="Verification Documents" icon="paperclip" class="bg-slate-900/50 border-slate-800" :padding="false">
                <div class="divide-y divide-slate-800">
                    @forelse($documentRequest->attachments as $a)
                        <div class="flex items-center justify-between p-4 hover:bg-slate-800/30 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-500 group-hover:bg-indigo-500 group-hover:text-white transition-all">
                                    <i data-lucide="file" class="h-5 w-5"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-white truncate max-w-[200px]">{{ $a->file_name }}</p>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Resident Attachment</p>
                                </div>
                            </div>
                            <a href="{{ asset('storage/'.$a->file_path) }}" target="_blank" class="h-9 px-4 flex items-center gap-2 rounded-xl bg-slate-800 border border-slate-700 text-[10px] font-black text-white uppercase tracking-widest hover:bg-indigo-600 hover:border-indigo-500 transition-all shadow-sm">
                                <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                                View File
                            </a>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <i data-lucide="file-x" class="h-10 w-10 text-slate-700 mx-auto mb-3"></i>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">No files attached to this request</p>
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Sidebar Actions -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Update Form -->
            <x-card title="Workflow Control" icon="refresh-cw" class="bg-slate-900 border-slate-800 shadow-2xl">
                <form method="POST" action="{{ route('staff.requests.update-status', $documentRequest) }}" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Update Status</label>
                        <div class="relative group">
                            <select name="status" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3.5 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none shadow-inner" required>
                                @foreach(['under_review','for_revision','approved','rejected','ready_for_pickup','released'] as $s)
                                    <option value="{{ $s }}" @selected($documentRequest->status==$s)>{{ ucfirst(str_replace('_',' ', $s)) }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                                <i data-lucide="chevron-down" class="h-4 w-4"></i>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Internal Remarks</label>
                        <textarea name="remarks" rows="2" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none resize-none" placeholder="Notes for staff..."></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-rose-500 uppercase tracking-widest ml-1">Rejection Reason</label>
                        <input name="rejection_reason" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white placeholder:text-slate-600 focus:border-rose-500 focus:bg-slate-800 focus:ring-4 focus:ring-rose-500/10 transition-all outline-none" placeholder="Only if rejected">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-amber-500 uppercase tracking-widest ml-1">Revision Instructions</label>
                        <textarea name="revision_notes" rows="2" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white placeholder:text-slate-600 focus:border-amber-500 focus:bg-slate-800 focus:ring-4 focus:ring-amber-500/10 transition-all outline-none resize-none" placeholder="Instructions for resident..."></textarea>
                    </div>

                    <x-button type="submit" variant="primary" size="lg" icon="check-circle" class="w-full shadow-indigo-600/20">
                        Update Workflow Status
                    </x-button>
                </form>
            </x-card>

            <!-- History Timeline -->
            <x-card title="Activity Log" icon="history" class="bg-slate-900/50 border-slate-800" :padding="false">
                <div class="p-6">
                    <div class="space-y-6">
                        @foreach($documentRequest->statusLogs as $log)
                            <div class="relative pl-6 pb-6 last:pb-0">
                                @if(!$loop->last)
                                    <div class="absolute left-[7px] top-4 bottom-0 w-px bg-slate-800"></div>
                                @endif
                                <div class="absolute left-0 top-1.5 h-3.5 w-3.5 rounded-full bg-slate-800 border-2 border-slate-700"></div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-tighter">{{ str_replace('_', ' ', $log->to_status) }}</span>
                                        <span class="text-[9px] text-slate-600 font-bold">• {{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 font-medium">Transitioned from <span class="text-slate-400">{{ str_replace('_', ' ', $log->from_status) }}</span></p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
