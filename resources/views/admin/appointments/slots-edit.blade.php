@extends('layouts.app')

@section('title', 'Edit Schedule')

@section('breadcrumb')
    <a href="{{ route('admin.appointments.slots') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">Appointment Schedules</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <span class="text-slate-300">Edit Schedule</span>
@endsection

@section('content')
<div class="w-full max-w-4xl mx-auto space-y-8 animate-in fade-in duration-700">
    <x-card class="bg-slate-900/50 border-slate-800" :padding="false">
        <div class="p-6 space-y-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Edit Schedule</h1>
                <p class="mt-2 text-sm text-slate-400">Update the appointment schedule details below.</p>
            </div>

            <form method="POST" action="{{ route('admin.appointments.slots.update', $slot) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-6 md:grid-cols-2">
                    <label class="block">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Date</span>
                        <input type="date" name="slot_date" value="{{ old('slot_date', $slot->slot_date->format('Y-m-d')) }}" required
                            class="mt-3 block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                        @error('slot_date') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                    </label>

                    <label class="block">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Time</span>
                        <input type="time" name="slot_time" value="{{ old('slot_time', $slot->slot_time) }}" required
                            class="mt-3 block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                        @error('slot_time') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                    </label>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <label class="block">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Max Appointments</span>
                        <input type="number" name="max_appointments" value="{{ old('max_appointments', $slot->max_appointments) }}" min="1"
                            class="mt-3 block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                        @error('max_appointments') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                    </label>

                    <label class="block">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Schedule Status</span>
                        <select name="is_available" class="mt-3 block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                            <option value="1" @selected(old('is_available', $slot->is_available) == 1)>Available</option>
                            <option value="0" @selected(old('is_available', $slot->is_available) == 0)>Unavailable</option>
                        </select>
                        @error('is_available') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                    </label>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <x-button type="submit" variant="primary" size="lg">
                        Save Schedule
                    </x-button>
                    <a href="{{ route('admin.appointments.slots') }}" class="text-sm font-black uppercase tracking-[0.2em] text-slate-500 hover:text-white transition-colors">Back to Schedules</a>
                </div>
            </form>
        </div>
    </x-card>
</div>
@endsection
