<?php

namespace App\Mail;

use App\Models\JobVacancy;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobVacancyApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly JobVacancy $job
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lowongan Kerja Anda Telah Disetujui',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job-vacancy-approved',
            with: [
                'job' => $this->job,
                'appUrl' => config('app.url'),
            ],
        );
    }
}
