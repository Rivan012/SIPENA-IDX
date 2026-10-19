<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivationLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $activationUrl,
        public ?string $userName = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aktivasi Akun SIPENA IDX Bengkulu',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.activation-link',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
