@extends('layouts.app')
@section('title','Appointment calendar')
@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <div class="grid gap-6 md:grid-cols-[1fr_auto] md:items-end">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 rounded-full bg-slate-900/70 px-4 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-slate-800/70">
                <i data-lucide="calendar" class="h-4 w-4"></i>
                <span>Appointment Calendar</span>
            </div>
            <p class="text-sm text-slate-400 max-w-2xl">View staff appointments grouped by day with quick status badges and resident details. Use the month selector to jump between schedules.</p>
        </div>
        <form method="get" class="flex items-center gap-3">
            <label for="month" class="sr-only">Select month</label>
            <input id="month" type="month" name="month" value="{{ $month }}" class="rounded-2xl border border-slate-700 bg-slate-800/60 py-3 px-4 text-sm text-white outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
        </form>
    </div>

    @php
        $byDay = $items->groupBy(fn($i) => $i->appointment_date->format('Y-m-d'));
        $totalAppointments = $items->count();
    @endphp

    <x-card class="bg-slate-900/70 border-slate-800 shadow-sm" :padding="false">
        <div class="p-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-3xl bg-slate-950/70 p-4 border border-slate-800">
                <span class="text-sm uppercase tracking-[0.24em] text-slate-500">Month total</span>
                <div class="mt-3 text-4xl font-black text-white">{{ $totalAppointments }}</div>
                <p class="text-sm text-slate-500 mt-2">appointments in {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</p>
            </div>
            <div class="rounded-3xl bg-slate-950/70 p-4 border border-slate-800">
                <span class="text-sm uppercase tracking-[0.24em] text-slate-500">Days scheduled</span>
                <div class="mt-3 text-4xl font-black text-white">{{ $byDay->count() }}</div>
                <p class="text-sm text-slate-500 mt-2">active appointment days this month</p>
            </div>
            <div class="rounded-3xl bg-slate-950/70 p-4 border border-slate-800">
                <span class="text-sm uppercase tracking-[0.24em] text-slate-500">Next action</span>
                <div class="mt-3 text-4xl font-black text-white">{{ $items->first()?->appointment_date?->format('M d') ?? 'N/A' }}</div>
                <p class="text-sm text-slate-500 mt-2">next upcoming appointment</p>
            </div>
        </div>
    </x-card>

    @if($items->isEmpty())
        <x-card class="bg-slate-900/70 border-slate-800 text-center">
            <div class="py-12">
                <i data-lucide="calendar-off" class="mx-auto h-12 w-12 text-slate-500"></i>
                <h3 class="mt-5 text-xl font-black text-white">No appointments found</h3>
                <p class="mt-3 text-sm text-slate-400">There are no scheduled appointments for this month. Create a new appointment to populate the calendar.</p>
                <x-button href="{{ route('staff.appointments.create') }}" variant="primary" size="sm" icon="plus" class="mt-6">
                    Create Appointment
                </x-button>
            </div>
        </x-card>
    @else
        <div class="grid gap-4">
            @foreach($byDay as $day => $list)
                <div class="rounded-[2rem] border border-slate-800 bg-slate-950/80 p-6 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="text-xs uppercase tracking-[0.28em] text-slate-500">{{ \Carbon\Carbon::parse($day)->format('l, F j') }}</div>
                            <div class="mt-1 text-xl font-black text-white">{{ $list->count() }} appointment{{ $list->count() === 1 ? '' : 's' }}</div>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-slate-900/70 px-4 py-2 text-sm text-slate-300 ring-1 ring-slate-800/80">
                            <i data-lucide="clock" class="h-4 w-4"></i>
                            <span>{{ \Carbon\Carbon::parse($day)->diffForHumans(['parts' => 2, 'short' => true]) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4">
                        @foreach($list as $item)
                            @php
                                $statusType = match($item->status) {
                                    'confirmed' => 'success',
                                    'pending' => 'warning',
                                    'cancelled' => 'danger',
                                    'completed' => 'primary',
                                    default => 'neutral',
                                };
                            @endphp
                            <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-4 sm:p-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-3 text-sm text-slate-400">
                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-300">
                                            <i data-lucide="user" class="h-4 w-4"></i>
                                        </span>
                                        <span class="font-semibold text-slate-200">{{ $item->resident->full_name }}</span>
                                    </div>
                                    <div class="text-sm text-slate-400">
                                        <span class="font-semibold text-slate-200">{{ \Carbon\Carbon::parse($item->appointment_time)->format('g:i A') }}</span>
                                        <span class="mx-2">•</span>
                                        {{ ucfirst($item->status) }}
                                    </div>
                                </div>
                                <div class="flex flex-col gap-3 sm:items-end">
                                    <x-badge :type="$statusType" class="uppercase tracking-[0.18em] py-2 px-3 text-[11px]">{{ $item->status }}</x-badge>
                                    <x-button href="{{ route('staff.appointments.show', $item) }}" variant="secondary" size="xs" icon="external-link">
                                        View details
                                    </x-button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
