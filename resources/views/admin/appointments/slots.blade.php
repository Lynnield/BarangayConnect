@extends('layouts.app')

@section('title', 'Appointment Slots')

@section('breadcrumb')
    <a href="{{ route('admin.appointments.index') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">Appointments</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <span class="text-slate-300">Appointment Slots</span>
@endsection

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Appointment Slots</h1>
            <p class="mt-2 text-sm text-slate-500">Create and manage available appointment slots for residents.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <x-button href="{{ route('admin.appointments.index') }}" variant="secondary" size="md" icon="chevron-left">
                Back to Appointments
            </x-button>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-4">
            <x-card class="bg-slate-900/50 border-slate-800" :padding="false">
                <div class="p-6 space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                            <i data-lucide="calendar" class="h-4 w-4"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-black uppercase tracking-[0.2em] text-white">Add New Slot</h2>
                            <p class="text-xs text-slate-500">Add a new date and time block for scheduling.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.appointments.slots.store') }}" class="space-y-5">
                        @csrf

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Date</label>
                            <input type="date" name="slot_date" value="{{ old('slot_date') }}" required
                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                            @error('slot_date') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Time</label>
                            <input type="time" name="slot_time" value="{{ old('slot_time') }}" required
                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                            @error('slot_time') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Max Appointments</label>
                            <input type="number" name="max_appointments" value="{{ old('max_appointments', 5) }}" min="1"
                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                            @error('max_appointments') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Schedule Status</label>
                            <select name="is_available" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                                <option value="1" selected>Available</option>
                                <option value="0">Unavailable</option>
                            </select>
                        </div>

                        <x-button type="submit" variant="primary" size="md" class="w-full">
                            Save Slot
                        </x-button>
                    </form>
                </div>
            </x-card>
        </div>

        <div class="xl:col-span-8">
            <x-card class="bg-slate-900/50 border-slate-800" :padding="false">
                <div class="p-6 space-y-6">
                    <div class="flex flex-wrap items-center gap-3 justify-between">
                        <div>
                            <h2 class="text-sm font-black uppercase tracking-[0.2em] text-white">Existing Slots</h2>
                            <p class="text-xs text-slate-500">Review available appointment blocks and current capacity.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-list-sort
                                default="slot_date"
                                defaultDirection="asc"
                                :options="[
                                    'slot_date' => 'Date',
                                    'slot_time' => 'Time',
                                    'max_appointments' => 'Capacity',
                                ]"
                            />
                            <span class="rounded-full bg-slate-800 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ $slots->total() }} slots</span>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-950/40">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-900/80 text-[10px] uppercase tracking-[0.2em] text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Time</th>
                                    <th class="px-4 py-3">Max</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($slots as $s)
                                    <tr class="border-t border-slate-800 hover:bg-slate-900/60">
                                        <td class="px-4 py-4 font-semibold text-white">{{ $s->slot_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-4 text-slate-400">{{ \Carbon\Carbon::parse($s->slot_time)->format('H:i') }}</td>
                                        <td class="px-4 py-4 text-slate-400">{{ $s->max_appointments }}</td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] {{ $s->is_available ? 'bg-emerald-500/10 text-emerald-300' : 'bg-rose-500/10 text-rose-300' }}">
                                                {{ $s->is_available ? 'Available' : 'Unavailable' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.appointments.slots.edit', $s) }}" class="inline-flex h-9 items-center justify-center rounded-xl border border-slate-700 bg-slate-800 px-3 text-xs font-black uppercase tracking-[0.2em] text-slate-300 hover:border-indigo-400 hover:text-indigo-300 transition-all">Edit</a>
                                            <form method="POST" action="{{ route('admin.appointments.slots.destroy', $s) }}" onsubmit="return confirm('Delete this schedule?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-9 items-center justify-center rounded-xl border border-rose-500 bg-rose-500/10 px-3 text-xs font-black uppercase tracking-[0.2em] text-rose-300 hover:bg-rose-500/20 transition-all">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-slate-500">No appointment slots found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {{ $slots->links() }}
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
