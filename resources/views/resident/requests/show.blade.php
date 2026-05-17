@extends('layouts.app')

@section('title', 'Request #' . $documentRequest->request_number)

@section('breadcrumb')
    <a href="{{ route('resident.requests.index') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">My Requests</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <span class="text-slate-300">Status</span>
@endsection

@section('content')
<div class="w-full max-w-5xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('resident.requests.index') }}" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-400 hover:text-white hover:border-slate-600 transition-all">
                <i data-lucide="arrow-left" class="h-5 w-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Track Application</h1>
                <p class="text-sm text-slate-500 font-medium mt-1">{{ $documentRequest->documentType->name }} · #{{ $documentRequest->request_number }}</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if($documentRequest->canBeCancelled())
                <form method="POST" action="{{ route('resident.requests.cancel',$documentRequest) }}" onsubmit="return confirm('Permanently cancel this request?')">
                    @csrf
                    <x-button type="submit" variant="ghost" size="md" icon="x-circle" class="text-rose-500 hover:text-rose-400 hover:bg-rose-500/5 border-rose-500/10">
                        Cancel Request
                    </x-button>
                </form>
            @endif

            @if($documentRequest->status==='released' && $documentRequest->pdf_path)
                <x-button href="{{ route('resident.requests.download',$documentRequest) }}" variant="primary" size="md" icon="download" class="shadow-indigo-600/20">
                    Download Official PDF
                </x-button>
            @endif

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
        <!-- Main Column -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Progress Tracker (Visual) -->
            <x-card class="bg-slate-900/50 border-slate-800">
                <div class="relative pt-2 pb-6 px-4">
                    <div class="flex items-center justify-between relative z-10">
                        @foreach(['pending', 'under_review', 'approved', 'released'] as $step)
                            @php
                                $isCompleted = array_search($documentRequest->status, ['pending', 'under_review', 'approved', 'released']) >= array_search($step, ['pending', 'under_review', 'approved', 'released']);
                                $isActive = $documentRequest->status === $step;
                            @endphp
                            <div class="flex flex-col items-center gap-3">
                                <div @class([
                                    'h-10 w-10 rounded-2xl flex items-center justify-center transition-all duration-500',
                                    'bg-indigo-600 text-white shadow-xl shadow-indigo-900/40 ring-4 ring-indigo-950' => $isCompleted,
                                    'bg-slate-800 text-slate-600 border border-slate-700' => !$isCompleted
                                ])>
                                    <i data-lucide="{{ match($step){'pending'=>'clock','under_review'=>'search','approved'=>'check-circle','released'=>'award'} }}" class="h-5 w-5"></i>
                                </div>
                                <span @class([
                                    'text-[9px] font-black uppercase tracking-widest',
                                    'text-indigo-400' => $isCompleted,
                                    'text-slate-600' => !$isCompleted
                                ])>{{ str_replace('_', ' ', $step) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <!-- Line Background -->
                    <div class="absolute top-7 left-10 right-10 h-0.5 bg-slate-800 -z-0"></div>
                </div>
            </x-card>

            <!-- Request Data -->
            <x-card title="Application Details" icon="file-text" class="bg-slate-900/50 border-slate-800">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Purpose</span>
                        <p class="text-sm font-bold text-white leading-relaxed">{{ $documentRequest->purpose }}</p>
                    </div>

                    @if($documentRequest->remarks)
                        <div class="bg-indigo-500/5 border border-indigo-500/10 rounded-2xl p-6 flex items-start gap-4">
                            <i data-lucide="message-square" class="h-5 w-5 text-indigo-400 shrink-0 mt-0.5"></i>
                            <div>
                                <h4 class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Office Message</h4>
                                <p class="text-xs text-slate-300 leading-relaxed italic">"{{ $documentRequest->remarks }}"</p>
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Attachments -->
            <x-card title="Your Uploaded Files" icon="paperclip" class="bg-slate-900/50 border-slate-800" :padding="false">
                <div class="divide-y divide-slate-800">
                    @forelse($documentRequest->attachments as $a)
                        <div class="flex items-center justify-between p-4 hover:bg-slate-800/30 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-500 group-hover:bg-indigo-500 group-hover:text-white transition-all">
                                    <i data-lucide="file" class="h-5 w-5"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-300 truncate max-w-[250px]">{{ $a->file_name }}</p>
                            </div>
                            <a href="{{ asset('storage/'.$a->file_path) }}" target="_blank" class="text-[10px] font-black text-indigo-400 hover:text-indigo-300 uppercase tracking-widest px-4 py-2">
                                Open File
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-[10px] font-black text-slate-600 uppercase tracking-widest italic">
                            No files were required for this application.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Sidebar Column -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Appointment Management -->
            <x-card title="Pickup Schedule" icon="calendar" class="bg-slate-900 border-slate-800 shadow-2xl">
                <div class="space-y-6">
                    @forelse($documentRequest->appointments as $ap)
                        <div class="flex flex-col items-center justify-center rounded-3xl bg-slate-800 border border-slate-700 p-6 text-center group">
                            <div class="h-16 w-16 rounded-2xl bg-indigo-600 flex flex-col items-center justify-center text-white shadow-xl shadow-indigo-900/40 mb-4 transition-transform group-hover:scale-110">
                                <span class="text-[10px] font-black uppercase tracking-tighter">{{ $ap->appointment_date->format('M') }}</span>
                                <span class="text-2xl font-black leading-none mt-1">{{ $ap->appointment_date->format('d') }}</span>
                            </div>
                            <h4 class="text-sm font-black text-white uppercase tracking-tight">{{ \Carbon\Carbon::parse($ap->appointment_time)->format('g:i A') }}</h4>
                            <div class="mt-4">
                                <x-badge type="{{ $ap->status === 'confirmed' ? 'success' : 'warning' }}" size="sm">{{ $ap->status }}</x-badge>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <div class="h-14 w-14 rounded-2xl bg-slate-800 flex items-center justify-center text-slate-600 mx-auto mb-4">
                                <i data-lucide="calendar-off" class="h-6 w-6"></i>
                            </div>
                            <p class="text-xs text-slate-500 font-medium mb-6 leading-relaxed">No pickup appointment has been scheduled for this request yet.</p>
                            
                            @if(in_array($documentRequest->status, ['approved', 'ready_for_pickup']))
                                <x-button href="{{ route('resident.appointments.create', $documentRequest) }}" variant="primary" size="sm" icon="plus" class="w-full">
                                    Schedule Pickup
                                </x-button>
                            @else
                                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-[10px] font-bold text-slate-600 uppercase tracking-widest leading-relaxed">
                                    Appointment booking unlocks once approved.
                                </div>
                            @endif
                        </div>
                    @endforelse
                </div>
            </x-card>

            <!-- Support Info -->
            <x-card class="bg-indigo-600/5 border-indigo-500/20">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 shrink-0">
                        <i data-lucide="help-circle" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-white uppercase tracking-widest">Need Help?</h4>
                        <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                            If you have questions about your application status, please visit the Barangay Hall or contact our hotline.
                        </p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
