@extends('layouts.app')

@section('title', $resident->full_name)

@section('breadcrumb')
    <a href="{{ route('admin.residents.index') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">Residents</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <span class="text-slate-300">Profile Details</span>
@endsection

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-8 animate-in fade-in duration-700">
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-8">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="h-24 w-24 rounded-3xl bg-slate-800 border-2 border-indigo-500/30 shadow-2xl flex items-center justify-center text-slate-500">
                        <i data-lucide="user" class="h-12 w-12"></i>
                    </div>
                    <div class="absolute -bottom-1 -right-1 h-6 w-6 rounded-full bg-emerald-500 border-4 border-slate-900"></div>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tight">{{ $resident->full_name }}</h1>
                    <div class="flex flex-wrap items-center gap-3 mt-2">
                        @php
                            $verificationType = match($resident->verification_status) {
                                'verified' => 'success',
                                'rejected' => 'danger',
                                default => 'warning',
                            };
                        @endphp
                        <x-badge :type="$verificationType">{{ $resident->verification_status ?? 'pending' }}</x-badge>
                        <span class="text-slate-500 text-xs font-bold">•</span>
                        <span class="text-slate-400 text-xs font-medium">{{ $resident->resident_number }}</span>
                    </div>
                    <p class="mt-3 text-sm text-slate-400 max-w-2xl">Resident profile and request history for administrative management and support.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <x-button href="{{ route('admin.residents.edit', $resident) }}" variant="primary" size="md" icon="edit-3" class="shadow-indigo-600/20">
                    Edit Record
                </x-button>
            </div>
        </div>
    </x-card>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <x-card title="Personal Information" icon="user" class="bg-slate-900/50 border-slate-800">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-1">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Full Name</span>
                        <p class="text-sm font-black text-white">{{ $resident->full_name }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Contact Number</span>
                        <p class="text-sm font-black text-white">{{ $resident->contact_number ?: 'Not Provided' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Gender</span>
                        <p class="text-sm font-black text-white capitalize">{{ $resident->gender }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Civil Status</span>
                        <p class="text-sm font-black text-white capitalize">{{ $resident->civil_status }}</p>
                    </div>
                    <div class="md:col-span-2 space-y-1">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Residential Address</span>
                        <p class="text-sm font-black text-white leading-relaxed">{{ $resident->address }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Verification</span>
                        <p class="text-sm font-black text-white capitalize">{{ $resident->verification_status ?? 'pending' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Verified At</span>
                        <p class="text-sm font-black text-white">{{ $resident->verified_at?->format('M d, Y g:i A') ?: 'Not verified' }}</p>
                    </div>
                </div>
            </x-card>

            <x-table-wrapper title="Application History" icon="history">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-900/50 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                            <th class="px-6 py-4">Reference</th>
                            <th class="px-6 py-4">Document Type</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($resident->documentRequests as $d)
                            <tr class="hover:bg-slate-800/30 transition-all group">
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-slate-400 group-hover:text-indigo-400 transition-colors">{{ $d->request_number }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-black text-white">{{ $d->documentType->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge type="neutral">{{ $d->status }}</x-badge>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.requests.show', $d) }}" class="text-indigo-400 hover:text-indigo-300 transition-colors">
                                        <i data-lucide="external-link" class="h-4 w-4 ml-auto"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-[10px] font-black text-slate-600 uppercase tracking-widest">
                                    No document requests recorded
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-table-wrapper>
        </div>

        <div class="lg:col-span-1 space-y-8">
            <x-card title="System Access" icon="shield-check" class="bg-slate-900/50 border-slate-800">
                <div class="space-y-6">
                    <div class="space-y-1">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Linked Account</span>
                        <p class="text-xs font-bold text-white">{{ $resident->user?->email ?: 'No account linked' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Account Created</span>
                        <p class="text-xs font-bold text-white">{{ $resident->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-indigo-600/5 border-indigo-500/20">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 shrink-0">
                        <i data-lucide="info" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-white uppercase tracking-widest">Admin Note</h4>
                        <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                            Use this profile to manage resident details, verify status, and review document request history.
                        </p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
