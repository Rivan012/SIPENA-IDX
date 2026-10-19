<?php

namespace App\Mail;

use App\Models\Kegiatan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KegiatanStatusNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Kegiatan $kegiatan,
        public string $status,
        public string $judul,
        public string $pesan,
        public ?string $catatan = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[{$this->judul}] {$this->kegiatan->nama_kegiatan} — SIPENA IDX",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.kegiatan-status-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
