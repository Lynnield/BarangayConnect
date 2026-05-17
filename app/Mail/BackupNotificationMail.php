<?php

namespace App\Mail;

use App\Models\Backup;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BackupNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Backup $backup,
        public bool $success,
        public ?string $errorMessage = null
    ) {
    }

    public function envelope(): Envelope
    {
        $status = $this->success ? 'Success' : 'Failed';
        return new Envelope(
            subject: config('app.name') . " - Backup $status: " . $this->backup->backup_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.backup-notification',
        );
    }
}
