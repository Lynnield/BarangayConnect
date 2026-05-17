<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class SecurityController extends Controller
{
    public function index(Request $request, TwoFactorService $twoFactor)
    {
        $user = $request->user();

        /** @var string|null $pendingSecret */
        $pendingSecret = $request->session()->get('account.mfa_totp.secret');

        $totpQr = is_string($pendingSecret)
            ? $twoFactor->qrCodeDataUrl($user, $pendingSecret)
            : null;

        return view('account.security', [
            'user' => $user,
            'totpQr' => $totpQr,
        ]);
    }

    public function startTotpEnrollment(Request $request, TwoFactorService $twoFactor)
    {
        if ($request->user()->hasMfaEnabled()) {
            return redirect()->route('account.security.index');
        }

        $secret = $twoFactor->generateAuthenticatorSecret();

        session(['account.mfa_totp.secret' => $secret]);

        AuditService::log('Account', 'totp_setup_started', null, null, 'User started authenticator MFA setup');

        return back()->with('status', __('Scan the QR code and enter a code from your authenticator app to confirm.'));
    }

    public function cancelTotpEnrollment(Request $request)
    {
        $request->session()->forget('account.mfa_totp.secret');

        return back()->with('status', __('Authenticator setup cancelled.'));
    }

    public function confirmTotpEnrollment(Request $request, TwoFactorService $twoFactor)
    {
        if ($request->user()->hasMfaEnabled()) {
            return redirect()->route('account.security.index');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'min:6'],
        ]);

        /** @var string|null $pending */
        $pending = session('account.mfa_totp.secret');

        if (! $pending || ! $twoFactor->verifyAuthenticatorCode($pending, $validated['code'])) {
            return back()->withErrors(['code' => __('Invalid authenticator code. Try again.')]);
        }

        $plainRecoveryCodes = $twoFactor->plainRecoveryCodes();
        $user = $request->user()->fresh();

        $user->forceFill([
            'two_factor_secret' => $pending,
            'two_factor_channel' => 'app',
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $twoFactor->hashRecoveryCodes($plainRecoveryCodes),
        ])->save();

        session()->forget('account.mfa_totp.secret');

        AuditService::log('Account', 'mfa_enabled', null, ['channel' => 'app'], 'User enabled authenticator MFA');

        return redirect()
            ->route('account.security.index')
            ->with('recovery_codes_plain', $plainRecoveryCodes)
            ->with(
                'success',
                __('Authenticator MFA is enabled. Save your recovery codes now — they will not be shown again.')
            );
    }

    public function sendEnrollmentEmail(Request $request, TwoFactorService $twoFactor)
    {
        if ($request->user()->hasMfaEnabled()) {
            return redirect()->route('account.security.index');
        }

        $user = $request->user();

        $key = 'mfa.enrollment.email.send.'.$user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'email_code' => __('Please wait a moment before requesting another email code.'),
            ]);
        }

        RateLimiter::hit($key, 60);

        $twoFactor->sendEnrollmentEmailCode($user);

        AuditService::log('Account', 'mfa_email_enrollment_sent', null, null, 'User requested MFA enrollment email code');

        return back()->with('status', __('We emailed you a verification code.'));
    }

    public function confirmEnrollmentEmail(Request $request, TwoFactorService $twoFactor)
    {
        if ($request->user()->hasMfaEnabled()) {
            return redirect()->route('account.security.index');
        }

        $validated = $request->validate([
            'email_code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (! $twoFactor->verifyEnrollmentEmailCode($user, $validated['email_code'])) {
            return back()->withErrors(['email_code' => __('That code is invalid or expired.')]);
        }

        $plainRecoveryCodes = $twoFactor->plainRecoveryCodes();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_channel' => 'email',
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $twoFactor->hashRecoveryCodes($plainRecoveryCodes),
        ])->save();

        AuditService::log('Account', 'mfa_enabled', null, ['channel' => 'email'], 'User enabled email OTP MFA');

        return redirect()
            ->route('account.security.index')
            ->with('recovery_codes_plain', $plainRecoveryCodes)
            ->with(
                'success',
                __('Email MFA is enabled. Save your recovery codes now — they will not be shown again.')
            );
    }

    public function sendDisableEmailCode(Request $request, TwoFactorService $twoFactor)
    {
        $request->validate([
            'disable_email_password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->string('disable_email_password')->toString(), (string) $user->password)) {
            return back()->withErrors(['disable_email_password' => __('Incorrect password.')]);
        }

        $key = 'mfa.disable.email.send.'.$user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'disable_email_password' => __('Please wait before requesting another code.'),
            ]);
        }

        RateLimiter::hit($key, 60);

        $twoFactor->sendDisableEmailChallenge($user);

        return back()->with('status', __('We emailed you a code to disable MFA.'));
    }

    public function disable(Request $request, TwoFactorService $twoFactor)
    {
        $user = $request->user();

        if (! $user->hasMfaEnabled()) {
            return redirect()->route('account.security.index');
        }

        $request->validate([
            'disable_password' => ['required', 'string'],
            'disable_totp_code' => ['nullable', 'string'],
            'disable_recovery_code' => ['nullable', 'string'],
            'disable_email_code' => ['nullable', 'digits:6'],
        ]);

        if (! Hash::check($request->string('disable_password')->toString(), (string) $user->password)) {
            return back()->withErrors(['disable_password' => __('Incorrect password.')]);
        }

        $verifiedSecondFactor = false;

        if ($request->filled('disable_recovery_code')) {
            $consume = $twoFactor->tryConsumeRecoveryCode($user, (string) $request->input('disable_recovery_code'));
            $verifiedSecondFactor = $consume['accepted'];
            $user->refresh();
        } elseif ($user->usesAuthenticatorMfa() && $request->filled('disable_totp_code')) {
            $verifiedSecondFactor = $twoFactor->verifyAuthenticatorCode(
                $user->two_factor_secret,
                (string) $request->input('disable_totp_code'),
            );
        } elseif ($user->usesEmailMfa() && $request->filled('disable_email_code')) {
            $verifiedSecondFactor = $twoFactor->verifyDisableEmailChallenge(
                $user,
                (string) $request->input('disable_email_code'),
            );
        }

        if (! $verifiedSecondFactor) {
            return back()->withErrors([
                'disable_factor' => __('Verify MFA using your primary code or a recovery code (or email code if applicable).'),
            ]);
        }

        $user->clearMfa();

        AuditService::log('Account', 'mfa_disabled', null, null, 'User disabled MFA');

        return redirect()
            ->route('account.security.index')
            ->with('success', __('Multi-factor authentication has been disabled.'));
    }

    public function sendRecoveryRegenerationEmail(Request $request, TwoFactorService $twoFactor)
    {
        $request->validate([
            'recovery_email_password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! $user->hasMfaEnabled() || ! $user->usesEmailMfa()) {
            return redirect()->route('account.security.index');
        }

        if (! Hash::check($request->string('recovery_email_password')->toString(), (string) $user->password)) {
            return back()->withErrors(['recovery_email_password' => __('Incorrect password.')]);
        }

        $key = 'mfa.recovery.email.send.'.$user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'recovery_email_password' => __('Please wait before requesting another code.'),
            ]);
        }

        RateLimiter::hit($key, 60);

        $twoFactor->sendRecoveryRegenerationEmailChallenge($user);

        return back()->with('status', __('We emailed you a code to reset recovery codes.'));
    }

    public function regenerateRecoveryCodes(Request $request, TwoFactorService $twoFactor)
    {
        $user = $request->user();

        if (! $user->hasMfaEnabled()) {
            return redirect()->route('account.security.index');
        }

        $request->validate([
            'recovery_password' => ['required', 'string'],
        ]);

        if (! Hash::check($request->string('recovery_password')->toString(), (string) $user->password)) {
            return back()->withErrors(['recovery_password' => __('Incorrect password.')]);
        }

        $verified = false;

        if ($user->usesAuthenticatorMfa()) {
            $validated = $request->validate([
                'recovery_totp_code' => ['required', 'string', 'min:6'],
            ]);

            $verified = $twoFactor->verifyAuthenticatorCode(
                $user->two_factor_secret,
                (string) $validated['recovery_totp_code'],
            );
        } elseif ($user->usesEmailMfa()) {
            $validated = $request->validate([
                'recovery_email_code' => ['required', 'digits:6'],
            ]);

            $verified = $twoFactor->verifyRecoveryRegenerationEmailChallenge(
                $user,
                (string) $validated['recovery_email_code'],
            );
        }

        if (! $verified) {
            return back()->withErrors(['recovery_verify' => __('Invalid verification code.')]);
        }

        $plainRecoveryCodes = $twoFactor->plainRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => $twoFactor->hashRecoveryCodes($plainRecoveryCodes),
        ])->save();

        AuditService::log('Account', 'recovery_codes_reset', null, null, 'User regenerated MFA recovery codes');

        return redirect()
            ->route('account.security.index')
            ->with('recovery_codes_plain', $plainRecoveryCodes)
            ->with('success', __('Recovery codes regenerated. Save them now — older codes no longer work.'));
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        AuditService::log('Account', 'password_changed', null, null, 'User changed password from security settings');

        return back()->with('success', __('Password updated.'));
    }
}
