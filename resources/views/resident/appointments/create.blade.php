@extends('layouts.app')
@section('title','Book appointment')
@section('content')
<div class="space-y-8 animate-in fade-in duration-700">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.28em] font-black text-slate-500">Appointment Booking</p>
            <h1 class="mt-2 text-3xl font-black text-white tracking-tight">Book your appointment</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-400">Choose from admin-created dates and times only. Your selected slot is pulled from the available schedule, not entered manually.</p>
        </div>
        <x-button href="{{ route('resident.appointments.index') }}" variant="secondary" size="md" icon="chevrons-left">
            My Appointments
        </x-button>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.5fr_0.9fr]">
        <x-card class="space-y-6">
            <div class="space-y-2">
                <h2 class="text-xl font-black text-white">Pick a slot</h2>
                <p class="text-sm text-slate-400">Select a date to display only the available booking times for that day.</p>
            </div>

            @if(session('message'))
                <div class="rounded-3xl border border-emerald-500/20 bg-emerald-500/5 px-4 py-3 text-sm text-emerald-200">
                    {{ session('message') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-3xl border border-rose-500/20 bg-rose-500/5 px-4 py-3 text-sm text-rose-200">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(isset($documentRequest))
                <div class="rounded-3xl border border-slate-700 bg-slate-950/80 p-4">
                    <p class="text-sm font-semibold text-slate-300">Booking for request</p>
                    <p class="mt-1 text-base font-black text-white">{{ $documentRequest->request_number }}</p>
                </div>
            @endif

            <form id="apptform" method="POST" action="{{ route('resident.appointments.store') }}" class="space-y-6">
                @csrf
                @if(isset($documentRequest))
                    <input type="hidden" name="document_request_id" value="{{ $documentRequest->id }}">
                @endif

                <div class="grid gap-6 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-300">Available date</span>
                        <select
                            name="slot_date"
                            id="slotDate"
                            required
                            class="mt-3 w-full rounded-3xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                        >
                            @forelse($availableDates as $date)
                                <option value="{{ $date }}" @selected(old('slot_date', $defaultDate) === $date)>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</option>
                            @empty
                                <option value="">No available dates</option>
                            @endforelse
                        </select>
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold text-slate-300">Available time</span>
                        <select
                            name="schedule_id"
                            id="slotTime"
                            required
                            @disabled($availableDates->isEmpty())
                            class="mt-3 w-full rounded-3xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 disabled:opacity-50"
                        >
                            @forelse($availableTimes ?? [] as $time)
                                <option value="{{ $time['id'] }}" @selected(old('schedule_id') == $time['id'])>{{ $time['time'] }}</option>
                            @empty
                                <option value="">{{ $availableDates->isEmpty() ? 'No dates available' : 'No times for this date' }}</option>
                            @endforelse
                        </select>
                    </label>
                </div>

                @if(isset($defaultDate) && $defaultDate !== today()->format('Y-m-d'))
                    <div class="rounded-3xl border border-slate-700 bg-slate-950/80 p-4 text-sm text-slate-400">
                        <p class="font-semibold text-slate-200">Showing earliest available date</p>
                        <p class="mt-1">The next open booking date is {{ \Carbon\Carbon::parse($defaultDate)->format('M d, Y') }}. You can change the date if you want another day.</p>
                    </div>
                @else
                    <div class="rounded-3xl border border-slate-700 bg-slate-950/80 p-4 text-sm text-slate-400">
                        <p class="font-semibold text-slate-200">Tip</p>
                        <p class="mt-1">If no slots are shown after picking a date, please select another day. The system only shows times that are still available.</p>
                    </div>
                @endif

                <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-8 py-4 text-base font-black uppercase tracking-widest text-white transition-all duration-300 hover:bg-indigo-500 hover:-translate-y-0.5 active:scale-95 disabled:opacity-50 disabled:pointer-events-none" {{ $availableDates->isEmpty() ? 'disabled' : '' }}>
                        Confirm appointment
                    </button>
                    <p class="text-xs uppercase tracking-[0.28em] text-slate-500">Available times refresh immediately</p>
                </div>
            </form>
        </x-card>

        <x-card class="rounded-3xl border border-slate-800 bg-slate-950 shadow-2xl">
            <div class="space-y-6 p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.28em] font-black text-slate-500">Need help?</p>
                        <h3 class="mt-2 text-xl font-black text-white">How bookings work</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-3xl bg-indigo-500/10 text-indigo-300">
                        <i data-lucide="calendar" class="h-6 w-6"></i>
                    </div>
                </div>

                <div class="space-y-4 text-sm text-slate-400">
                    <p><span class="font-semibold text-slate-200">Step 1:</span> Choose an available date created by admin.</p>
                    <p><span class="font-semibold text-slate-200">Step 2:</span> Select one of the available time slots.</p>
                    <p><span class="font-semibold text-slate-200">Step 3:</span> Submit the appointment and review it in your dashboard.</p>
                </div>

                <div class="rounded-3xl border border-slate-700 bg-slate-900/80 p-4 text-sm text-slate-400">
                    <p class="font-semibold text-slate-200">Need assistance?</p>
                    <p class="mt-2">Visit the barangay office or contact the support team if you can’t find a suitable slot.</p>
                </div>
            </div>
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
(function () {
    const slotDate = document.getElementById('slotDate');
    const slotTime = document.getElementById('slotTime');
    const slotsUrl = @json(route('resident.appointment-slots'));

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

        fetch(`${slotsUrl}?date=${encodeURIComponent(selectedDate)}`, {
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
