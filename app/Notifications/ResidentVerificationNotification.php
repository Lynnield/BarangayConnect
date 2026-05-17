<?php

namespace App\Notifications;

use App\Models\Resident;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ResidentVerificationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Resident $resident,
        public string $status,
        public ?string $notes = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'resident_id' => $this->resident->id,
            'title' => 'Resident profile ' . $this->status,
            'message' => $this->notes ?: 'Your resident profile verification is now ' . $this->status . '.',
            'link' => route('resident.profile.show'),
        ];
    }
}
