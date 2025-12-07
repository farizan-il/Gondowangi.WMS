<?php

namespace App\Services;

use App\Mail\PengajuanStatusNotification;
use App\Models\EmailNotificationLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    public function sendStatusUpdateNotification($pengajuan, $status, $approverName, $catatan = null)
    {
        try {
            // Kirim email ke requester
            $requester = $pengajuan->requester;
            
            if (!$requester || !$requester->email) {
                throw new \Exception('Email requester tidak ditemukan');
            }

            // Determine notification type based on approval flow
            $notificationType = $this->determineNotificationType($pengajuan, $status);

            Mail::to($requester->email)->send(
                new PengajuanStatusNotification($pengajuan, $status, $approverName, $catatan, $notificationType)
            );

            // Log email berhasil dikirim
            $this->logEmailNotification($pengajuan->id, $requester->id, $requester->email, 'sent', 'Email berhasil dikirim');

            return true;

        } catch (\Exception $e) {
            Log::error('Error sending email notification: ' . $e->getMessage());
            
            // Log email gagal dikirim
            $this->logEmailNotification(
                $pengajuan->id,
                $requester->id ?? null,
                $requester->email ?? 'unknown',
                'failed',
                'Gagal mengirim email',
                ['error' => $e->getMessage()]
            );

            return false;
        }
    }

    /**
     * Determine notification type based on approval flow
     */
    private function determineNotificationType($pengajuan, $status)
    {
        if ($status === 'approved') {
            // Check if this is the final approval (all layers completed)
            if ($pengajuan->current_step >= $pengajuan->total_step) {
                return 'final_approved'; // Final approval - ready for finance processing
            } else {
                return 'partial_approved'; // Approved but more steps needed
            }
        } elseif ($status === 'rejected') {
            return 'rejected';
        } elseif ($status === 'revision') {
            return 'revision';
        }

        return 'default';
    }

    private function logEmailNotification($pengajuanId, $recipientId, $recipientEmail, $status, $message, $errorDetails = null)
    {
        EmailNotificationLog::create([
            'pengajuan_id' => $pengajuanId,
            'recipient_id' => $recipientId,
            'recipient_email' => $recipientEmail,
            'status' => $status,
            'message' => $message,
            'sent_at' => $status === 'sent' ? now() : null,
            'error_details' => $errorDetails
        ]);
    }
}