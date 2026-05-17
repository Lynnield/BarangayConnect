@extends('layouts.app')

@section('title', 'New User Account')

@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}" class="text-slate-500 hover:text-indigo-400 transition-colors">Users</a>
    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-700"></i>
    <span class="text-slate-300">Create Account</span>
@endsection

@section('content')
<div class="w-full max-w-4xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-400 hover:text-white hover:border-slate-600 transition-all">
            <i data-lucide="arrow-left" class="h-5 w-5"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Create User Account</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Register a new administrative or staff member to the system.</p>
        </div>
    </div>

    <x-card class="bg-slate-900/50 border-slate-800" :padding="false">
        <form method="POST" action="{{ route('admin.users.store') }}" class="p-8 space-y-8">
            @csrf
            
            <!-- Account Information -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                        <i data-lucide="user" class="h-4 w-4"></i>
                    </div>
                    <h2 class="text-xs font-black text-white uppercase tracking-[0.2em]">Account Information</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                            placeholder="e.g. John Doe">
                        @error('name') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                            placeholder="john@example.com">
                        @error('email') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Password</label>
                        <input type="password" name="password" required
                            class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                            placeholder="••••••••">
                        @error('password') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required
                            class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                            placeholder="••••••••">
                    </div>
                </div>
            </div>

            <!-- Access Control -->
            <div class="space-y-6 pt-8 border-t border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                        <i data-lucide="shield-check" class="h-4 w-4"></i>
                    </div>
                    <h2 class="text-xs font-black text-white uppercase tracking-[0.2em]">Access & Status</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">System Role</label>
                        <div class="relative">
                            <select name="role_id" required
                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                @foreach($roles as $r)
                                    <option value="{{ $r->id }}" @selected(old('role_id') == $r->id)>{{ $r->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                                <i data-lucide="chevron-down" class="h-4 w-4"></i>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Account Status</label>
                        <div class="relative">
                            <select name="status"
                                class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none">
                                <option value="active" @selected(old('status') == 'active')>Active</option>
                                <option value="inactive" @selected(old('status') == 'inactive')>Inactive</option>
                                <option value="suspended" @selected(old('status') == 'suspended')>Suspended</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-600">
                                <i data-lucide="chevron-down" class="h-4 w-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Details -->
            <div class="space-y-6 pt-8 border-t border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500">
                        <i data-lucide="contact-2" class="h-4 w-4"></i>
                    </div>
                    <h2 class="text-xs font-black text-white uppercase tracking-[0.2em]">Contact Details</h2>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                            placeholder="e.g. +63 900 000 0000">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Residential Address</label>
                        <textarea name="address" rows="3"
                            class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-xs text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none resize-none"
                            placeholder="Complete address information...">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-8 border-t border-slate-800">
                <x-button type="button" variant="ghost" size="md" onclick="window.location.href='{{ route('admin.users.index') }}'">
                    Discard Changes
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="check" class="shadow-indigo-600/20">
                    Create Account
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection