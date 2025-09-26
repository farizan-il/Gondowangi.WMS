<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Karyawan;

class KandidatLamaranMail extends Mailable
{
    use Queueable, SerializesModels;

    public $kandidat;
    public $posisi;

    /**
     * Create a new message instance.
     */
    public function __construct(Karyawan $kandidat)
    {
        $this->kandidat = $kandidat;
        $this->posisi = $kandidat->posisilamaran;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Penerimaan Lamaran - ' . ($this->posisi->title ?? 'Posisi'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.kandidat-lamaran',
            with: [
                'kandidatNama' => $this->kandidat->nama,
                'posisiNama' => $this->posisi->title ?? 'Posisi yang dilamar',
                'tanggalLamaran' => $this->kandidat->created_at->format('d F Y'),
                'statusLamaran' => $this->kandidat->status
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}