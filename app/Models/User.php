<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'role_id', 'status',
        'phone', 'address', 'failed_login_attempts', 'locked_until',
        'last_login_at', 'last_login_ip',
        'two_factor_secret', 'two_factor_confirmed_at', 'two_factor_channel', 'two_factor_recovery_codes',
    ];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'two_factor_secret' => 'encrypted',
        'two_factor_recovery_codes' => 'encrypted:array',
    ];

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function resident()
    {
        return $this->hasOne(Resident::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function activityFeeds()
    {
        return $this->hasMany(ActivityFeed::class);
    }

    // Role helpers
    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role?->slug === 'staff';
    }

    public function isResident(): bool
    {
        return $this->role?->slug === 'resident';
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->slug === $role;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) return true;
        return $this->role?->permissions()->where('slug', $permission)->exists() ?? false;
    }

    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1a56db&color=fff';
    }

    public function incrementFailedLogins(): void
    {
        $this->increment('failed_login_attempts');
        if ($this->failed_login_attempts >= 5) {
            $this->update(['locked_until' => now()->addMinutes(30)]);
        }
    }

    public function resetFailedLogins(): void
    {
        $this->update(['failed_login_attempts' => 0, 'locked_until' => null]);
    }

    public function hasMfaEnabled(): bool
    {
        return $this->two_factor_confirmed_at !== null
            && $this->two_factor_channel !== null;
    }

    public function usesAuthenticatorMfa(): bool
    {
        return $this->hasMfaEnabled() && $this->two_factor_channel === 'app';
    }

    public function usesEmailMfa(): bool
    {
        return $this->hasMfaEnabled() && $this->two_factor_channel === 'email';
    }

    public function clearMfa(): void
    {
        $this->forceFill([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_channel' => null,
            'two_factor_recovery_codes' => null,
        ])->save();
    }
}
