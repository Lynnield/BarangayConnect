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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        $this->ensureCaptchaChallenge(request());

        return view('auth.login');
    }

    public function login(Request $request, TwoFactorService $twoFactor)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        if ($this->captchaRequired($request)) {
            $rules['captcha_answer'] = 'required|integer';
        }

        $request->validate($rules);

        if ($this->captchaRequired($request) && (int) $request->captcha_answer !== (int) $request->session()->get('login.captcha_answer')) {
            $this->rotateCaptcha($request);

            return back()->withErrors(['captcha_answer' => 'The verification answer is incorrect.'])->withInput($request->only('email'));
        }

        // Rate limiting
        $key = 'login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $user = User::where('email', $request->email)->first();

        // Check if user exists and account status
        if ($user) {
            if ($user->status === 'suspended') {
                $this->logLoginAttempt($request, $user, false, 'Account suspended');
                return back()->withErrors(['email' => 'Your account has been suspended. Contact the administrator.']);
            }

            if ($user->status === 'inactive') {
                $this->logLoginAttempt($request, $user, false, 'Account inactive');
                return back()->withErrors(['email' => 'Your account is inactive. Contact the administrator.']);
            }

            if ($user->isLocked()) {
                $this->logLoginAttempt($request, $user, false, 'Account locked');
                return back()->withErrors(['email' => 'Account locked due to too many failed attempts. Try again in 30 minutes.']);
            }
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::clear($key);

            /** @var User $user */
            $user = Auth::user();

            if ($user->hasMfaEnabled()) {
                $remember = $request->boolean('remember');
                Auth::logout();

                $request->session()->regenerate();

                $request->session()->put([
                    'login.mfa.user_id' => $user->id,
                    'login.mfa.remember' => $remember,
                ]);

                if ($user->usesEmailMfa()) {
                    $twoFactor->sendLoginEmailCode($user);
                }

                return redirect()->route('login.mfa')->with(
                    'status',
                    $user->usesEmailMfa()
                        ? __('We emailed you a verification code.')
                        : __('Enter the code from your authenticator app.'),
                );
            }

            $request->session()->regenerate();
            $request->session()->forget(['login.failed_count', 'login.captcha_question', 'login.captcha_answer']);
            $this->enforceSingleSession($request, $user);

            $user->update([
                'failed_login_attempts' => 0,
                'locked_until' => null,
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            $this->logLoginAttempt($request, $user, true);
            AuditService::log('Auth', 'login', null, null, "User logged in: {$user->email}");

            return redirect()->intended($this->getDashboardRoute($user));
        }

        RateLimiter::hit($key, 300);

        if ($user) {
            $user->incrementFailedLogins();
        }

        $request->session()->increment('login.failed_count');
        $this->ensureCaptchaChallenge($request);

        $this->logLoginAttempt($request, $user, false, 'Invalid credentials');

        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        AuditService::log('Auth', 'logout', null, null, 'User logged out');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $residentRole = \App\Models\Role::where('slug', 'resident')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'role_id' => $residentRole?->id,
            'status' => 'active',
        ]);

        AuditService::log('Auth', 'register', null, ['email' => $user->email], "New user registered: {$user->email}");

        Auth::login($user);
        return redirect()->route('resident.dashboard')->with('success', 'Welcome! Your account has been created.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = \Illuminate\Support\Facades\Password::sendResetLink($request->only('email'));

        return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('status', 'Password reset link sent to your email.')
            : back()->withErrors(['email' => trans($status)]);
    }

    public function showResetPassword(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password reset successfully. Please login.')
            : back()->withErrors(['email' => [trans($status)]]);
    }

    private function getDashboardRoute(User $user): string
    {
        return match($user->role?->slug) {
            'admin' => route('admin.dashboard'),
            'staff' => route('staff.dashboard'),
            default => route('resident.dashboard'),
        };
    }

    private function logLoginAttempt(Request $request, ?User $user, bool $success, ?string $reason = null): void
    {
        LoginHistory::create([
            'user_id' => $user?->id,
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'device_info' => $request->userAgent(),
            'success' => $success,
            'failure_reason' => $reason,
        ]);
    }

    private function captchaRequired(Request $request): bool
    {
        $threshold = SystemSetting::int('captcha_after_failed_attempts', 3);

        return $threshold > 0 && (int) $request->session()->get('login.failed_count', 0) >= $threshold;
    }

    private function ensureCaptchaChallenge(Request $request): void
    {
        if ($this->captchaRequired($request) && ! $request->session()->has('login.captcha_answer')) {
            $this->rotateCaptcha($request);
        }
    }

    private function rotateCaptcha(Request $request): void
    {
        $a = random_int(2, 9);
        $b = random_int(2, 9);

        $request->session()->put([
            'login.captcha_question' => "{$a} + {$b}",
            'login.captcha_answer' => $a + $b,
        ]);
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
            // Non-database session drivers do not have a sessions table to prune.
        }
    }
}
