<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appointment_number', 'document_request_id', 'resident_id',
        'appointment_date', 'appointment_time', 'status', 'notes',
        'managed_by', 'reminder_sent_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'reminder_sent_at' => 'datetime',
    ];

    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function managedBy()
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'warning',
            'confirmed' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            'no_show' => 'secondary',
            default => 'secondary',
        };
    }

    protected static function booted(): void
    {
        static::creating(function ($appt) {
            if (!$appt->appointment_number) {
                $appt->appointment_number = 'APT-' . date('Y') . '-' . str_pad(
                    (static::whereYear('created_at', date('Y'))->count() + 1),
                    5, '0', STR_PAD_LEFT
                );
            }
        });
    }
}
