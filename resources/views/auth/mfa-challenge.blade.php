@extends('layouts.guest')

@section('title', 'Verify login')

@section('container_width', 'max-w-lg')

@section('content')
<div class="text-center mb-8">
    <h1 class="text-3xl font-black text-slate-900 tracking-tight">{{ __('Verify it’s you') }}</h1>
    <p class="text-slate-500 mt-2 font-medium">
        @if($channel === 'email')
            {{ __('Enter the verification code sent to your email.') }}
        @else
            {{ __('Enter the 6‑digit code from your authenticator app.') }}
        @endif
    </p>
</div>

<div class="rounded-3xl bg-white p-8 shadow-xl shadow-slate-200/50 border border-slate-100">
    @if(session('status'))
        <div class="mb-6 rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm font-medium text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ url('/login/mfa') }}" class="space-y-6">
        @csrf

        <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-xs text-slate-600 leading-relaxed">
            {{ __('You can sign in using a recovery code instead (this consumes one code). Leave the primary code empty if you use a recovery code.') }}
        </div>

        @if($channel === 'email')
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">{{ __('Email verification code') }}</label>
                <input type="text" inputmode="numeric" pattern="\d*" name="code" value="{{ old('code') }}" autocomplete="one-time-code"
                    class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-4 text-sm tracking-widest text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                    placeholder="123456">
            </div>
        @else
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">{{ __('Authenticator code') }}</label>
                <input type="text" inputmode="numeric" pattern="\d*" name="code" value="{{ old('code') }}" autocomplete="one-time-code"
                    class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-4 text-sm tracking-widest text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                    placeholder="123456">
            </div>
        @endif

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">{{ __('Recovery code') }} <span class="text-slate-400 font-semibold">{{ __('(optional)') }}</span></label>
            <input type="text" name="recovery_code" value="{{ old('recovery_code') }}"
                class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                placeholder="{{ __('abcd-1234') }}">
        </div>

        <button type="submit" class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-500 hover:translate-y-[-1px] active:translate-y-[0px] transition-all">
            {{ __('Continue') }}
        </button>
    </form>

    @if($channel === 'email')
        <form method="POST" action="{{ url('/login/mfa/resend-email') }}" class="mt-6">
            @csrf
            <button type="submit" class="w-full rounded-xl border border-slate-200 bg-white py-3 text-xs font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 transition-colors">
                {{ __('Resend email code') }}
            </button>
        </form>
    @endif

    <div class="mt-8 pt-6 border-t border-slate-100 text-center space-y-3">
        <form method="POST" action="{{ route('login.mfa.cancel') }}">
            @csrf
            <button type="submit" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-rose-600 transition-colors">{{ __('Cancel and use a different account') }}</button>
        </form>

        <a href="{{ route('login') }}" class="inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-500">{{ __('Back to login') }}</a>
    </div>
</div>
@endsection
