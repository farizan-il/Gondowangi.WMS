<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pengajuan;
use App\Models\EmailNotificationLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\ArgoSettlementReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckArgoNotificationCommand extends Command
{
    protected $signature = 'argo:check-notifications';
    protected $description = 'Check and send argo settlement reminder notifications';

    public function handle()
    {
        $this->info('Starting argo notification check...');
        
        // Cek apakah waktu saat ini adalah dalam rentang jam pengiriman notifikasi
        $currentTime = Carbon::now();
        $currentHour = $currentTime->hour;
        $currentMinute = $currentTime->minute;
        
        // Allowed time windows (dengan toleransi 2 menit)
        $allowedTimeRanges = [
            ['hour' => 9, 'min_start' => 0, 'min_end' => 2],    // 09:00-09:02
            ['hour' => 11, 'min_start' => 0, 'min_end' => 2],   // 11:00-11:02
            ['hour' => 11, 'min_start' => 30, 'min_end' => 32], // 11:30-11:32
            ['hour' => 12, 'min_start' => 0, 'min_end' => 2],   // 12:00-12:02
            ['hour' => 15, 'min_start' => 0, 'min_end' => 2],   // 15:00-15:02
            ['hour' => 16, 'min_start' => 0, 'min_end' => 2],   // 16:00-16:02
            ['hour' => 17, 'min_start' => 0, 'min_end' => 2],   // 17:00-17:02
        ];
        
        $isValidTime = false;
        $scheduledTime = '';
        
        foreach ($allowedTimeRanges as $range) {
            if ($currentHour == $range['hour'] && 
                $currentMinute >= $range['min_start'] && 
                $currentMinute <= $range['min_end']) {
                $isValidTime = true;
                $scheduledTime = sprintf('%02d:%02d', $range['hour'], $range['min_start']);
                break;
            }
        }
        
        if (!$isValidTime) {
            $currentTimeFormatted = $currentTime->format('H:i');
            $this->info("Current time ({$currentTimeFormatted}) is not a scheduled notification time. Allowed times: 09:00, 11:00, 11:30, 12:00, 15:00, 16:00, 17:00");
            return 0;
        }
        
        // Ambil pengajuan yang memenuhi kriteria:
        // 1. Status approved atau proses_settlement
        // 2. Membutuhkan settlement
        // 3. Belum ada settlement_id
        // 4. Argo sisa ≤5 hari
        $pengajuanList = Pengajuan::whereIn('status_pengajuan', ['approved', 'proses_settlement'])
            ->where('is_settlement_required', true)
            ->whereNull('settlement_id')
            ->whereNotNull('argo')
            ->with(['requester', 'kategoriPengajuan'])
            ->get();

        $notificationsSent = 0;

        foreach ($pengajuanList as $pengajuan) {
            $remainingDays = $pengajuan->getRemainingArgoDays();
            
            // Skip jika argo sudah expired atau masih >5 hari
            if ($remainingDays === null || $remainingDays > 5) {
                continue;
            }

            // Cek apakah sudah ada notifikasi pada hari yang sama
            $today = Carbon::now()->startOfDay();
            $lastNotification = EmailNotificationLog::where('pengajuan_id', $pengajuan->id)
                ->where('type', 'argo_settlement_reminder')
                ->where('status', 'success')
                ->where('sent_at', '>=', $today)
                ->where('sent_at', '<', $today->copy()->addDay())
                ->first();

            if ($lastNotification) {
                $this->info("Skipping pengajuan {$pengajuan->nomor_pengajuan} - notification already sent today");
                continue;
            }

            try {
                // Kirim email
                Mail::to($pengajuan->requester->email)->send(
                    new ArgoSettlementReminder($pengajuan)
                );

                // Log notifikasi berhasil
                EmailNotificationLog::create([
                    'pengajuan_id' => $pengajuan->id,
                    'settlement_id' => null,
                    'recipient_id' => $pengajuan->requester_id,
                    'recipient_email' => $pengajuan->requester->email,
                    'type' => 'argo_settlement_reminder',
                    'status' => 'success',
                    'message' => "Argo settlement reminder - {$remainingDays} days remaining (≤5 days alert, sent at {$scheduledTime})",
                    'sent_at' => Carbon::now(),
                ]);

                $notificationsSent++;
                $this->info("Notification sent for pengajuan: {$pengajuan->nomor_pengajuan} ({$remainingDays} days remaining) at {$scheduledTime}");

            } catch (\Exception $e) {
                // Log notifikasi gagal
                EmailNotificationLog::create([
                    'pengajuan_id' => $pengajuan->id,
                    'settlement_id' => null,
                    'recipient_id' => $pengajuan->requester_id,
                    'recipient_email' => $pengajuan->requester->email,
                    'type' => 'argo_settlement_reminder',
                    'status' => 'failed',
                    'message' => "Failed to send argo settlement reminder at {$scheduledTime}",
                    'error_details' => [
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]
                ]);

                $this->error("Failed to send notification for pengajuan: {$pengajuan->nomor_pengajuan} - {$e->getMessage()}");
                Log::error('Argo notification failed', [
                    'pengajuan_id' => $pengajuan->id,
                    'error' => $e->getMessage(),
                    'time' => $scheduledTime
                ]);
            }
        }

        $this->info("Argo notification check completed at {$scheduledTime}. {$notificationsSent} notifications sent.");
        return 0;
    }
}