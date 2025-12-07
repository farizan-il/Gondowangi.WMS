<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SettlementRefundStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    /**
     * Create a new message instance.
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = "Status Pengembalian Settlement - {$this->emailData['nomor_settlement']}";
        
        return $this->subject($subject)
                    ->view('emails.settlement_refund_status')
                    ->with([
                        'data' => $this->emailData
                    ]);
    }
}