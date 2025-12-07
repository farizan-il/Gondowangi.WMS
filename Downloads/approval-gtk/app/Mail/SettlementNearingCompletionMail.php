<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SettlementNearingCompletionMail extends Mailable
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
        $subject = "Settlement Mendekati Penyelesaian - {$this->emailData['nomor_settlement']}";
        
        return $this->subject($subject)
                    ->view('emails.settlement_nearing_completion')
                    ->with([
                        'data' => $this->emailData
                    ]);
    }
}