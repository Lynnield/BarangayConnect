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
                <h1 class="text-3xl font-black text-white tracking-tight">Resident Directory</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Access and manage community member profiles and information.</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 border border-indigo-500/20">
                <i data-lucide="users" class="h-6 w-6"></i>
            </div>
        </div>
    </x-card>

    <!-- Search & Filters -->
    <x-card class="bg-slate-900/50 border-slate-800" :padding="false">
        <form method="GET" action="{{ route('staff.residents.index') }}" class="p-6 flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Search Resident</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                        <i data-lucide="search" class="h-4 w-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 pl-11 pr-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner" 
                        placeholder="Search by name, resident #, or contact...">
                </div>
            </div>
            <div class="flex gap-3 w-full md:w-auto">
                <x-button type="submit" variant="secondary" size="md" class="flex-1 md:flex-none" icon="filter">Search</x-button>
                @if(request('search'))
                    <x-button type="button" variant="ghost" size="md" onclick="window.location.href='{{ route('staff.residents.index') }}'" icon="rotate-ccw"></x-button>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Table Section -->
    <x-table-wrapper title="Community Members" icon="users">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-900/50 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    <th class="px-6 py-4">Resident Info</th>
                    <th class="px-6 py-4">Contact Details</th>
                    <th class="px-6 py-4">Demographics</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($residents as $r)
                    <tr class="hover:bg-slate-800/30 transition-all group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500 group-hover:bg-indigo-500 group-hover:text-white transition-all">
                                    <i data-lucide="user" class="h-5 w-5"></i>
                                </div>
                                <div>
                                    <div class="font-black text-white group-hover:text-indigo-400 transition-colors">{{ $r->full_name }}</div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $r->resident_number }}</div>
                                    <div class="mt-1">
                                        <x-badge type="{{ $r->verification_status === 'verified' ? 'success' : ($r->verification_status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $r->verification_status ?? 'pending' }}
                                        </x-badge>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-slate-300">{{ $r->contact_number ?: 'No Contact' }}</div>
                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-tight mt-0.5">{{ $r->user?->email }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-slate-300 capitalize">{{ $r->gender }}</div>
                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $r->birthdate?->age }} Years Old</div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <x-button href="{{ route('staff.residents.show', $r) }}" variant="secondary" size="xs" icon="eye">
                                Profile
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-24 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div class="h-20 w-20 rounded-3xl bg-slate-800/50 flex items-center justify-center text-slate-700 shadow-inner">
                                    <i data-lucide="users" class="h-10 w-10"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-white">No Residents Found</h3>
                                    <p class="text-sm text-slate-500 max-w-xs mx-auto mt-1 font-medium italic">We couldn't find any residents matching your search criteria.</p>
                                </div>
                                <x-button href="{{ route('staff.residents.index') }}" variant="outline" size="sm" icon="rotate-ccw">
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
