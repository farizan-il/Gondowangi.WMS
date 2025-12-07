<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailNotificationLog;
use App\Models\Pengajuan;
use App\Models\Karyawan;
use App\Mail\PengajuanBaruNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EmailMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:monitor 
       {action : Aksi yang akan dilakukan (status|test|retry|clean)}
       {--pengajuan= : ID pengajuan untuk test atau retry}
       {--email= : Email untuk testing}
       {--days=7 : Jumlah hari untuk clean log}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor dan kelola email notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'status':
                $this->showEmailStatus();
                break;
            case 'test':
                $this->testEmail();
                break;
            case 'retry':
                $this->retryFailedEmail();
                break;
            case 'clean':
                $this->cleanOldLogs();
                break;
            default:
                $this->error('Aksi tidak valid. Gunakan: status, test, retry, atau clean');
        }
    }

    /**
     * Tampilkan status email notifications
     */
    private function showEmailStatus()
    {
        $this->info('=== STATUS EMAIL NOTIFICATIONS ===');
        
        // Status dalam 24 jam terakhir
        $yesterday = Carbon::now()->subDay();
        $logs = EmailNotificationLog::where('created_at', '>=', $yesterday)->get();
        
        $statusCount = [
            'sent' => 0,
            'failed' => 0,
            'queued' => 0,
            'error' => 0
        ];

        foreach ($logs as $log) {
            $statusCount[$log->status] = ($statusCount[$log->status] ?? 0) + 1;
        }

        $this->table(
            ['Status', 'Jumlah (24 jam terakhir)'],
            [
                ['Terkirim', $statusCount['sent']],
                ['Gagal', $statusCount['failed']],
                ['Antrian', $statusCount['queued']],
                ['Error', $statusCount['error']],
                ['Total', array_sum($statusCount)]
            ]
        );

        // Tampilkan email gagal terbaru
        $failedEmails = EmailNotificationLog::whereIn('status', ['failed', 'error'])
            ->with(['pengajuan', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($failedEmails->count() > 0) {
            $this->error("\n=== EMAIL GAGAL TERBARU ===");
            $failedData = [];
            
            foreach ($failedEmails as $log) {
                $failedData[] = [
                    $log->pengajuan->nomor_pengajuan ?? 'N/A',
                    $log->recipient->nama ?? 'Unknown',
                    $log->recipient_email,
                    $log->status,
                    $log->message,
                    $log->created_at->format('d/m/Y H:i')
                ];
            }
            
            $this->table(
                ['Nomor Pengajuan', 'Penerima', 'Email', 'Status', 'Pesan Error', 'Waktu'],
                $failedData
            );
        }

        // Cek konfigurasi email
        $this->checkEmailConfiguration();
    }

    /**
     * Test pengiriman email
     */
    private function testEmail()
    {
        $pengajuanId = $this->option('pengajuan');
        $testEmail = $this->option('email');

        if (!$pengajuanId || !$testEmail) {
            $this->error('Gunakan: php artisan email:monitor test --pengajuan=1 --email=test@example.com');
            return;
        }

        try {
            $pengajuan = Pengajuan::with(['kategoriPengajuan', 'requester.department'])->findOrFail($pengajuanId);
            
            // Buat dummy karyawan untuk testing
            $penerima = new Karyawan();
            $penerima->id = 0;
            $penerima->nama = 'Test User';
            $penerima->email = $testEmail;

            $this->info("Mengirim test email untuk pengajuan: {$pengajuan->nomor_pengajuan}");
            $this->info("Ke email: {$testEmail}");

            Mail::to($testEmail)->send(new PengajuanBaruNotification($pengajuan, $penerima, 'test'));
            
            $this->info('✅ Test email berhasil dikirim!');
            
            // Log test email
            EmailNotificationLog::create([
                'pengajuan_id' => $pengajuan->id,
                'recipient_id' => null,
                'recipient_email' => $testEmail,
                'status' => 'sent',
                'message' => 'Test email berhasil dikirim',
                'sent_at' => now()
            ]);

        } catch (\Exception $e) {
            $this->error('❌ Gagal mengirim test email: ' . $e->getMessage());
            
            // Log test email gagal
            if (isset($pengajuan)) {
                EmailNotificationLog::create([
                    'pengajuan_id' => $pengajuan->id,
                    'recipient_id' => null,
                    'recipient_email' => $testEmail,
                    'status' => 'failed',
                    'message' => 'Test email gagal: ' . $e->getMessage(),
                    'error_details' => [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ],
                    'sent_at' => now()
                ]);
            }
        }
    }

    /**
     * Retry email yang gagal
     */
    private function retryFailedEmail()
    {
        $pengajuanId = $this->option('pengajuan');
        
        if (!$pengajuanId) {
            // Retry semua email gagal dalam 24 jam terakhir
            $failedLogs = EmailNotificationLog::whereIn('status', ['failed', 'error'])
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->with(['pengajuan', 'recipient'])
                ->get();

            if ($failedLogs->count() == 0) {
                $this->info('Tidak ada email gagal yang perlu di-retry');
                return;
            }

            $this->info("Ditemukan {$failedLogs->count()} email gagal. Melakukan retry...");

        } else {
            // Retry untuk pengajuan spesifik
            $failedLogs = EmailNotificationLog::whereIn('status', ['failed', 'error'])
                ->where('pengajuan_id', $pengajuanId)
                ->with(['pengajuan', 'recipient'])
                ->get();

            if ($failedLogs->count() == 0) {
                $this->info("Tidak ada email gagal untuk pengajuan ID: {$pengajuanId}");
                return;
            }
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($failedLogs as $log) {
            if (!$log->pengajuan || !$log->recipient) {
                $this->warn("Skip log ID {$log->id}: Data pengajuan atau recipient tidak ditemukan");
                continue;
            }

            try {
                $this->info("Retry email untuk: {$log->recipient->nama} ({$log->recipient_email})");

                Mail::to($log->recipient_email)
                    ->send(new PengajuanBaruNotification($log->pengajuan, $log->recipient, 'retry'));

                // Update status
                $log->update([
                    'status' => 'sent',
                    'message' => 'Email berhasil dikirim ulang',
                    'sent_at' => now()
                ]);

                $successCount++;
                $this->info("✅ Berhasil");

            } catch (\Exception $e) {
                $log->update([
                    'message' => 'Retry gagal: ' . $e->getMessage(),
                    'error_details' => array_merge($log->error_details ?? [], [
                        'retry_error' => $e->getMessage(),
                        'retry_at' => now()->toDateTimeString()
                    ])
                ]);

                $failCount++;
                $this->error("❌ Gagal: " . $e->getMessage());
            }
        }

        $this->info("\n=== HASIL RETRY ===");
        $this->info("Berhasil: {$successCount}");
        $this->info("Gagal: {$failCount}");
    }

    /**
     * Bersihkan log email lama
     */
    private function cleanOldLogs()
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $oldLogsCount = EmailNotificationLog::where('created_at', '<', $cutoffDate)->count();

        if ($oldLogsCount == 0) {
            $this->info("Tidak ada log email yang lebih lama dari {$days} hari");
            return;
        }

        if ($this->confirm("Hapus {$oldLogsCount} log email yang lebih lama dari {$days} hari?")) {
            $deleted = EmailNotificationLog::where('created_at', '<', $cutoffDate)->delete();
            $this->info("✅ Berhasil menghapus {$deleted} log email lama");
        } else {
            $this->info("Operasi dibatalkan");
        }
    }

    /**
     * Cek konfigurasi email
     */
    private function checkEmailConfiguration()
    {
        $this->info("\n=== KONFIGURASI EMAIL ===");
        
        $configs = [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username') ? '***SET***' : 'NOT SET',
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'QUEUE_CONNECTION' => config('queue.default')
        ];

        $configData = [];
        foreach ($configs as $key => $value) {
            $status = !empty($value) ? '✅' : '❌';
            $configData[] = [$key, $value ?? 'NOT SET', $status];
        }

        $this->table(['Config', 'Value', 'Status'], $configData);
    }
}