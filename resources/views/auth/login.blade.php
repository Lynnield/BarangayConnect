@extends('layouts.guest')
@section('title', 'Sign In')
@section('content')
<div class="text-center mb-10 animate-in fade-in slide-in-from-top-6 duration-700">
    <div class="mx-auto mb-8 w-max">
        <x-barangay-logo size="2xl" class="mx-auto" />
    </div>
    <h1 class="text-4xl font-black text-white tracking-tight">Welcome Back</h1>
    <p class="text-slate-500 mt-3 font-medium text-lg italic">"Connecting San Jose, one citizen at a time."</p>
</div>

<div class="rounded-3xl bg-slate-900/40 backdrop-blur-xl p-8 lg:p-10 shadow-2xl border border-slate-800 animate-in fade-in zoom-in-95 duration-500">
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf
        <div>
            <label class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 mb-3 ml-1">Email Address</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                    <i data-lucide="mail" class="h-4.5 w-4.5"></i>
                </div>
                <input type="email" name="email" value="{{ old('email') }}" 
                    class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-4 pl-12 pr-4 text-sm text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner" 
                    placeholder="name@example.com" required autofocus data-validate="email">
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-3 ml-1">
                <label class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500">Password</label>
                <a href="{{ route('password.request') }}" class="text-[10px] font-black uppercase tracking-widest text-indigo-400 hover:text-indigo-300 transition-colors">Forgot Password?</a>
            </div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                    <i data-lucide="lock" class="h-4.5 w-4.5"></i>
                </div>
                <input type="password" name="password" 
                    class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-4 pl-12 pr-4 text-sm text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner" 
                    placeholder="••••••••" required data-validate="password">
            </div>
        </div>

        <div class="flex items-center ml-1">
            <label class="relative flex items-center cursor-pointer group">
                <input type="checkbox" name="remember" id="remember" class="peer sr-only">
                <div class="h-5 w-5 rounded-md border border-slate-700 bg-slate-800/50 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center">
                    <i data-lucide="check" class="h-3 w-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                </div>
                <span class="ml-3 text-sm font-bold text-slate-400 select-none group-hover:text-slate-300 transition-colors">Keep me signed in</span>
            </label>
        </div>

        @if(session('login.captcha_question'))
            <div>
                <label class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 mb-3 ml-1">
                    Verification: {{ session('login.captcha_question') }}
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                        <i data-lucide="shield-question" class="h-4.5 w-4.5"></i>
                    </div>
                    <input type="number" name="captcha_answer"
                        class="block w-full rounded-2xl border border-slate-700 bg-slate-800/50 py-4 pl-12 pr-4 text-sm text-white placeholder:text-slate-600 focus:border-indigo-500 focus:bg-slate-800 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none shadow-inner"
                        placeholder="Answer" required>
                </div>
                @error('captcha_answer')
                    <p class="mt-2 text-xs font-bold text-rose-400">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <x-button type="submit" variant="primary" size="lg" icon="log-in" class="w-full py-4 text-sm shadow-indigo-600/20">
            Sign Into Account
        </x-button>
    </form>

    <div class="mt-10 pt-8 border-t border-slate-800 text-center">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">
            Don't have an account? 
            <a href="{{ route('register') }}" class="ml-2 text-indigo-400 hover:text-indigo-300 transition-colors">Register Now</a>
        </p>
    </div>
</div>
@endsection
