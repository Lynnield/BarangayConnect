<?php

namespace App\Services;

use App\Mail\MfaLoginCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FAQRCode\Google2FA;

class TwoFactorService
{
    public function __construct(
        protected Google2FA $google2fa,
        protected SmsService $smsService,
    ) {
    }

    public function google2fa(): Google2FA
    {
        return $this->google2fa;
    }

    /**
     * Plain recovery codes displayed once during enrollment.
     */
    public function plainRecoveryCodes(int $count = 8): array
    {
        $out = [];
        for ($i = 0; $i < $count; $i++) {
            $out[] = $this->randomRecoverySegment().'-'.$this->randomRecoverySegment();
        }

        return $out;
    }

    /**
     * @param  array<int, string>  $plainCodes
     */
    public function hashRecoveryCodes(array $plainCodes): array
    {
        return collect($plainCodes)
            ->map(fn (string $c) => password_hash($this->normalizeRecoveryCode($c), PASSWORD_BCRYPT))
            ->all();
    }

    public function normalizeRecoveryCode(string $code): string
    {
        return strtolower((string) preg_replace('/[^a-zA-Z0-9]/', '', $code));
    }

    /**
     * @return array{accepted: bool, remaining: array<int|string, mixed>}
     */
    public function tryConsumeRecoveryCode(User $user, string $input): array
    {
        $stored = $user->two_factor_recovery_codes ?? [];

        $normalizedInput = $this->normalizeRecoveryCode($input);

        foreach ($stored as $index => $hash) {
            if (password_verify($normalizedInput, $hash)) {
                unset($stored[$index]);

                $user->forceFill([
                    'two_factor_recovery_codes' => array_values($stored),
                ])->save();

                return ['accepted' => true, 'remaining' => $user->two_factor_recovery_codes ?? []];
            }
        }

        return ['accepted' => false, 'remaining' => $stored];
    }

    protected function randomRecoverySegment(int $chars = 4): string
    {
        $alphabet = 'abcdefghjkmnpqrstuvwxyz23456789';
        $s = '';

        for ($i = 0; $i < $chars; $i++) {
            $s .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $s;
    }

    public function generateAuthenticatorSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function verifyAuthenticatorCode(?string $secret, string $code): bool
    {
        if (! $secret) {
            return false;
        }

        return $this->google2fa->verifyKey($secret, preg_replace('/\s+/', '', $code));
    }

    public function qrCodeDataUrl(User $user, string $secret, int $size = 220): string
    {
        $issuer = trim((string) config('app.name', 'Barangay Connect'));

        return $this->google2fa->getQRCodeInline($issuer, (string) $user->email, $secret, $size);
    }

    public function sendLoginEmailCode(User $user): void
    {
        $plain = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $cacheKey = 'mfa_login_email_'.$user->id;
        Cache::put($cacheKey, Hash::make($plain), now()->addMinutes(10));

        Mail::to($user)->send(new MfaLoginCodeMail($plain));
        $this->smsService->send($user->phone, config('app.name') . " MFA code: {$plain}");
    }

    public function verifyLoginEmailCode(User $user, string $input): bool
    {
        return $this->consumeEmailChallengeHash('mfa_login_email_'.$user->id, $input);
    }

    public function sendEnrollmentEmailCode(User $user): void
    {
        $plain = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put('mfa_enroll_email_'.$user->id, Hash::make($plain), now()->addMinutes(15));

        Mail::to($user)->send(new MfaLoginCodeMail(
            code: $plain,
            subjectLine: __(':app — confirm MFA enrollment', ['app' => config('app.name')]),
        ));
    }

    public function verifyEnrollmentEmailCode(User $user, string $input): bool
    {
        return $this->consumeEmailChallengeHash('mfa_enroll_email_'.$user->id, $input);
    }

    public function sendDisableEmailChallenge(User $user): void
    {
        $plain = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put('mfa_disable_email_'.$user->id, Hash::make($plain), now()->addMinutes(15));

        Mail::to($user)->send(new MfaLoginCodeMail(
            code: $plain,
            subjectLine: __(':app — confirm MFA change', ['app' => config('app.name')]),
        ));
    }

    public function verifyDisableEmailChallenge(User $user, string $input): bool
    {
        return $this->consumeEmailChallengeHash('mfa_disable_email_'.$user->id, $input);
    }

    public function sendRecoveryRegenerationEmailChallenge(User $user): void
    {
        $plain = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put('mfa_recovery_regen_email_'.$user->id, Hash::make($plain), now()->addMinutes(15));

        Mail::to($user)->send(new MfaLoginCodeMail(
            code: $plain,
            subjectLine: __(':app — confirm recovery-code reset', ['app' => config('app.name')]),
        ));
    }

    public function verifyRecoveryRegenerationEmailChallenge(User $user, string $input): bool
    {
        return $this->consumeEmailChallengeHash('mfa_recovery_regen_email_'.$user->id, $input);
    }

    protected function consumeEmailChallengeHash(string $cacheKey, string $input): bool
    {
        $expected = Cache::get($cacheKey);

        if (! is_string($expected)) {
            return false;
        }

        if (! Hash::check(preg_replace('/\s+/', '', $input), $expected)) {
            return false;
        }

        Cache::forget($cacheKey);

        return true;
    }
}
