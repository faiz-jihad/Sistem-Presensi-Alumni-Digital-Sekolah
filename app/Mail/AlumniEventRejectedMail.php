<?php

namespace App\Mail;

use App\Models\AlumniEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlumniEventRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly AlumniEvent $event
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengajuan Kegiatan Alumni Ditolak',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alumni-event-rejected',
            with: [
                'event' => $this->event,
                'appUrl' => config('app.url'),
            ],
        );
    }
}
