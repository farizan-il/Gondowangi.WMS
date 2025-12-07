<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pengajuan;
use App\Models\EmailNotificationLog;
use Carbon\Carbon;

class DebugArgoCommand extends Command
{
    protected $signature = 'argo:debug';
    protected $description = 'Debug argo notifications to see what data exists';

    public function handle()
    {
        $this->info('=== DEBUG ARGO NOTIFICATIONS ===');
        
        // 1. Cek semua pengajuan dengan status approved
        $approvedCount = Pengajuan::where('status_pengajuan', 'approved')->count();
        $this->info("1. Total pengajuan approved: {$approvedCount}");
        
        // 2. Cek yang membutuhkan settlement
        $needSettlementCount = Pengajuan::where('status_pengajuan', 'approved')
            ->where('is_settlement_required', true)
            ->count();
        $this->info("2. Yang membutuhkan settlement: {$needSettlementCount}");
        
        // 3. Cek yang belum ada settlement_id
        $noSettlementCount = Pengajuan::where('status_pengajuan', 'approved')
            ->where('is_settlement_required', true)
            ->whereNull('settlement_id')
            ->count();
        $this->info("3. Yang belum ada settlement_id: {$noSettlementCount}");
        
        // 4. Cek yang ada argo
        $withArgoCount = Pengajuan::where('status_pengajuan', 'approved')
            ->where('is_settlement_required', true)
            ->whereNull('settlement_id')
            ->whereNotNull('argo')
            ->count();
        $this->info("4. Yang ada argo: {$withArgoCount}");
        
        // 5. Detail pengajuan yang memenuhi kriteria dasar
        $pengajuanList = Pengajuan::where('status_pengajuan', 'approved')
            ->where('is_settlement_required', true)
            ->whereNull('settlement_id')
            ->whereNotNull('argo')
            ->with(['requester', 'kategoriPengajuan'])
            ->get();
        
        $this->info("\n=== DETAIL PENGAJUAN ===");
        
        foreach ($pengajuanList as $pengajuan) {
            $remainingDays = $pengajuan->getRemainingArgoDays();
            $argoDate = Carbon::parse($pengajuan->argo);
            $today = Carbon::today();
            
            $this->info("\n--- Pengajuan: {$pengajuan->nomor_pengajuan} ---");
            $this->info("Requester: {$pengajuan->requester->nama} ({$pengajuan->requester->email})");
            $this->info("Kategori: " . ($pengajuan->kategoriPengajuan->nama ?? 'N/A'));
            $this->info("Status: {$pengajuan->status_pengajuan}");
            $this->info("Settlement Required: " . ($pengajuan->is_settlement_required ? 'Ya' : 'Tidak'));
            $this->info("Settlement ID: " . ($pengajuan->settlement_id ?? 'NULL'));
            $this->info("Argo Date: " . $argoDate->format('d/m/Y'));
            $this->info("Today: " . $today->format('d/m/Y'));
            $this->info("Remaining Days: {$remainingDays}");
            $this->info("Kriteria <5 hari: " . ($remainingDays !== null && $remainingDays < 5 ? 'YA' : 'TIDAK'));
            
            // Cek notifikasi terakhir
            $lastNotification = EmailNotificationLog::where('pengajuan_id', $pengajuan->id)
                ->where('type', 'argo_settlement_reminder')
                ->where('status', 'success')
                ->where('sent_at', '>', Carbon::now()->subHours(5))
                ->first();
                
            $this->info("Notifikasi dalam 5 jam terakhir: " . ($lastNotification ? 'ADA' : 'TIDAK ADA'));
            
            if ($remainingDays !== null && $remainingDays < 5 && !$lastNotification) {
                $this->info(">>> AKAN DIKIRIM NOTIFIKASI <<<");
            } else {
                $this->info(">>> TIDAK DIKIRIM NOTIFIKASI <<<");
            }
        }
        
        // 6. Cek semua email notification logs untuk argo
        $argoNotificationCount = EmailNotificationLog::where('type', 'argo_settlement_reminder')->count();
        $this->info("\n=== EMAIL LOGS ===");
        $this->info("Total argo notification logs: {$argoNotificationCount}");
        
        // Recent notifications
        $recentNotifications = EmailNotificationLog::where('type', 'argo_settlement_reminder')
            ->where('sent_at', '>', Carbon::now()->subDays(1))
            ->with('pengajuan')
            ->get();
            
        $this->info("Notifikasi dalam 24 jam terakhir: {$recentNotifications->count()}");
        
        foreach ($recentNotifications as $notif) {
            $this->info("- {$notif->pengajuan->nomor_pengajuan} ke {$notif->recipient_email} pada {$notif->sent_at}");
        }
        
        // 7. Test method getRemainingArgoDays()
        $this->info("\n=== TEST METHOD ===");
        if ($pengajuanList->count() > 0) {
            $testPengajuan = $pengajuanList->first();
            $this->info("Test pengajuan: {$testPengajuan->nomor_pengajuan}");
            $this->info("Argo value dari DB: {$testPengajuan->argo}");
            
            // Manual calculation
            $argoDate = Carbon::parse($testPengajuan->argo);
            $today = Carbon::today();
            $diffDays = $today->diffInDays($argoDate, false); // false = bisa negatif
            
            $this->info("Manual calculation:");
            $this->info("- Argo date: " . $argoDate->format('Y-m-d'));
            $this->info("- Today: " . $today->format('Y-m-d'));
            $this->info("- Diff days: {$diffDays}");
            $this->info("- Method result: " . $testPengajuan->getRemainingArgoDays());
        }
        
        $this->info("\n=== SELESAI DEBUG ===");
        return 0;
    }
}