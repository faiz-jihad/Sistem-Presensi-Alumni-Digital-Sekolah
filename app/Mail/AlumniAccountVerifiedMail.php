<?php

namespace App\Mail;

use App\Models\Alumni;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlumniAccountVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Alumni $alumni
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akun Alumni Anda Telah Diverifikasi',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alumni-account-verified',
            with: [
                'alumni' => $this->alumni,
                'loginUrl' => config('app.url'),
            ],
        );
    }
}
