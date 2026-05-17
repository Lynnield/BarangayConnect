@extends('layouts.app')
@section('title', 'User Management')
@section('breadcrumb')
<span class="text-slate-900">Users</span>
@endsection
@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">User Management</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Oversee system accounts, roles, and security statuses.</p>
            </div>
            <x-button href="{{ route('admin.users.create') }}" variant="primary" size="md" icon="plus" class="shadow-indigo-600/20">
                New User Account
            </x-button>
        </div>
    </x-card>

    <!-- Advanced Filters -->
    <x-card class="bg-slate-900/50 border-slate-800" :padding="false">
        <form method="get" class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Quick Search</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                        <i data-lucide="search" class="h-4 w-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 pl-11 pr-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner" 
                        placeholder="Name, email, or phone...">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Role Type</label>
                <div class="relative group">
                    <select name="role" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none shadow-inner">
                        <option value="">All Active Roles</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->slug }}" @selected(request('role') == $r->slug)>{{ $r->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                        <i data-lucide="chevron-down" class="h-4 w-4"></i>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Account Status</label>
                <div class="relative group">
                    <select name="status" class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none shadow-inner">
                        <option value="">All Statuses</option>
                        @foreach(['active', 'inactive', 'suspended'] as $s)
                            <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                        <i data-lucide="chevron-down" class="h-4 w-4"></i>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <x-button type="submit" variant="secondary" size="md" class="flex-1" icon="filter">Filter</x-button>
                <x-button type="button" variant="ghost" size="md" onclick="window.location.href='{{ route('admin.users.index') }}'" icon="rotate-ccw"></x-button>
            </div>
        </form>
    </x-card>

    <x-table-wrapper title="System Users" icon="users">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-900/50 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    <th class="px-6 py-4">Identity</th>
                    <th class="px-6 py-4">Privileges</th>
                    <th class="px-6 py-4">Account Status</th>
                    <th class="px-6 py-4">Registration Date</th>
                    <th class="px-6 py-4 text-right">Administrative Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($users as $u)
                    <tr class="hover:bg-slate-800/30 transition-all group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <img src="{{ $u->avatar_url }}" class="h-11 w-11 rounded-2xl border border-slate-700 shadow-xl object-cover group-hover:scale-105 transition-transform">
                                    <span @class(['absolute -bottom-1 -right-1 h-3.5 w-3.5 rounded-full border-2 border-slate-900', 'bg-emerald-500' => $u->status === 'active', 'bg-slate-500' => $u->status === 'inactive', 'bg-rose-500' => $u->status === 'suspended'])></span>
                                </div>
                                <div>
                                    <div class="font-black text-white group-hover:text-indigo-400 transition-colors">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-500 font-medium">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $roleType = match($u->role?->slug) {
                                    'admin' => 'primary',
                                    'staff' => 'info',
                                    default => 'neutral',
                                };
                            @endphp
                            <x-badge :type="$roleType">{{ $u->role?->name }}</x-badge>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $statusType = match($u->status) {
                                    'active' => 'success',
                                    'suspended' => 'danger',
                                    default => 'neutral',
                                };
                            @endphp
                            <x-badge :type="$statusType">{{ $u->status }}</x-badge>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-slate-400">{{ $u->created_at->format('M d, Y') }}</div>
                            <div class="text-[10px] text-slate-600 font-medium uppercase tracking-tighter">{{ $u->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-3">
                                @if(Auth::id() !== $u->id)
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline" onsubmit="return confirm('Soft-delete this user? They can be restored from Trash within 30 days.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-500 hover:text-rose-500 hover:border-rose-500/50 hover:bg-rose-500/5 transition-all" title="Delete">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.users.show', $u) }}" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-500 hover:text-indigo-400 hover:border-indigo-400/50 hover:bg-indigo-400/5 transition-all" title="View">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $u) }}" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-500 hover:text-blue-400 hover:border-blue-400/50 hover:bg-blue-400/5 transition-all" title="Edit">
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
                                    <i data-lucide="users-round" class="h-10 w-10"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-white">No Users Found</h3>
                                    <p class="text-sm text-slate-500 max-w-xs mx-auto mt-1 font-medium italic">We couldn't find any users matching your criteria.</p>
                                </div>
                                <x-button href="{{ route('admin.users.index') }}" variant="outline" size="sm" icon="rotate-ccw">
                                    Clear Filters
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($users->hasPages())
            <x-slot:footer>
                <div class="px-2">
                    {{ $users->links() }}
                </div>
            </x-slot:footer>
        @endif
    </x-table-wrapper>
</div>
@endsection
