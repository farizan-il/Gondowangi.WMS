<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\PengajuanBaruNotification;
use App\Models\Pengajuan;
use App\Models\Karyawan;
use App\Models\EmailNotificationLog;

class SendPengajuanEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $pengajuan;
    public $penerima;
    public $tipePenerima;
    
    // Konfigurasi retry
    public $tries = 3;
    public $maxExceptions = 3;
    public $backoff = [60, 180, 600]; // Retry setelah 1 menit, 3 menit, 10 menit

    /**
     * Create a new job instance.
     */
    public function __construct(Pengajuan $pengajuan, Karyawan $penerima, $tipePenerima = 'approver')
    {
        $this->pengajuan = $pengajuan;
        $this->penerima = $penerima;
        $this->tipePenerima = $tipePenerima;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Update status menjadi processing
            $this->updateEmailLog('processing', 'Memproses pengiriman email...');

            // Validasi email penerima
            if (empty($this->penerima->email)) {
                throw new \Exception('Email penerima tidak ditemukan atau kosong');
            }

            if (!filter_var($this->penerima->email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Format email tidak valid: ' . $this->penerima->email);
            }

            // Kirim email
            Mail::to($this->penerima->email)
                ->send(new PengajuanBaruNotification(
                    $this->pengajuan, 
                    $this->penerima, 
                    $this->tipePenerima
                ));

            // Update status berhasil
            $this->updateEmailLog('sent', 'Email berhasil dikirim');

            \Log::info('Email pengajuan berhasil dikirim melalui job queue', [
                'pengajuan_id' => $this->pengajuan->id,
                'pengajuan_nomor' => $this->pengajuan->nomor_pengajuan,
                'penerima_id' => $this->penerima->id,
                'penerima_nama' => $this->penerima->nama,
                'penerima_email' => $this->penerima->email,
                'tipe_penerima' => $this->tipePenerima,
                'attempt' => $this->attempts()
            ]);

        } catch (\Exception $e) {
            // Update status gagal
            $this->updateEmailLog('failed', 'Gagal mengirim email: ' . $e->getMessage(), [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'attempt' => $this->attempts()
            ]);

            \Log::error('Gagal mengirim email pengajuan melalui job queue', [
                'pengajuan_id' => $this->pengajuan->id,
                'penerima_email' => $this->penerima->email,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries
            ]);
            
            // Re-throw exception untuk retry mechanism jika belum mencapai max tries
            if ($this->attempts() < $this->tries) {
                throw $e;
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception)
    {
        // Update status final failure
        $this->updateEmailLog('error', 'Job gagal setelah ' . $this->tries . ' kali percobaan: ' . $exception->getMessage(), [
            'final_error' => $exception->getMessage(),
            'total_attempts' => $this->tries,
            'failed_at' => now()->toDateTimeString()
        ]);

        \Log::error('Job SendPengajuanEmailNotification gagal final', [
            'pengajuan_id' => $this->pengajuan->id,
            'penerima_email' => $this->penerima->email,
            'tipe_penerima' => $this->tipePenerima,
            'error' => $exception->getMessage(),
            'total_attempts' => $this->tries
        ]);

        // Kirim notifikasi ke admin (opsional)
        $this->notifyAdminOfFailure($exception);
    }

    /**
     * Update log email notification
     */
    private function updateEmailLog($status, $message, $errorDetails = null)
    {
        try {
            // Cari log yang sudah ada atau buat baru
            $log = EmailNotificationLog::where('pengajuan_id', $this->pengajuan->id)
                ->where('recipient_id', $this->penerima->id)
                ->first();

            if (!$log) {
                $log = new EmailNotificationLog();
                $log->pengajuan_id = $this->pengajuan->id;
                $log->recipient_id = $this->penerima->id;
                $log->recipient_email = $this->penerima->email;
            }

            $log->status = $status;
            $log->message = $message;
            $log->sent_at = now();
            
            if ($errorDetails) {
                $log->error_details = $errorDetails;
            }

            $log->save();

        } catch (\Exception $e) {
            \Log::error('Gagal update email log: ' . $e->getMessage());
        }
    }

    /**
     * Notifikasi admin ketika email gagal total
     */
    private function notifyAdminOfFailure($exception)
    {
        try {
            // Kirim email ke admin atau log ke sistem monitoring
            $adminEmails = config('mail.admin_emails', []); // Set di config

            if (!empty($adminEmails)) {
                foreach ($adminEmails as $adminEmail) {
                    Mail::raw(
                        "Email notification gagal dikirim:\n\n" .
                        "Pengajuan: {$this->pengajuan->nomor_pengajuan}\n" .
                        "Judul: {$this->pengajuan->judul}\n" .
                        "Penerima: {$this->penerima->nama} ({$this->penerima->email})\n" .
                        "Error: {$exception->getMessage()}\n" .
                        "Tanggal: " . now()->format('d/m/Y H:i:s'),
                        function ($message) use ($adminEmail) {
                            $message->to($adminEmail)
                                   ->subject('ALERT: Email Notification Gagal - ' . $this->pengajuan->nomor_pengajuan);
                        }
                    );
                }
            }
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim notifikasi admin: ' . $e->getMessage());
        }
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff()
    {
        return $this->backoff;
    }
}