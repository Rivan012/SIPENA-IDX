<?php

namespace App\Mail;

use App\Models\Kegiatan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KegiatanReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Kegiatan $kegiatan,
        public ?string $recipientName = null,
        public ?string $customNote = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Pengingat Kegiatan] {$this->kegiatan->nama_kegiatan} — SIPENA IDX",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.kegiatan-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
