@extends('layouts.app')
@section('title', 'My Appointments')
@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-indigo-900 via-slate-900 to-slate-900" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Appointments</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Manage your scheduled visits and processing times.</p>
            </div>
            <x-button href="{{ route('resident.appointments.create') }}" variant="primary" size="md" icon="calendar-plus" class="shadow-indigo-600/20">
                Book Appointment
            </x-button>
        </div>
    </x-card>

    <x-table-wrapper title="Appointment Schedule" icon="calendar-days">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-900/50 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    <th class="px-6 py-4">Reference</th>
                    <th class="px-6 py-4">Date & Time</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($appointments as $a)
                    <tr class="hover:bg-slate-800/30 transition-all group">
                        <td class="px-6 py-5">
                            <span class="font-mono text-sm font-black text-indigo-400 group-hover:text-indigo-300">{{ $a->appointment_number }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col items-center justify-center rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-indigo-400 min-w-[60px] group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                    <span class="text-[10px] font-black uppercase tracking-tighter">{{ $a->appointment_date->format('M') }}</span>
                                    <span class="text-xl font-black leading-none">{{ $a->appointment_date->format('d') }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-white">{{ $a->appointment_date->format('l, Y') }}</div>
                                    <div class="text-[10px] text-slate-500 font-medium uppercase tracking-widest mt-0.5 flex items-center gap-1.5">
                                        <i data-lucide="clock" class="h-3 w-3"></i>
                                        {{ \Carbon\Carbon::parse($a->appointment_time)->format('g:i A') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $type = match($a->status) {
                                    'pending' => 'warning',
                                    'confirmed' => 'success',
                                    'cancelled', 'rejected' => 'danger',
                                    'completed' => 'primary',
                                    default => 'neutral'
                                };
                            @endphp
                            <x-badge :type="$type">{{ $a->status }}</x-badge>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <x-button href="{{ route('resident.appointments.show', $a) }}" variant="secondary" size="xs" icon="eye">
                                View Details
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div class="h-20 w-20 rounded-3xl bg-slate-800/50 flex items-center justify-center text-slate-700 shadow-inner">
                                    <i data-lucide="calendar-off" class="h-10 w-10"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-white">No Appointments</h3>
                                    <p class="text-sm text-slate-500 max-w-xs mx-auto mt-1 font-medium italic">You haven't scheduled any appointments yet.</p>
                                </div>
                                <x-button href="{{ route('resident.appointments.create') }}" variant="outline" size="sm" icon="plus-circle">
                                    Schedule Now
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
