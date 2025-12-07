<?php 
namespace App\Services;
use App\Mail\PaymentStatusNotification;
use App\Mail\SettlementStatusNotificationMail;
use App\Models\EmailNotificationLog;
use App\Models\TransactionRequest;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FinancePaymentService
{
    /**
     * Send payment status notification to requester
     */
    public function sendPaymentStatusNotification($pengajuan, $transactionRequest, $status, $financeName, $catatan = null)
    {
        try {
            $requester = $pengajuan->requester;
            
            if (!$requester || !$requester->email) {
                throw new \Exception('Email requester tidak ditemukan');
            }

            Mail::to($requester->email)->send(
                new PaymentStatusNotification($pengajuan, $transactionRequest, $status, $financeName, $catatan)
            );

            // Log email berhasil dikirim
            $this->logEmailNotification(
                $pengajuan->id, 
                $requester->id, 
                $requester->email, 
                'sent', 
                "Transfer dana untuk pengajuan Anda telah selesai dilakukan. Silakan cek rekening Anda",
                'payment'
            );

            return true;

        } catch (\Exception $e) {
            Log::error('Error sending payment status notification: ' . $e->getMessage(), [
                'pengajuan_id' => $pengajuan->id,
                'status' => $status,
                'finance_name' => $financeName
            ]);
            
            // Log email gagal dikirim
            $this->logEmailNotification(
                $pengajuan->id,
                $requester->id ?? null,
                $requester->email ?? 'unknown',
                'failed',
                'Gagal mengirim notifikasi pembayaran',
                'payment',
                ['error' => $e->getMessage()]
            );

            return false;
        }
    }

    /**
     * Send settlement status notification to requester
     */
    public function sendSettlementStatusNotification($settlement, $transactionRequest, $status, $financeName, $catatan = null)
    {
        try {
            $requester = $settlement->pengajuan->requester;
            
            if (!$requester || !$requester->email) {
                throw new \Exception('Email requester tidak ditemukan');
            }

            Mail::to($requester->email)->send(
                new SettlementStatusNotificationMail($settlement, $transactionRequest, $status, $financeName, $catatan)
            );

            // Log email berhasil dikirim
            $this->logEmailNotification(
                $settlement->pengajuan_id, 
                $requester->id, 
                $requester->email, 
                'sent', 
                "Status settlement untuk pengajuan Anda telah diperbarui menjadi: " . $this->getPaymentStatusText($status),
                'settlement'
            );

            return true;

        } catch (\Exception $e) {
            Log::error('Error sending settlement status notification: ' . $e->getMessage(), [
                'settlement_id' => $settlement->id,
                'pengajuan_id' => $settlement->pengajuan_id,
                'status' => $status,
                'finance_name' => $financeName
            ]);
            
            // Log email gagal dikirim
            $this->logEmailNotification(
                $settlement->pengajuan_id,
                $requester->id ?? null,
                $requester->email ?? 'unknown',
                'failed',
                'Gagal mengirim notifikasi settlement',
                'settlement',
                ['error' => $e->getMessage()]
            );

            return false;
        }
    }

    /**
     * Get payment status display text
     */
    public function getPaymentStatusText($status)
    {
        return match($status) {
            'paid' => 'Dibayarkan',
            'proses' => 'Sedang Diproses',
            'waiting' => 'Menunggu Pembayaran',
            'rejected' => 'Ditolak',
            default => 'Status Tidak Dikenal'
        };
    }

    /**
     * Get payment status color for UI
     */
    public function getPaymentStatusColor($status)
    {
        return match($status) {
            'paid' => 'success',
            'proses' => 'info',
            'waiting' => 'warning',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Generate payment receipt URL if bukti transfer exists
     */
    public function getPaymentReceiptUrl($transactionRequest)
    {
        if ($transactionRequest->bukti_transfer && Storage::disk('public')->exists($transactionRequest->bukti_transfer)) {
            return Storage::url($transactionRequest->bukti_transfer);
        }
        return null;
    }

    private function logEmailNotification($pengajuanId, $recipientId, $recipientEmail, $status, $message, $type = 'payment', $errorDetails = null)
    {
        EmailNotificationLog::create([
            'pengajuan_id' => $pengajuanId,
            'recipient_id' => $recipientId,
            'recipient_email' => $recipientEmail,
            'status' => $status,
            'message' => $message,
            'type' => $type,
            'sent_at' => $status === 'sent' ? now() : null,
            'error_details' => $errorDetails
        ]);
    }
}