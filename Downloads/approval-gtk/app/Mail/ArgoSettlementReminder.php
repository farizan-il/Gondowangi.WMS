<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Pengajuan;

class ArgoSettlementReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuan;
    public $remainingDays;

    public function __construct(Pengajuan $pengajuan)
    {
        $this->pengajuan = $pengajuan;
        $this->remainingDays = $pengajuan->getRemainingArgoDays();
    }

    public function build()
    {
        $subject = "Urgent: Settlement Required - {$this->pengajuan->nomor_pengajuan}";
        
        if ($this->remainingDays <= 0) {
            $subject = "OVERDUE: Settlement Required - {$this->pengajuan->nomor_pengajuan}";
        } elseif ($this->remainingDays <= 2) {
            $subject = "URGENT: Settlement Required in {$this->remainingDays} day(s) - {$this->pengajuan->nomor_pengajuan}";
        }

        return $this->subject($subject)
                    ->view('emails.argo-settlement-reminder')
                    ->with([
                        'pengajuan' => $this->pengajuan,
                        'remainingDays' => $this->remainingDays,
                        'requesterName' => $this->pengajuan->requester->nama,
                        'kategori' => $this->pengajuan->kategoriPengajuan->nama ?? 'N/A',
                        'nominal' => number_format($this->pengajuan->nominal_pengajuan, 0, ',', '.'),
                        'argoDate' => $this->pengajuan->argo ? \Carbon\Carbon::parse($this->pengajuan->argo)->format('d/m/Y') : 'N/A'
                    ]);
    }
}