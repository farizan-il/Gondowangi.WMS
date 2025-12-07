<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FinanceInterventionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Intervensi Finance - Pengajuan ' . $this->data['pengajuan']->nomor_pengajuan)
                    ->view('emails.finance-intervention')
                    ->with($this->data);
    }
}