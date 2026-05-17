<?php

namespace App\Mail;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportDeliveryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Report $report
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Scheduled Report: ' . $this->report->report_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.report-delivery',
        );
    }

    public function attachments(): array
    {
        $path = storage_path('app/public/' . $this->report->file_path);
        
        if (!is_file($path)) {
            return [];
        }

        return [
            Attachment::fromPath($path)
                ->as($this->report->report_name . '.' . $this->report->file_format)
                ->withMime($this->report->file_format === 'pdf' ? 'application/pdf' : 'text/csv'),
        ];
    }
}
