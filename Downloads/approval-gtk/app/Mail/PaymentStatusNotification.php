<?php

// 1. MAIL CLASS - app/Mail/PaymentStatusNotification.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Pengajuan;
use App\Models\TransactionRequest;

class PaymentStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuan;
    public $transactionRequest;
    public $status;
    public $financeName;
    public $catatan;

    public function __construct($pengajuan, $transactionRequest, $status, $financeName, $catatan = null)
    {
        $this->pengajuan = $pengajuan;
        $this->transactionRequest = $transactionRequest;
        $this->status = $status;
        $this->financeName = $financeName;
        $this->catatan = $catatan;
    }

    public function build()
    {
        $statusText = [
            'paid' => 'Pembayaran Berhasil',
            'waiting' => 'Menunggu Pembayaran',
            'rejected' => 'Pembayaran Ditolak'
        ];

        $subject = match($this->status) {
            'paid' => '💰 Pembayaran Selesai - ' . $this->pengajuan->nomor_pengajuan,
            'rejected' => '⚠️ Pembayaran Ditolak - ' . $this->pengajuan->nomor_pengajuan,
            'waiting' => '⏳ Status Pembayaran - ' . $this->pengajuan->nomor_pengajuan,
            default => 'Update Status Pembayaran - ' . $this->pengajuan->nomor_pengajuan
        };

        return $this->view('emails.payment-status-notification')
                    ->subject($subject);
    }
}