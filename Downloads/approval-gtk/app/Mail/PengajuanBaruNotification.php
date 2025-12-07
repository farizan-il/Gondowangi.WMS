<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Pengajuan;
use App\Models\Karyawan;

class PengajuanBaruNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuan;
    public $penerima;
    public $tipePenerima; // 'approver' atau 'atasan'

    /**
     * Create a new message instance.
     */
    public function __construct(Pengajuan $pengajuan, Karyawan $penerima, $tipePenerima = 'approver')
    {
        $this->pengajuan = $pengajuan;
        $this->penerima = $penerima;
        $this->tipePenerima = $tipePenerima;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Pengajuan Baru: ' . $this->pengajuan->nomor_pengajuan . ' - ' . $this->pengajuan->judul;
        
        return $this->subject($subject)
            ->view('emails.pengajuan-baru')
            ->with([
                'pengajuan' => $this->pengajuan,
                'penerima' => $this->penerima,
                'tipePenerima' => $this->tipePenerima,
                'appUrl' => config('app.url')
            ]);
    }
}