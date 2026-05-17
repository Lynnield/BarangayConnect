@extends('layouts.app')

@section('title', 'Security')

@section('content')
<div class="max-w-screen-lg mx-auto space-y-10">
    <div>
        <h1 class="text-2xl font-black text-slate-900">{{ __('Security') }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ __('Manage MFA, recovery codes, and your password.') }}</p>
    </div>

    @if(session('recovery_codes_plain') && is_array(session('recovery_codes_plain')))
        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-6">
            <h2 class="text-sm font-black text-amber-900 uppercase tracking-widest">{{ __('Save your recovery codes') }}</h2>
            <p class="mt-2 text-xs text-amber-900/80 leading-relaxed mb-4">
                {{ __('Each code works once. Store them offline (password manager / print). These will not be shown again.') }}
            </p>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach(session('recovery_codes_plain') as $code)
                    <li class="font-mono text-sm px-4 py-2 rounded-xl bg-white border border-amber-100 text-slate-900">{{ $code }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- MFA status --}}
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-100 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-black text-slate-900">{{ __('Multi-factor authentication (MFA)') }}</h2>
                <p class="mt-2 text-xs text-slate-500">{{ __('Adds a second step after your password.') }}</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-black uppercase tracking-wider {{ $user->hasMfaEnabled() ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-slate-50 text-slate-600 ring-1 ring-slate-100' }}">
                {{ $user->hasMfaEnabled() ? __('Enabled') : __('Not enabled') }}
            </span>
        </div>

        <div class="p-8 space-y-10">
            @if(! $user->hasMfaEnabled())
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <div class="space-y-6">
                        <h3 class="text-sm font-black text-slate-900">{{ __('Authenticator app (recommended)') }}</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ __('Works offline. Supports Google Authenticator, Authy, 1Password, etc.') }}</p>

                        @if($totpQr)
                            <div class="flex flex-col sm:flex-row items-start gap-6">
                                <img src="{{ $totpQr }}" alt="Authenticator QR code" class="h-44 w-44 rounded-2xl border border-slate-100 bg-white p-2">
                                <form method="POST" action="{{ route('account.security.totp.confirm') }}" class="w-full space-y-4">
                                    @csrf
                                    <label class="block text-xs font-bold text-slate-700">{{ __('Enter a 6-digit code to confirm') }}</label>
                                    <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-4 text-sm tracking-widest text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none"
                                        required>
                                    <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-black uppercase tracking-wider text-white hover:bg-indigo-500 transition-colors">{{ __('Activate authenticator MFA') }}</button>
                                </form>
                            </div>

                            <form method="POST" action="{{ route('account.security.totp.cancel') }}">
                                @csrf
                                <button class="text-xs font-bold text-slate-400 hover:text-rose-600 transition-colors">{{ __('Cancel setup') }}</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('account.security.totp.start') }}">
                                @csrf
                                <button class="rounded-xl bg-slate-900 px-6 py-3 text-xs font-black uppercase tracking-wider text-white hover:bg-slate-800 transition-colors">{{ __('Set up authenticator') }}</button>
                            </form>
                        @endif
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-sm font-black text-slate-900">{{ __('Email OTP') }}</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ __('We send a short code to :email.', ['email' => $user->email]) }}</p>

                        <form method="POST" action="{{ route('account.security.email.send') }}" class="space-y-3">
                            @csrf
                            <button type="submit" class="rounded-xl bg-white border border-slate-200 px-6 py-3 text-xs font-black uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition-colors">{{ __('Send enrollment code') }}</button>
                        </form>

                        <form method="POST" action="{{ route('account.security.email.confirm') }}" class="space-y-4 pt-6 border-t border-slate-100">
                            @csrf
                            <label class="block text-xs font-bold text-slate-700">{{ __('Enter the 6-digit email code') }}</label>
                            <input type="text" name="email_code" inputmode="numeric"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-4 text-sm tracking-widest text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none"
                                required>
                            <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-black uppercase tracking-wider text-white hover:bg-indigo-500 transition-colors">{{ __('Activate email MFA') }}</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="text-sm text-slate-600 space-y-2">
                    <p class="font-bold text-slate-900">{{ __('Method') }}:
                        {{ $user->usesAuthenticatorMfa() ? __('Authenticator app') : __('Email OTP') }}
                    </p>
                    @if(is_array($user->two_factor_recovery_codes))
                        <p class="text-xs text-slate-500">{{ __('Recovery codes remaining: :count', ['count' => count($user->two_factor_recovery_codes)]) }}</p>
                    @endif
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 pt-10 border-t border-slate-100">
                    <div class="space-y-4">
                        <h3 class="text-sm font-black text-slate-900">{{ __('Regenerate recovery codes') }}</h3>
                        @if($user->usesAuthenticatorMfa())
                            <form method="POST" action="{{ route('account.security.recovery.regenerate') }}" class="space-y-4">
                                @csrf
                                <input type="password" name="recovery_password" placeholder="{{ __('Current password') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-4 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none" required autocomplete="current-password">
                                <input type="text" name="recovery_totp_code" placeholder="{{ __('Authenticator code') }}" inputmode="numeric" autocomplete="one-time-code"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-4 text-sm tracking-widest text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none" required>
                                <button class="rounded-xl bg-slate-900 px-6 py-3 text-xs font-black uppercase tracking-wider text-white hover:bg-slate-800 transition-colors">{{ __('Generate new codes') }}</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('account.security.recovery.email') }}" class="space-y-3">
                                @csrf
                                <input type="password" name="recovery_email_password" placeholder="{{ __('Current password') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-4 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none" required autocomplete="current-password">
                                <button class="rounded-xl bg-white border border-slate-200 px-6 py-3 text-xs font-black uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition-colors">{{ __('Email me a verification code') }}</button>
                            </form>

                            <form method="POST" action="{{ route('account.security.recovery.regenerate') }}" class="space-y-4 pt-6 border-t border-slate-100">
                                @csrf
                                <input type="password" name="recovery_password" placeholder="{{ __('Current password') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-4 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none" required autocomplete="current-password">
                                <input type="text" name="recovery_email_code" placeholder="{{ __('Email code') }}" inputmode="numeric"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-4 text-sm tracking-widest text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none" required>
                                <button class="rounded-xl bg-slate-900 px-6 py-3 text-xs font-black uppercase tracking-wider text-white hover:bg-slate-800 transition-colors">{{ __('Generate new codes') }}</button>
                            </form>
                        @endif
                    </div>

                    <div class="rounded-3xl bg-rose-50 border border-rose-100 p-7 space-y-5">
                        <h3 class="text-sm font-black text-rose-900">{{ __('Disable MFA') }}</h3>

                        @if($user->usesEmailMfa())
                            <form method="POST" action="{{ route('account.security.disable.email') }}" class="space-y-4">
                                @csrf
                                <input type="password" name="disable_email_password" placeholder="{{ __('Current password') }}"
                                    class="w-full rounded-xl border border-rose-100 bg-white py-3 px-4 text-sm text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none" required autocomplete="current-password">
                                <button class="rounded-xl bg-white px-6 py-3 text-xs font-black uppercase tracking-wider text-slate-800 border border-slate-200 hover:bg-slate-50 transition-colors">{{ __('Email me a disable code') }}</button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('account.security.disable') }}" class="space-y-4 pt-6 border-t border-rose-100">
                            @csrf
                            <input type="password" name="disable_password" placeholder="{{ __('Current password') }}"
                                class="w-full rounded-xl border border-rose-100 bg-white py-3 px-4 text-sm text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none" required autocomplete="current-password">

                            @if($user->usesAuthenticatorMfa())
                                <input type="text" name="disable_totp_code" placeholder="{{ __('Authenticator code') }}" inputmode="numeric" autocomplete="one-time-code"
                                    class="w-full rounded-xl border border-rose-100 bg-white py-3 px-4 text-sm tracking-widest text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                            @elseif($user->usesEmailMfa())
                                <input type="text" name="disable_email_code" placeholder="{{ __('Email disable code') }}" inputmode="numeric"
                                    class="w-full rounded-xl border border-rose-100 bg-white py-3 px-4 text-sm tracking-widest text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                            @endif

                            <input type="text" name="disable_recovery_code" placeholder="{{ __('Or use recovery code') }}"
                                class="w-full rounded-xl border border-rose-100 bg-white py-3 px-4 text-sm text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none">

                            <button class="rounded-xl bg-rose-600 px-6 py-3 text-xs font-black uppercase tracking-wider text-white hover:bg-rose-500 transition-colors">{{ __('Disable MFA') }}</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-100">
            <h2 class="text-lg font-black text-slate-900">{{ __('Change password') }}</h2>
        </div>

        <form method="POST" action="{{ route('account.security.password') }}" class="p-8 space-y-4 max-w-xl">
            @csrf

            <div>
                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">{{ __('Current password') }}</label>
                <input type="password" name="current_password"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-4 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none" required autocomplete="current-password">
            </div>

            <div>
                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">{{ __('New password') }}</label>
                <input type="password" name="password"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-4 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none" required autocomplete="new-password">
            </div>

            <div>
                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">{{ __('Confirm password') }}</label>
                <input type="password" name="password_confirmation"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-4 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none" required autocomplete="new-password">
            </div>

            <button class="rounded-xl bg-indigo-600 px-6 py-3 text-xs font-black uppercase tracking-wider text-white hover:bg-indigo-500 transition-colors">{{ __('Update password') }}</button>
        </form>
    </div>
</div>
@endsection
