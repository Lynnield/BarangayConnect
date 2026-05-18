@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('breadcrumb')
    <span class="text-slate-500">Roles & Permissions</span>
@endsection

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <!-- Header Section -->
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Roles & Permissions</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Define and manage system access levels and user capabilities.</p>
            </div>
            <x-button href="{{ route('admin.roles.create') }}" variant="primary" size="md" icon="plus" class="shadow-indigo-600/20">
                Create New Role
            </x-button>
        </div>
    </x-card>

    <!-- Roles Table -->
    <x-table-wrapper title="System Roles" icon="shield-check">
        <x-slot:action>
            <x-list-sort
                default="name"
                defaultDirection="asc"
                :options="[
                    'name' => 'Role name',
                    'users' => 'User count',
                    'created_at' => 'Date created',
                ]"
            />
        </x-slot:action>
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-900/50 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                    <th class="px-6 py-4">Role Identity</th>
                    <th class="px-6 py-4">System Slug</th>
                    <th class="px-6 py-4">Active Users</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @foreach($roles as $r)
                    <tr class="hover:bg-slate-800/30 transition-all group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500 group-hover:bg-indigo-500 group-hover:text-white transition-all">
                                    <i data-lucide="shield" class="h-5 w-5"></i>
                                </div>
                                <div>
                                    <div class="font-black text-white group-hover:text-indigo-400 transition-colors">{{ $r->name }}</div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Access Level</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <code class="px-2 py-1 rounded-lg bg-slate-950 border border-slate-800 text-[10px] font-bold text-indigo-400 uppercase tracking-tight">
                                {{ $r->slug }}
                            </code>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                                <span class="text-sm font-bold text-slate-300">{{ $r->users_count }}</span>
                                <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Accounts</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.roles.show', $r) }}" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-500 hover:text-indigo-400 hover:border-indigo-400/50 hover:bg-indigo-400/5 transition-all" title="View Details">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </a>
                                <a href="{{ route('admin.roles.edit', $r) }}" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-500 hover:text-blue-400 hover:border-blue-400/50 hover:bg-blue-400/5 transition-all" title="Edit Role">
                                    <i data-lucide="edit-2" class="h-4 w-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($roles->hasPages())
            <x-slot:footer>
                <div class="px-2">
                    {{ $roles->links() }}
                </div>
            </x-slot:footer>
        @endif
    </x-table-wrapper>
</div>
@endsection