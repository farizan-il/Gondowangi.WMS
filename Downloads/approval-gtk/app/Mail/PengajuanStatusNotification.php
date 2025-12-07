<?php

// 1. Mail Class - app/Mail/PengajuanStatusNotification.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Pengajuan;

class PengajuanStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuan;
    public $status;
    public $approverName;
    public $catatan;
    public $notificationType;

    public function __construct($pengajuan, $status, $approverName, $catatan = null, $notificationType = 'default')
    {
        $this->pengajuan = $pengajuan;
        $this->status = $status;
        $this->approverName = $approverName;
        $this->catatan = $catatan;
        $this->notificationType = $notificationType;
    }

    public function build()
    {
        $statusText = [
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision' => 'Perlu Revisi'
        ];

        // Smart subject based on notification type
        if ($this->notificationType === 'final_approved') {
            $subject = '🎉 Pengajuan ' . $this->pengajuan->nomor_pengajuan . ' - Disetujui Lengkap & Siap Diproses Finance';
        } else {
            $subject = 'Status Pengajuan ' . $this->pengajuan->nomor_pengajuan . ' - ' . $statusText[$this->status];
        }

        return $this->view('emails.pengajuan-status-notification')
                    ->subject($subject);
    }
}