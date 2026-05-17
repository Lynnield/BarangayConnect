<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AuditService;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class MfaChallengeController extends Controller
{
    public function show(Request $request)
    {
        $user = $this->pendingUser($request);

        if (! $user || ! $user->hasMfaEnabled()) {
            return redirect()->route('login');
        }

        return view('auth.mfa-challenge', [
            'channel' => $user->two_factor_channel,
        ]);
    }

    public function verify(Request $request, TwoFactorService $twoFactor)
    {
        $user = $this->pendingUser($request);

        if (! $user || ! $user->hasMfaEnabled()) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => ['nullable', 'string'],
            'recovery_code' => ['nullable', 'string'],
        ]);

        if (! $request->filled('recovery_code') && ! $request->filled('code')) {
            throw ValidationException::withMessages([
                'code' => __('Enter your authenticator or email code, or use a recovery code.'),
            ]);
        }

        $ipKey = 'mfa.verify.'.$request->ip();

        if (RateLimiter::tooManyAttempts($ipKey, 10)) {
            throw ValidationException::withMessages([
                'code' => __('Too many verification attempts. Try again in :seconds seconds.', [
                    'seconds' => RateLimiter::availableIn($ipKey),
                ]),
            ]);
        }

        $ok = false;

        if ($request->filled('recovery_code')) {
            $result = $twoFactor->tryConsumeRecoveryCode($user, (string) $request->input('recovery_code'));
            $ok = $result['accepted'];
        } else {
            $code = (string) $request->input('code', '');

            if ($user->usesAuthenticatorMfa()) {
                $ok = $twoFactor->verifyAuthenticatorCode($user->two_factor_secret, $code);
            } elseif ($user->usesEmailMfa()) {
                $ok = $twoFactor->verifyLoginEmailCode($user, $code);
            }
        }

        if (! $ok) {
            RateLimiter::hit($ipKey, 60);

            throw ValidationException::withMessages([
                'code' => __('Invalid verification code.'),
            ]);
        }

        RateLimiter::clear($ipKey);

        $remember = (bool) $request->session()->pull('login.mfa.remember', false);
        $request->session()->forget('login.mfa.user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();
        $this->enforceSingleSession($request, $user);

        $user->refresh();
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        LoginHistory::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'ip_address' => $request->ip(),
            'device_info' => $request->userAgent(),
            'success' => true,
            'failure_reason' => null,
        ]);

        AuditService::log('Auth', 'login', null, null, 'User completed MFA login: '.$user->email);

        return redirect()->intended($this->dashboardRoute($user));
    }

    public function resendEmail(Request $request, TwoFactorService $twoFactor)
    {
        $user = $this->pendingUser($request);

        if (! $user || ! $user->usesEmailMfa()) {
            return redirect()->route('login');
        }

        $key = 'mfa.email.resend.'.$user->id;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors([
                'code' => __('Please wait before requesting another code.'),
            ]);
        }

        RateLimiter::hit($key, 60);

        $twoFactor->sendLoginEmailCode($user);

        return back()->with('status', __('A new code was sent to your email.'));
    }

    public function cancel(Request $request)
    {
        $request->session()->forget([
            'login.mfa.user_id',
            'login.mfa.remember',
        ]);

        return redirect()->route('login')->with('status', __('Sign-in cancelled. You can log in again.'));
    }

    protected function pendingUser(Request $request): ?User
    {
        $id = $request->session()->get('login.mfa.user_id');

        return $id ? User::query()->find($id) : null;
    }

    protected function dashboardRoute(User $user): string
    {
        return match ($user->role?->slug) {
            'admin' => route('admin.dashboard'),
            'staff' => route('staff.dashboard'),
            default => route('resident.dashboard'),
        };
    }

    private function enforceSingleSession(Request $request, User $user): void
    {
        if (! SystemSetting::bool('single_session_per_user', false)) {
            return;
        }

        try {
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        } catch (\Throwable) {
            //
        }
    }
}
