<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resident extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'resident_number', 'full_name', 'first_name', 'middle_name',
        'last_name', 'suffix', 'gender', 'birthdate', 'civil_status', 'address',
        'house_number', 'street', 'purok', 'barangay', 'city', 'province',
        'postal_code', 'contact_number', 'email', 'occupation', 'valid_id_type',
        'valid_id_number', 'is_active', 'notes', 'verification_status',
        'verified_by', 'verified_at',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getAgeAttribute(): int
    {
        return $this->birthdate->age;
    }

    public function getFullNameAttribute($value): string
    {
        $parts = array_filter([
            $this->attributes['first_name'] ?? null,
            $this->attributes['middle_name'] ?? null,
            $this->attributes['last_name'] ?? null,
            $this->attributes['suffix'] ?? null,
        ]);

        return $parts ? implode(' ', $parts) : (string) $value;
    }

    public function getStructuredAddressAttribute(): string
    {
        $parts = array_filter([
            $this->house_number,
            $this->street,
            $this->purok,
            $this->barangay,
            $this->city,
            $this->province,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    public function getActiveRequestsCountAttribute(): int
    {
        return $this->documentRequests()
            ->whereNotIn('status', ['released', 'cancelled', 'rejected'])
            ->count();
    }

    protected static function booted(): void
    {
        static::creating(function ($resident) {
            if (!$resident->resident_number) {
                $resident->resident_number = 'RES-' . date('Y') . '-' . str_pad(
                    (static::whereYear('created_at', date('Y'))->count() + 1),
                    5, '0', STR_PAD_LEFT
                );
            }
        });
    }
}
