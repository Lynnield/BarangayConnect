<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public DocumentRequest $documentRequest,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Document request update: ' . $this->documentRequest->request_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your document request status has been updated.')
            ->line('Request: ' . $this->documentRequest->request_number)
            ->line('New status: ' . str_replace('_', ' ', ucfirst($this->newStatus)))
            ->action('View request', url('/resident/requests/' . $this->documentRequest->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'document_request_id' => $this->documentRequest->id,
            'request_number' => $this->documentRequest->request_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'title' => 'Request ' . $this->documentRequest->request_number . ' updated',
            'message' => 'Status changed to ' . str_replace('_', ' ', $this->newStatus),
        ];
    }
}
