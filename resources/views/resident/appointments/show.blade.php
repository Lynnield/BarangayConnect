@extends('layouts.app')

@section('title', 'Appointment #' . $appointment->appointment_number)

@section('breadcrumb')
    <a href="{{ route('resident.appointments.index') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">My Appointments</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <span class="text-slate-300">Details</span>
@endsection

@section('content')
@php
    $statusType = match($appointment->status) {
        'scheduled' => 'info',
        'confirmed' => 'success',
        'rescheduled' => 'warning',
        'cancelled', 'rejected', 'no_show' => 'danger',
        'completed' => 'primary',
        default => 'neutral',
    };
@endphp

<div class="w-full max-w-5xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('resident.appointments.index') }}" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-400 hover:text-white hover:border-slate-600 transition-all">
                <i data-lucide="arrow-left" class="h-5 w-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Appointment Details</h1>
                <p class="text-sm text-slate-500 font-medium mt-1 font-mono text-indigo-400/90">#{{ $appointment->appointment_number }}</p>
            </div>
        </div>
        <x-badge :type="$statusType" class="px-4 py-2 text-[10px]">{{ str_replace('_', ' ', $appointment->status) }}</x-badge>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/5 px-5 py-4 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-500/20 bg-rose-500/5 px-5 py-4 text-sm text-rose-200">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8 space-y-8">
            <x-card title="Schedule Information" class="bg-slate-900/50 border-slate-800">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-2xl bg-indigo-500/10 flex flex-col items-center justify-center text-indigo-400 border border-indigo-500/20">
                            <span class="text-[10px] font-black uppercase tracking-tighter leading-none">{{ $appointment->appointment_date->format('M') }}</span>
                            <span class="text-lg font-black mt-1 leading-none">{{ $appointment->appointment_date->format('d') }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Scheduled Date</span>
                            <p class="text-sm font-black text-white">{{ $appointment->appointment_date->format('l, F d, Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-2xl bg-slate-800 flex items-center justify-center text-slate-400 border border-slate-700">
                            <i data-lucide="clock" class="h-6 w-6"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Arrival Time</span>
                            <p class="text-sm font-black text-white">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                        </div>
                    </div>
                </div>

                @if($appointment->notes)
                    <div class="h-px bg-slate-800 my-8"></div>
                    <div class="space-y-2">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Notes</span>
                        <div class="bg-slate-950/50 border border-slate-800 rounded-2xl p-6 text-sm text-slate-300 leading-relaxed">
                            {{ $appointment->notes }}
                        </div>
                    </div>
                @endif
            </x-card>

            @if($appointment->documentRequest)
                <x-card title="Linked Document Request" class="bg-slate-900/50 border-slate-800">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-black text-white">{{ $appointment->documentRequest->documentType->name }}</p>
                            <p class="text-[10px] text-slate-500 font-bold uppercase mt-1 font-mono">{{ $appointment->documentRequest->request_number }}</p>
                        </div>
                        <x-button href="{{ route('resident.requests.show', $appointment->documentRequest) }}" variant="secondary" size="sm" icon="external-link">
                            View Request
                        </x-button>
                    </div>
                </x-card>
            @endif
        </div>

        <div class="lg:col-span-4 space-y-8">
            @if($canManage)
                <x-card title="Manage Visit" class="bg-slate-900 border-slate-800 shadow-2xl">
                    <div class="space-y-6">
                        <form method="POST" action="{{ route('resident.appointments.reschedule', $appointment) }}" class="space-y-4">
                            @csrf
                            <p class="text-xs text-slate-500 leading-relaxed">Pick a new date and time from available barangay slots.</p>

                            <label class="block">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Available date</span>
                                <select
                                    name="slot_date"
                                    id="rescheduleDate"
                                    required
                                    @disabled($availableDates->isEmpty())
                                    class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 disabled:opacity-50"
                                >
                                    @forelse($availableDates as $date)
                                        <option value="{{ $date }}" @selected(old('slot_date', $defaultDate) === $date)>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</option>
                                    @empty
                                        <option value="">No available dates</option>
                                    @endforelse
                                </select>
                            </label>

                            <label class="block">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Available time</span>
                                <select
                                    name="schedule_id"
                                    id="rescheduleTime"
                                    required
                                    @disabled($availableDates->isEmpty())
                                    class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 disabled:opacity-50"
                                >
                                    @forelse($availableTimes as $time)
                                        <option value="{{ $time['id'] }}" @selected(old('schedule_id') == $time['id'])>{{ $time['time'] }}</option>
                                    @empty
                                        <option value="">{{ $availableDates->isEmpty() ? 'No dates available' : 'No times for this date' }}</option>
                                    @endforelse
                                </select>
                            </label>

                            <x-button type="submit" variant="primary" size="md" icon="calendar-clock" class="w-full shadow-indigo-600/20 @if($availableDates->isEmpty()) opacity-50 pointer-events-none @endif">
                                Reschedule
                            </x-button>
                        </form>

                        <div class="h-px bg-slate-800"></div>

                        <form method="POST" action="{{ route('resident.appointments.cancel', $appointment) }}" onsubmit="return confirm('Cancel this appointment?')">
                            @csrf
                            <x-button type="submit" variant="ghost" size="md" icon="x-circle" class="w-full text-rose-500 hover:bg-rose-500/5 border border-rose-500/10">
                                Cancel Appointment
                            </x-button>
                        </form>
                    </div>
                </x-card>
            @else
                <x-card title="Visit Status" class="bg-slate-900/50 border-slate-800">
                    <p class="text-sm text-slate-400 leading-relaxed">
                        This appointment is {{ str_replace('_', ' ', $appointment->status) }} and can no longer be changed online. Contact the barangay office if you need assistance.
                    </p>
                    <x-button href="{{ route('resident.appointments.index') }}" variant="secondary" size="sm" icon="arrow-left" class="mt-6 w-full">
                        Back to Appointments
                    </x-button>
                </x-card>
            @endif

            <x-card class="bg-slate-950/50 border-slate-800">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 shrink-0 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                        <i data-lucide="info" class="h-5 w-5"></i>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Arrive a few minutes early with a valid ID. If you linked a document request, bring any requirements listed on that application.
                    </p>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection

@if($canManage && $availableDates->isNotEmpty())
@push('scripts')
<script nonce="{{ $cspNonce }}">
(function () {
    const slotDate = document.getElementById('rescheduleDate');
    const slotTime = document.getElementById('rescheduleTime');
    const slotsUrl = @json(route('resident.appointment-slots'));
    const exceptId = @json($appointment->id);

    if (!slotDate || !slotTime) {
        return;
    }

    const renderTimeOptions = (rows) => {
        slotTime.innerHTML = '';
        slotTime.disabled = false;

        if (!rows.length) {
            const empty = document.createElement('option');
            empty.value = '';
            empty.textContent = 'No times available for this date';
            slotTime.appendChild(empty);
            slotTime.disabled = true;
            return;
        }

        rows.forEach((row) => {
            const option = document.createElement('option');
            option.value = row.id;
            option.textContent = row.time;
            slotTime.appendChild(option);
        });
    };

    const loadAvailableTimes = (selectedDate) => {
        if (!selectedDate) {
            slotTime.innerHTML = '<option value="">Choose a date first</option>';
            slotTime.disabled = true;
            return;
        }

        slotTime.disabled = true;
        slotTime.innerHTML = '<option value="">Loading…</option>';

        const params = new URLSearchParams({ date: selectedDate, except: String(exceptId) });

        fetch(`${slotsUrl}?${params}`, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Failed to load slots');
                }
                return response.json();
            })
            .then(renderTimeOptions)
            .catch(() => {
                slotTime.innerHTML = '<option value="">Unable to load times</option>';
                slotTime.disabled = true;
            });
    };

    slotDate.addEventListener('change', function () {
        loadAvailableTimes(this.value);
    });

    if (slotDate.value) {
        loadAvailableTimes(slotDate.value);
    }
})();
</script>
@endpush
@endif
