@extends('layouts.app')

@section('title', 'New Appointment')

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-8">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">New Appointment</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Create a new resident appointment with clear schedule details and status tracking.</p>
            </div>
            <div class="rounded-3xl border border-slate-800 bg-slate-950/80 p-4 text-sm text-slate-300 shadow-inner">
                <div class="flex items-center gap-2 text-indigo-300 font-semibold">
                    <i data-lucide="clock" class="h-4 w-4"></i>
                    <span>Quick schedule</span>
                </div>
                <p class="mt-2 text-slate-500">Keep appointment times accurate and choose a confirmed status when you’re ready to notify the resident.</p>
            </div>
        </div>
    </x-card>

    <div class="grid gap-6 lg:grid-cols-[1.8fr_1.2fr]">
        <x-card class="bg-slate-900/60 border-slate-800 shadow-sm">
            <form method="POST" action="{{ route('admin.appointments.store') }}" class="space-y-6 p-6">
                @csrf

                <div>
                    <label class="block text-sm font-black text-slate-200 mb-3">Resident</label>
                    <select name="resident_id" class="form-select" required>
                        <option value="" disabled selected>Select resident</option>
                        @foreach($residents as $res)
                            <option value="{{ $res->id }}">{{ $res->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-black text-slate-200 mb-3">Appointment Date</label>
                        <input type="date" name="appointment_date" class="form-control" required>
                    </div>
                    <div>
                        <label class="block text-sm font-black text-slate-200 mb-3">Appointment Time</label>
                        <input type="time" name="appointment_time" class="form-control" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-black text-slate-200 mb-3">Status</label>
                    <select name="status" class="form-select">
                        <option value="scheduled">scheduled</option>
                        <option value="confirmed">confirmed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-black text-slate-200 mb-3">Notes</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="Add internal notes or appointment instructions"></textarea>
                </div>

                <input type="hidden" name="document_request_id" value="">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm text-slate-500">All appointment records are saved and visible to staff only.</div>
                    <button class="btn btn-primary inline-flex items-center gap-2">
                        <i data-lucide="save" class="h-4 w-4"></i>
                        Save Appointment
                    </button>
                </div>
            </form>
        </x-card>

        <x-card title="Scheduling tips" icon="info" class="bg-slate-900/60 border-slate-800 shadow-sm">
            <div class="space-y-4 text-sm text-slate-300">
                <p>Select a resident and appointment time that matches your available staff calendar.</p>
                <ul class="space-y-2">
                    <li class="flex items-start gap-2">
                        <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full bg-indigo-500"></span>
                        Keep a 10-minute buffer for walk-ins and paperwork.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        Use confirmed status only after the appointment is finalized.
                    </li>
                </ul>
                <p class="text-slate-500">If the resident has a linked request, consider adding the appointment under the same service reference to keep follow-up actions aligned.</p>
            </div>
        </x-card>
    </div>
</div>
@endsection
