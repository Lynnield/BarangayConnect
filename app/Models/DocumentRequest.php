<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentRequest extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_FOR_REVISION = 'for_revision';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_READY = 'ready_for_pickup';
    const STATUS_RELEASED = 'released';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'request_number', 'resident_id', 'document_type_id', 'status', 'purpose',
        'form_data', 'remarks', 'rejection_reason', 'revision_notes', 'processed_by',
        'approved_by', 'processed_at', 'approved_at', 'released_at', 'pdf_path',
        'fee_amount', 'fee_paid', 'fee_paid_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'processed_at' => 'datetime',
        'approved_at' => 'datetime',
        'released_at' => 'datetime',
        'fee_paid_at' => 'datetime',
        'fee_amount' => 'decimal:2',
        'fee_paid' => 'boolean',
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function attachments()
    {
        return $this->hasMany(RequestAttachment::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(RequestStatusLog::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'under_review' => 'info',
            'for_revision' => 'secondary',
            'approved' => 'success',
            'rejected' => 'danger',
            'ready_for_pickup' => 'primary',
            'released' => 'dark',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'under_review' => 'Under Review',
            'for_revision' => 'For Revision',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'ready_for_pickup' => 'Ready for Pickup',
            'released' => 'Released',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_UNDER_REVIEW]);
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_UNDER_REVIEW]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_UNDER_REVIEW, self::STATUS_FOR_REVISION]);
    }

    protected static function booted(): void
    {
        static::creating(function ($request) {
            if (!$request->request_number) {
                $request->request_number = 'REQ-' . date('Y') . '-' . str_pad(
                    (static::whereYear('created_at', date('Y'))->count() + 1),
                    6, '0', STR_PAD_LEFT
                );
            }
        });
    }
}
