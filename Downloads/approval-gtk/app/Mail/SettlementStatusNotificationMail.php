<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Mail\Mailable;

use Illuminate\Mail\Mailables\Content;

use Illuminate\Mail\Mailables\Envelope;

use Illuminate\Queue\SerializesModels;

class SettlementStatusNotificationMail extends Mailable implements ShouldQueue

{

    use Queueable, SerializesModels;

    public $settlement;

    public $transactionRequest;

    public $status;

    public $financeName;

    public $catatan;

    /**

     * Create a new message instance.

     */

    public function __construct($settlement, $transactionRequest, $status, $financeName, $catatan = null)

    {

        $this->settlement = $settlement;

        $this->transactionRequest = $transactionRequest;

        $this->status = $status;

        $this->financeName = $financeName;

        $this->catatan = $catatan;

    }

    /**

     * Get the message envelope.

     */

    public function envelope(): Envelope

    {

        $statusText = $this->getStatusText();

        

        return new Envelope(

            subject: "Update Status Settlement - {$this->settlement->nomor_settlement} ({$statusText})",

        );

    }

    /**

     * Get the message content definition.

     */

    public function content(): Content

    {

        return new Content(

            view: 'emails.settlement-status-notification',

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

    private function getStatusText()

    {

        return match($this->status) {

            'paid' => 'Dibayarkan',

            'proses' => 'Sedang Diproses',

            'rejected' => 'Ditolak',

            default => 'Status Tidak Dikenal'

        };

    }

}