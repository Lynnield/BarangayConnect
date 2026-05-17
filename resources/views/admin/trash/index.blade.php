@extends('layouts.app')

@section('title', 'Trash & Restore')

@section('breadcrumb')
    <span class="text-slate-500">Trash</span>
@endsection

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Trash & Restore</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Recover or permanently remove soft-deleted records across core resources.</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 border border-indigo-500/20">
                <i data-lucide="archive-restore" class="h-6 w-6"></i>
            </div>
        </div>
    </x-card>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @foreach($trash as $type => $records)
            <x-table-wrapper :title="ucwords(str_replace('_', ' ', $type))" icon="trash-2">
                <div class="divide-y divide-slate-800/50">
                    @forelse($records as $record)
                        <div class="p-4 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-xs font-black text-white truncate">
                                    {{ $record->name ?? $record->full_name ?? $record->request_number ?? $record->appointment_number ?? ('Record #' . $record->id) }}
                                </p>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-1">
                                    Deleted {{ optional($record->deleted_at)->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.trash.restore', [$type, $record->id]) }}">
                                    @csrf
                                    <button type="submit" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-500 hover:text-emerald-400 hover:border-emerald-400/50 hover:bg-emerald-400/5 transition-all" title="Restore">
                                        <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.trash.force-delete', [$type, $record->id]) }}" onsubmit="return confirm('Permanently delete this record? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-500 hover:text-rose-500 hover:border-rose-500/50 hover:bg-rose-500/5 transition-all" title="Delete permanently">
                                        <i data-lucide="trash" class="h-4 w-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-10 text-center">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">No deleted records</p>
                        </div>
                    @endforelse
                </div>
            </x-table-wrapper>
        @endforeach
    </div>
</div>
@endsection
