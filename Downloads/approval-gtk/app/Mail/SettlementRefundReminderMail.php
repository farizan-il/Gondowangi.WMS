<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Settlement;
use App\Models\Pengajuan;
use App\Models\Karyawan;

class SettlementRefundReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $settlement;
    public $pengajuan;
    public $requester;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Settlement $settlement, Pengajuan $pengajuan, Karyawan $requester)
    {
        $this->settlement = $settlement;
        $this->pengajuan = $pengajuan;
        $this->requester = $requester;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Pengingat: Pengembalian Sisa Dana Settlement - ' . $this->pengajuan->nomor_pengajuan)
                    ->view('emails.settlement_refund_reminder');
    }
}