@extends('layouts.guest')
@section('title', 'Create Account')
@section('container_width', 'max-w-2xl')
@section('content')
<div class="text-center mb-10 animate-in fade-in slide-in-from-top-6 duration-700">
    <div class="mx-auto mb-8 w-max">
        <x-barangay-logo size="2xl" class="mx-auto" />
    </div>
    <h1 class="text-4xl font-black text-white tracking-tight">Create Account</h1>
    <p class="text-slate-500 mt-3 font-medium text-lg italic">Join our digital community today.</p>
</div>

<div class="rounded-3xl bg-slate-900/40 backdrop-blur-xl p-8 lg:p-10 shadow-2xl border border-slate-800 animate-in fade-in zoom-in-95 duration-500">
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 mb-3 ml-1">Display Name</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                        <i data-lucide="user" class="h-4.5 w-4.5"></i>
                    </div>
                    <input type="text" name="name" value="{{ old('name') }}" 
                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3.5 pl-12 pr-4 text-sm text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner" 
                        placeholder="John Doe" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 mb-3 ml-1">Email Address</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                        <i data-lucide="mail" class="h-4.5 w-4.5"></i>
                    </div>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3.5 pl-12 pr-4 text-sm text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner" 
                        placeholder="john@example.com" required>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 mb-3 ml-1">Phone Number</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                    <i data-lucide="phone" class="h-4.5 w-4.5"></i>
                </div>
                <input type="text" name="phone" value="{{ old('phone') }}" 
                    class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3.5 pl-12 pr-4 text-sm text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner" 
                    placeholder="09123456789">
            </div>
        </div>

        <div>
            <label class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 mb-3 ml-1">Residential Address</label>
            <div class="relative group">
                <div class="absolute top-4 left-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                    <i data-lucide="map-pin" class="h-4.5 w-4.5"></i>
                </div>
                <textarea name="address" rows="2"
                    class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3.5 pl-12 pr-4 text-sm text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner" 
                    placeholder="House No., Street, Barangay">{{ old('address') }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 mb-3 ml-1">Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                        <i data-lucide="lock" class="h-4.5 w-4.5"></i>
                    </div>
                    <input type="password" name="password" 
                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3.5 pl-12 pr-4 text-sm text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner" 
                        placeholder="••••••••" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 mb-3 ml-1">Confirm Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                        <i data-lucide="shield-check" class="h-4.5 w-4.5"></i>
                    </div>
                    <input type="password" name="password_confirmation" 
                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-3.5 pl-12 pr-4 text-sm text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner" 
                        placeholder="••••••••" required>
                </div>
            </div>
        </div>

        <x-button type="submit" variant="primary" size="lg" icon="user-plus" class="w-full py-4 text-sm shadow-indigo-600/20">
            Create Official Account
        </x-button>
    </form>

    <div class="mt-10 pt-8 border-t border-slate-800 text-center">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">
            Already have an account? 
            <a href="{{ route('login') }}" class="ml-2 text-indigo-400 hover:text-indigo-300 transition-colors">Sign In</a>
        </p>
    </div>
</div>
@endsection
