@extends('layouts.app')

@section('title', 'System Backups')

@section('breadcrumb')
    <span class="text-slate-500">Backups</span>
@endsection

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <!-- Header Section -->
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">System Backups</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Protect your data by creating and managing database snapshots.</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="inline-flex h-2 w-2 rounded-full bg-indigo-500"></span>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                        Support: SQLite & MySQL (mysqldump)
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.backups.create') }}">
                @csrf
                <x-button type="submit" variant="primary" size="md" icon="database" class="shadow-indigo-600/20">
                    Run Backup Now
                </x-button>
            </form>
        </div>
    </x-card>

    <!-- Table Section -->
    <x-table-wrapper title="Backup Archives" icon="archive">
        <x-slot:action>
            <x-list-sort
                default="created_at"
                defaultDirection="desc"
                :options="[
                    'created_at' => 'Date created',
                    'backup_name' => 'Backup name',
                    'file_size' => 'File size',
                    'status' => 'Status',
                ]"
            />
        </x-slot:action>
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-900/50 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    <th class="px-6 py-4">Backup Name</th>
                    <th class="px-6 py-4">Storage Type</th>
                    <th class="px-6 py-4">Current Status</th>
                    <th class="px-6 py-4">File Size</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($backups as $b)
                    <tr class="hover:bg-slate-800/30 transition-all group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500 group-hover:bg-indigo-500 group-hover:text-white transition-all">
                                    <i data-lucide="file-archive" class="h-5 w-5"></i>
                                </div>
                                <div>
                                    <div class="font-black text-white group-hover:text-indigo-400 transition-colors">{{ $b->backup_name }}</div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Database Snapshot</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <code class="px-2 py-1 rounded-lg bg-slate-950 border border-slate-800 text-[10px] font-bold text-slate-400 uppercase tracking-tight">
                                {{ $b->backup_type }}
                            </code>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $statusType = match($b->status) {
                                    'completed' => 'success',
                                    'running' => 'warning',
                                    'failed' => 'danger',
                                    default => 'neutral',
                                };
                            @endphp
                            <x-badge :type="$statusType">{{ $b->status }}</x-badge>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-slate-300">{{ $b->file_size_formatted }}</div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($b->status === 'completed' && $b->file_path)
                                    <a href="{{ route('admin.backups.download', $b) }}" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-500 hover:text-emerald-400 hover:border-emerald-400/50 hover:bg-emerald-400/5 transition-all" title="Download">
                                        <i data-lucide="download" class="h-4 w-4"></i>
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('admin.backups.destroy', $b) }}" class="inline" onsubmit="return confirm('Permanently delete this backup record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-500 hover:text-rose-500 hover:border-rose-500/50 hover:bg-rose-500/5 transition-all" title="Delete">
                                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div class="h-20 w-20 rounded-3xl bg-slate-800/50 flex items-center justify-center text-slate-700 shadow-inner">
                                    <i data-lucide="database-backup" class="h-10 w-10"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-white">No Backups Found</h3>
                                    <p class="text-sm text-slate-500 max-w-xs mx-auto mt-1 font-medium italic">No database snapshots have been created yet.</p>
                                </div>
                                <form method="POST" action="{{ route('admin.backups.create') }}">
                                    @csrf
                                    <x-button type="submit" variant="outline" size="sm" icon="plus">
                                        Create First Backup
                                    </x-button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($backups->hasPages())
            <x-slot:footer>
                <div class="px-2">
                    {{ $backups->links() }}
                </div>
            </x-slot:footer>
        @endif
    </x-table-wrapper>
</div>
@endsection