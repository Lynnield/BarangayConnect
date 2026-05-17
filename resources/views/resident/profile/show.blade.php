@extends('layouts.app')

@section('title', 'My Profile')

@section('breadcrumb')
    <span class="text-slate-500">Profile</span>
@endsection

@section('content')
<div class="w-full space-y-8 animate-in fade-in duration-700">
    <!-- Header Section -->
    <x-card class="border-none shadow-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950" :padding="false">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-8 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Personal Information</h1>
                <p class="text-sm text-slate-400 mt-2 font-medium">Manage your community identity and contact preferences.</p>
            </div>
            <x-button href="{{ route('resident.profile.edit') }}" variant="primary" size="md" icon="edit-3" class="shadow-indigo-600/20">
                Edit Profile
            </x-button>
        </div>
    </x-card>

    @if(!$resident)
        <x-card class="border-amber-500/20 bg-amber-500/5" :padding="true">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500 shrink-0">
                    <i data-lucide="alert-triangle" class="h-6 w-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-amber-500 uppercase tracking-tight">Profile Incomplete</h3>
                    <p class="text-amber-200/60 text-sm mt-1 leading-relaxed">
                        Your resident profile is not yet fully configured. To request official documents and schedule appointments, you must provide your complete details.
                    </p>
                    <div class="mt-4">
                        <x-button href="{{ route('resident.profile.edit') }}" variant="primary" size="sm" icon="check-circle" class="bg-amber-600 hover:bg-amber-500 shadow-amber-900/20">
                            Complete Setup Now
                        </x-button>
                    </div>
                </div>
            </div>
        </x-card>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar Column -->
            <div class="lg:col-span-1 space-y-8">
                <x-card class="bg-slate-900/50 border-slate-800 text-center" :padding="true">
                    <div class="relative mx-auto mb-6 h-32 w-32 group">
                        <div class="absolute inset-0 rounded-full bg-indigo-500 blur-2xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <img src="{{ auth()->user()->avatar_url }}" class="relative h-full w-full rounded-full border-4 border-slate-800 object-cover shadow-2xl">
                        <div class="absolute bottom-1 right-1 h-6 w-6 rounded-full bg-emerald-500 border-4 border-slate-900"></div>
                    </div>
                    <h2 class="text-2xl font-black text-white tracking-tight">{{ $resident->full_name }}</h2>
                    <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mt-2">{{ $resident->verification_status ?? 'pending' }} Community Member</p>
                    
                    <div class="mt-8 grid grid-cols-2 gap-4 border-t border-slate-800 pt-8">
                        <div class="text-left">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Status</p>
                            <p class="text-sm font-black text-emerald-500 mt-1">Active</p>
                        </div>
                        <div class="text-left">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Joined</p>
                            <p class="text-sm font-black text-slate-300 mt-1">{{ $resident->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </x-card>

                <x-card class="bg-indigo-600/5 border-indigo-500/20">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 shrink-0">
                            <i data-lucide="shield-check" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-white uppercase tracking-widest">Secure Data</h4>
                            <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                                Your personal information is encrypted and only accessible by authorized barangay officials for official processing.
                            </p>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Details Column -->
            <div class="lg:col-span-2">
                <x-card title="Resident Profile Details" icon="user" class="bg-slate-900/50 border-slate-800" :padding="false">
                    <div class="p-8 space-y-8">
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
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Email Address</span>
                                <p class="text-sm font-black text-white">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Civil Status</span>
                                <p class="text-sm font-black text-white capitalize">{{ $resident->civil_status }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Verification</span>
                                <p class="text-sm font-black text-white capitalize">{{ $resident->verification_status ?? 'pending' }}</p>
                            </div>
                            <div class="md:col-span-2 space-y-1">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Residential Address</span>
                                <p class="text-sm font-black text-white leading-relaxed">{{ $resident->address }}</p>
                            </div>
                        </div>

                        <div class="h-px bg-slate-800"></div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Date of Birth</span>
                                <p class="text-sm font-black text-white">{{ $resident->birthdate?->format('F d, Y') ?: 'Not Provided' }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Age</span>
                                <p class="text-sm font-black text-white">{{ $resident->birthdate?->age }} Years Old</p>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    @endif
</div>
@endsection
