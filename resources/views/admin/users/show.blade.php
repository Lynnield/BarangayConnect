@extends('layouts.app')

@section('title', $user->name)

@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">Users</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <span class="text-slate-300">Profile Details</span>
@endsection

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-8 animate-in fade-in duration-700">
    <!-- Header Card -->
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-8">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <img src="{{ $user->avatar_url }}" class="h-24 w-24 rounded-3xl border-2 border-indigo-500/30 shadow-2xl object-cover">
                    <div @class(['absolute -bottom-1 -right-1 h-5 w-5 rounded-full border-4 border-slate-900', 'bg-emerald-500' => $user->status === 'active', 'bg-slate-500' => $user->status === 'inactive', 'bg-rose-500' => $user->status === 'suspended'])></div>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tight">{{ $user->name }}</h1>
                    <div class="flex flex-wrap items-center gap-3 mt-2">
                        <x-badge type="primary">{{ $user->role?->name }}</x-badge>
                        <span class="text-slate-500 text-xs font-bold">•</span>
                        <span class="text-slate-400 text-xs font-medium">{{ $user->email }}</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <form method="POST" action="{{ route('admin.users.force-logout', $user) }}">
                    @csrf
                    <x-button type="submit" variant="secondary" size="md" icon="log-out">
                        Force Logout
                    </x-button>
                </form>
                <x-button href="{{ route('admin.users.edit', $user) }}" variant="primary" size="md" icon="edit-2" class="shadow-indigo-600/20">
                    Edit Account
                </x-button>
            </div>
        </div>
    </x-card>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Login History -->
        <x-table-wrapper title="Recent Access Logs" icon="shield-check">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-900/50 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                        <th class="px-6 py-4">Timestamp</th>
                        <th class="px-6 py-4">Network Info</th>
                        <th class="px-6 py-4">Result</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    @forelse($loginHistories as $h)
                        <tr class="hover:bg-slate-800/30 transition-all">
                            <td class="px-6 py-4">
                                <div class="text-xs font-bold text-slate-300">{{ $h->created_at->format('M d, Y') }}</div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $h->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-[10px] font-mono font-bold text-indigo-400 bg-indigo-500/5 px-2 py-1 rounded-lg border border-indigo-500/10 inline-block">
                                    {{ $h->ip_address }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($h->success)
                                    <x-badge type="success">Success</x-badge>
                                @else
                                    <div class="flex flex-col">
                                        <x-badge type="danger">Failed</x-badge>
                                        <span class="text-[9px] text-rose-500/70 font-bold mt-1 uppercase">{{ $h->failure_reason ?? 'Unknown' }}</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-[10px] font-black text-slate-600 uppercase tracking-widest">
                                No activity recorded
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-wrapper>

        <!-- Administrative Actions -->
        <div class="space-y-8">
            <x-card title="Security Override" icon="key" class="bg-slate-900/50 border-slate-800">
                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="space-y-6">
                    @csrf
                    <p class="text-[11px] text-slate-500 font-medium leading-relaxed italic">
                        Manually override this user's password. This action should only be performed in emergency recovery scenarios.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">New Secure Password</label>
                            <input type="password" name="new_password" required
                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                                placeholder="••••••••">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" required
                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <x-button type="submit" variant="danger" size="md" icon="shield-alert" class="w-full">
                        Update Security Credentials
                    </x-button>
                </form>
            </x-card>

            <x-card class="bg-indigo-600/5 border-indigo-500/20">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 shrink-0">
                        <i data-lucide="info" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-white uppercase tracking-widest">Account Insight</h4>
                        <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                            This account was registered on {{ $user->created_at->format('M d, Y') }}. 
                            Last seen active {{ $user->created_at->diffForHumans() }}.
                        </p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection