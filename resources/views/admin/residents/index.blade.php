@extends('layouts.app')

@section('title', 'Residents')
@section('breadcrumb')
    <span class="text-slate-500">Residents</span>
@endsection

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <!-- Header Section -->
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Resident Records</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Manage and monitor community members profile and data.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <x-button href="{{ route('admin.residents.import-form') }}" variant="secondary" size="md" icon="upload">
                    Import
                </x-button>
                <x-button href="{{ route('admin.residents.create') }}" variant="primary" size="md" icon="plus" class="shadow-indigo-600/20">
                    Add Resident
                </x-button>
            </div>
        </div>
    </x-card>

    @if(session('import_errors'))
        <x-card class="border-rose-500/20 bg-rose-500/5" :padding="true">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xs font-black text-rose-500 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="alert-circle" class="h-4 w-4"></i>
                    Import Validation Errors
                </h2>
                <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest bg-rose-500/10 px-2 py-1 rounded-lg">
                    {{ count(session('import_errors')) }} Rows Failed
                </span>
            </div>
            <div class="max-h-60 overflow-y-auto space-y-3 pr-2 scrollbar-thin scrollbar-thumb-rose-500/20">
                @foreach(session('import_errors') as $error)
                    <div class="text-xs text-rose-200 bg-slate-950/40 p-4 rounded-2xl border border-rose-500/10">
                        <span class="font-black text-rose-400 uppercase tracking-tight">Row {{ $error['row'] }} ({{ $error['name'] }}):</span>
                        <ul class="mt-2 space-y-1 list-disc list-inside text-slate-400">
                            @foreach($error['errors'] as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    <!-- Search & Filters -->
    <x-card class="bg-slate-900/50 border-slate-800" :padding="false">
        <form method="GET" action="{{ route('admin.residents.index') }}" class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Search Directory</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                        <i data-lucide="search" class="h-4 w-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 pl-11 pr-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner" 
                        placeholder="Search by name, ID, or address...">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Gender</label>
                <div class="relative group">
                    <select name="gender" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none shadow-inner">
                        <option value="">All Genders</option>
                        <option value="male" @selected(request('gender') === 'male')>Male</option>
                        <option value="female" @selected(request('gender') === 'female')>Female</option>
                        <option value="other" @selected(request('gender') === 'other')>Other</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                        <i data-lucide="chevron-down" class="h-4 w-4"></i>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <x-button type="submit" variant="secondary" size="md" class="flex-1" icon="filter">Filter</x-button>
                @if(request()->anyFilled(['search', 'gender']))
                    <x-button type="button" variant="ghost" size="md" onclick="window.location.href='{{ route('admin.residents.index') }}'" icon="rotate-ccw"></x-button>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Results Table -->
    <x-table-wrapper title="Community Directory" icon="users">
        <x-slot:action>
            <div class="flex items-center gap-2">
                <x-button href="{{ route('admin.residents.export', ['format' => 'csv']) }}" variant="ghost" size="xs" icon="download">CSV</x-button>
                <x-button href="{{ route('admin.residents.export', ['format' => 'json']) }}" variant="ghost" size="xs" icon="code">JSON</x-button>
            </div>
        </x-slot:action>

        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-900/50 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    <th class="px-6 py-4">Resident Info</th>
                    <th class="px-6 py-4">Contact & Status</th>
                    <th class="px-6 py-4">Demographics</th>
                    <th class="px-6 py-4">Address</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($residents as $res)
                    <tr class="hover:bg-slate-800/30 transition-all group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500 group-hover:bg-indigo-500 group-hover:text-white transition-all">
                                    <i data-lucide="user" class="h-5 w-5"></i>
                                </div>
                                <div>
                                    <div class="font-black text-white group-hover:text-indigo-400 transition-colors">{{ $res->full_name }}</div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $res->resident_number }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-slate-300">{{ $res->contact_number ?: 'No Contact' }}</div>
                            <div class="mt-1">
                                <x-badge type="neutral">{{ $res->civil_status }}</x-badge>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-slate-300">{{ ucfirst($res->gender) }}</div>
                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-tight mt-0.5">{{ $res->birthdate?->age }} Years Old</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-xs text-slate-400 font-medium max-w-[200px] truncate group-hover:text-slate-200 transition-colors" title="{{ $res->address }}">
                                {{ $res->address }}
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.residents.show', $res) }}" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-500 hover:text-indigo-400 hover:border-indigo-400/50 hover:bg-indigo-400/5 transition-all" title="View Profile">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </a>
                                <a href="{{ route('admin.residents.edit', $res) }}" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-500 hover:text-blue-400 hover:border-blue-400/50 hover:bg-blue-400/5 transition-all" title="Edit Record">
                                    <i data-lucide="edit-2" class="h-4 w-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div class="h-20 w-20 rounded-3xl bg-slate-800/50 flex items-center justify-center text-slate-700 shadow-inner">
                                    <i data-lucide="users" class="h-10 w-10"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-white">No Residents Registered</h3>
                                    <p class="text-sm text-slate-500 max-w-xs mx-auto mt-1 font-medium italic">We couldn't find any residents matching your search criteria.</p>
                                </div>
                                <x-button href="{{ route('admin.residents.index') }}" variant="outline" size="sm" icon="rotate-ccw">
                                    Clear Search
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($residents->hasPages())
            <x-slot:footer>
                <div class="px-2">
                    {{ $residents->links() }}
                </div>
            </x-slot:footer>
        @endif
    </x-table-wrapper>
</div>
@endsection