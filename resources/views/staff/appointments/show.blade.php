@extends('layouts.app')

@section('title', 'Appointment #' . $appointment->appointment_number)

@section('breadcrumb')
    <a href="{{ route('staff.appointments.index') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">Appointments</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <span class="text-slate-300">Details</span>
@endsection

@section('content')
<div class="w-full max-w-5xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('staff.appointments.index') }}" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-400 hover:text-white hover:border-slate-600 transition-all">
                <i data-lucide="arrow-left" class="h-5 w-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Appointment Details</h1>
                <p class="text-sm text-slate-500 font-medium mt-1">Visit scheduling for {{ $appointment->resident->full_name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @php
                $statusType = match($appointment->status) {
                    'confirmed' => 'success',
                    'pending' => 'warning',
                    'cancelled' => 'danger',
                    'completed' => 'primary',
                    default => 'neutral',
                };
            @endphp
            <x-badge :type="$statusType" class="px-4 py-2 text-[10px]">{{ $appointment->status }}</x-badge>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-8 space-y-8">
            <x-card title="Schedule Information" icon="calendar" class="bg-slate-900/50 border-slate-800">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-2xl bg-indigo-500/10 flex flex-col items-center justify-center text-indigo-400 border border-indigo-500/20">
                            <span class="text-[10px] font-black uppercase tracking-tighter leading-none">{{ $appointment->appointment_date->format('M') }}</span>
                            <span class="text-lg font-black mt-1 leading-none">{{ $appointment->appointment_date->format('d') }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Scheduled Date</span>
                            <p class="text-sm font-black text-white">{{ $appointment->appointment_date->format('F d, Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-2xl bg-slate-800 flex items-center justify-center text-slate-400 border border-slate-700">
                            <i data-lucide="clock" class="h-6 w-6"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Arrival Time</span>
                            <p class="text-sm font-black text-white">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
                        </div>
                    </div>
                </div>

                @if($appointment->reason)
                    <div class="h-px bg-slate-800 my-8"></div>
                    <div class="space-y-2">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Appointment Purpose</span>
                        <div class="bg-slate-950/50 border border-slate-800 rounded-2xl p-6 text-sm text-slate-300 leading-relaxed italic">
                            "{{ $appointment->reason }}"
                        </div>
                    </div>
                @endif
            </x-card>

            @if($appointment->documentRequest)
                <x-card title="Linked Document Request" icon="file-text" class="bg-slate-900/50 border-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-black text-white">{{ $appointment->documentRequest->documentType->name }}</p>
                            <p class="text-[10px] text-slate-500 font-bold uppercase mt-1">{{ $appointment->documentRequest->request_number }}</p>
                        </div>
                        <x-button href="{{ route('staff.requests.show', $appointment->documentRequest) }}" variant="secondary" size="sm" icon="external-link">
                            View Request
                        </x-button>
                    </div>
                </x-card>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="lg:col-span-4 space-y-8">
            <x-card title="Workflow Actions" icon="refresh-cw" class="bg-slate-900 border-slate-800 shadow-2xl">
                <div class="space-y-4">
                    @if($appointment->status === 'pending')
                        <form method="POST" action="{{ route('staff.appointments.confirm', $appointment) }}">
                            @csrf
                            <x-button type="submit" variant="primary" size="lg" icon="check-circle" class="w-full shadow-indigo-600/20">
                                Confirm Appointment
                            </x-button>
                        </form>
                    @endif

                    <x-button href="{{ route('staff.appointments.edit', $appointment) }}" variant="secondary" size="lg" icon="edit-3" class="w-full">
                        Modify Schedule
                    </x-button>

                    @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed')
                        <form method="POST" action="{{ route('staff.appointments.cancel', $appointment) }}" onsubmit="return confirm('Cancel this appointment?')">
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" variant="ghost" size="lg" icon="x-circle" class="w-full text-rose-500 hover:bg-rose-500/5">
                                Cancel Visit
                            </x-button>
                        </form>
                    @endif
                </div>
            </x-card>

            <x-card title="Resident Info" icon="user" class="bg-slate-900/50 border-slate-800">
                <div class="flex flex-col items-center text-center">
                    <div class="h-16 w-16 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500 mb-4">
                        <i data-lucide="user" class="h-8 w-8"></i>
                    </div>
                    <h3 class="text-sm font-black text-white">{{ $appointment->resident->full_name }}</h3>
                    <p class="text-[10px] text-slate-500 font-bold uppercase mt-1">{{ $appointment->resident->contact_number ?: 'No contact number' }}</p>
                    <a href="{{ route('staff.residents.show', $appointment->resident) }}" class="text-[10px] font-black text-indigo-400 hover:text-indigo-300 uppercase tracking-widest mt-4">
                        View Resident File
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
