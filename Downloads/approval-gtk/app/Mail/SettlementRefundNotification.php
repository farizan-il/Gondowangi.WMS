<?php

// File: app/Mail/SettlementRefundNotification.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Settlement;

class SettlementRefundNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $settlement;
    public $additionalMessage;

    public function __construct(Settlement $settlement, $additionalMessage = '')
    {
        $this->settlement = $settlement;
        $this->additionalMessage = $additionalMessage;
    }

    public function build()
    {
        $refundAmount = abs($this->settlement->selisih);
        $currency = $this->settlement->pengajuan->mata_uang;
        
        return $this->subject('Notifikasi Pengembalian Dana Settlement - ' . $this->settlement->nomor_settlement)
            ->view('emails.settlement-refund-notification')
            ->with([
                'settlement' => $this->settlement,
                'requesterName' => $this->settlement->pengajuan->requester->nama,
                'settlementNumber' => $this->settlement->nomor_settlement,
                'pengajuanNumber' => $this->settlement->pengajuan->nomor_pengajuan,
                'refundAmount' => number_format($refundAmount, 0, ',', '.'),
                'currency' => $currency,
                'additionalMessage' => $this->additionalMessage,
                'kategori' => $this->settlement->pengajuan->kategoriPengajuan->nama
            ]);
    }
}