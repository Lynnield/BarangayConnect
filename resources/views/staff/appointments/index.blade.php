@extends('layouts.app')

@section('title', 'Appointments')

@section('breadcrumb')
    <span class="text-slate-500">Appointments</span>
@endsection

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <!-- Header Section -->
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Appointment Schedule</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Manage and monitor scheduled visits and document pickups.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <x-button href="{{ route('staff.appointments.calendar') }}" variant="secondary" size="md" icon="calendar">
                    Calendar View
                </x-button>
                <x-button href="{{ route('staff.appointments.create') }}" variant="primary" size="md" icon="plus" class="shadow-indigo-600/20">
                    New Appointment
                </x-button>
            </div>
        </div>
    </x-card>

    <!-- Table Section -->
    <x-table-wrapper title="Scheduled Visits" icon="calendar-check">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-900/50 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    <th class="px-6 py-4">Reference</th>
                    <th class="px-6 py-4">Date & Time</th>
                    <th class="px-6 py-4">Resident</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($appointments as $a)
                    <tr class="hover:bg-slate-800/30 transition-all group">
                        <td class="px-6 py-5">
                            <div class="font-black text-white group-hover:text-indigo-400 transition-colors">{{ $a->appointment_number }}</div>
                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-tight mt-0.5">Reference ID</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-slate-800 border border-slate-700 flex flex-col items-center justify-center text-slate-400 group-hover:bg-indigo-500 group-hover:text-white transition-all">
                                    <span class="text-[10px] font-black uppercase tracking-tighter leading-none">{{ $a->appointment_date->format('M') }}</span>
                                    <span class="text-sm font-black mt-0.5 leading-none">{{ $a->appointment_date->format('d') }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-300">{{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}</div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Scheduled</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-slate-300">{{ $a->resident->full_name }}</div>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $statusType = match($a->status) {
                                    'confirmed' => 'success',
                                    'pending' => 'warning',
                                    'cancelled' => 'danger',
                                    'completed' => 'primary',
                                    default => 'neutral',
                                };
                            @endphp
                            <x-badge :type="$statusType">{{ $a->status }}</x-badge>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <x-button href="{{ route('staff.appointments.show', $a) }}" variant="secondary" size="xs" icon="external-link">
                                Open
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div class="h-20 w-20 rounded-3xl bg-slate-800/50 flex items-center justify-center text-slate-700 shadow-inner">
                                    <i data-lucide="calendar" class="h-10 w-10"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-white">No Appointments Found</h3>
                                    <p class="text-sm text-slate-500 max-w-xs mx-auto mt-1 font-medium italic">There are no scheduled appointments to display.</p>
                                </div>
                                <x-button href="{{ route('staff.appointments.create') }}" variant="outline" size="sm" icon="plus">
                                    Create Appointment
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($appointments->hasPages())
            <x-slot:footer>
                <div class="px-2">
                    {{ $appointments->links() }}
                </div>
            </x-slot:footer>
        @endif
    </x-table-wrapper>
</div>
@endsection
