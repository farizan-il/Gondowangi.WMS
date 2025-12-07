<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FinanceSettlementInterventionNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Notifikasi Intervensi Settlement oleh Finance - ' . $this->data['pengajuan']->judul_pengajuan)
            ->view('emails.finance_settlement_intervention')
            ->with([
                'pengajuan' => $this->data['pengajuan'],
                'settlement' => $this->data['settlement'],
                'interventions' => $this->data['interventions'],
                'finance_user' => $this->data['finance_user'],
                'catatan_intervensi' => $this->data['catatan_intervensi'],
                'total_actual_lama' => $this->data['total_actual_lama'],
                'total_actual_baru' => $this->data['total_actual_baru'],
                'selisih_lama' => $this->data['selisih_lama'],
                'selisih_baru' => $this->data['selisih_baru'],
                'total_items_changed' => $this->data['total_items_changed']
            ]);
    }
}