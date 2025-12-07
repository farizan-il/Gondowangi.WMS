<?php
// App/Services/SettlementNotificationService.php

namespace App\Services;

use App\Models\Settlement;
use App\Models\EmailNotificationLog;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SettlementNotificationService
{
    public function sendSubmissionNotifications(Settlement $settlement)
    {
        try {
            // 1. Kirim notifikasi ke requester (konfirmasi pengajuan)
            $this->sendRequesterNotification($settlement);
            
            // 2. Kirim notifikasi ke approver pertama
            $this->sendApproverNotification($settlement);
            
            // 3. Kirim notifikasi info ke semua approver (optional)
            $this->sendInfoToAllApprovers($settlement);
            
        } catch (\Exception $e) {
            Log::error('Settlement notification error: ' . $e->getMessage());
        }
    }
    
    private function sendRequesterNotification(Settlement $settlement)
    {
        $requester = $settlement->pengajuan->requester;
        
        try {
            Mail::send('emails.settlement.submitted_requester', [
                'settlement' => $settlement,
                'requester' => $requester,
                'pengajuan' => $settlement->pengajuan
            ], function($message) use ($requester, $settlement) {
                $message->to($requester->email, $requester->nama)
                       ->subject('Settlement Telah Diajukan - ' . $settlement->nomor_settlement);
            });
            
            // Log notifikasi dengan settlement_id
            EmailNotificationLog::create([
                'pengajuan_id' => $settlement->pengajuan_id,
                'settlement_id' => $settlement->id,  // NEW: Tambah settlement_id
                'recipient_id' => $requester->id,
                'recipient_email' => $requester->email,
                'status' => 'sent',
                'message' => 'Settlement submission notification sent to requester',
                'sent_at' => now()
            ]);
            
        } catch (\Exception $e) {
            EmailNotificationLog::create([
                'pengajuan_id' => $settlement->pengajuan_id,
                'settlement_id' => $settlement->id,  // NEW: Tambah settlement_id
                'recipient_id' => $requester->id,
                'recipient_email' => $requester->email,
                'status' => 'failed',
                'message' => 'Failed to send settlement notification to requester',
                'error_details' => ['error' => $e->getMessage()],
                'sent_at' => now()
            ]);
        }
    }
    
    private function sendApproverNotification(Settlement $settlement)
    {
        $currentApprover = $settlement->getCurrentApprover();
        
        if (!$currentApprover) {
            return;
        }
        
        $approver = Karyawan::find($currentApprover->approver_id);
        
        if (!$approver) {
            return;
        }
        
        try {
            Mail::send('emails.settlement.pending_approval', [
                'settlement' => $settlement,
                'approver' => $approver,
                'pengajuan' => $settlement->pengajuan,
                'requester' => $settlement->pengajuan->requester,
                'stepInfo' => $currentApprover
            ], function($message) use ($approver, $settlement) {
                $message->to($approver->email, $approver->nama)
                       ->subject('Settlement Menunggu Approval - ' . $settlement->nomor_settlement);
            });
            
            // Log notifikasi dengan settlement_id
            EmailNotificationLog::create([
                'pengajuan_id' => $settlement->pengajuan_id,
                'settlement_id' => $settlement->id,  // NEW: Tambah settlement_id
                'recipient_id' => $approver->id,
                'recipient_email' => $approver->email,
                'status' => 'sent',
                'message' => 'Settlement approval notification sent to current approver',
                'sent_at' => now()
            ]);
            
        } catch (\Exception $e) {
            EmailNotificationLog::create([
                'pengajuan_id' => $settlement->pengajuan_id,
                'settlement_id' => $settlement->id,  // NEW: Tambah settlement_id
                'recipient_id' => $approver->id,
                'recipient_email' => $approver->email,
                'status' => 'failed',
                'message' => 'Failed to send settlement approval notification',
                'error_details' => ['error' => $e->getMessage()],
                'sent_at' => now()
            ]);
        }
    }
    
    private function sendInfoToAllApprovers(Settlement $settlement)
    {
        $allApprovers = $settlement->progressApprovals()
            ->with('flowApproval')
            ->get();
            
        foreach ($allApprovers as $progressApproval) {
            $approver = Karyawan::find($progressApproval->approver_id);
            
            if (!$approver) {
                continue;
            }
            
            // Skip current approver (sudah dapat notifikasi khusus)
            if ($progressApproval->status === 'pending') {
                continue;
            }
            
            try {
                Mail::send('emails.settlement.info_notification', [
                    'settlement' => $settlement,
                    'approver' => $approver,
                    'pengajuan' => $settlement->pengajuan,
                    'requester' => $settlement->pengajuan->requester,
                    'stepInfo' => $progressApproval
                ], function($message) use ($approver, $settlement) {
                    $message->to($approver->email, $approver->nama)
                           ->subject('Info: Settlement Diajukan - ' . $settlement->nomor_settlement);
                });
                
                // Log notifikasi dengan settlement_id
                EmailNotificationLog::create([
                    'pengajuan_id' => $settlement->pengajuan_id,
                    'settlement_id' => $settlement->id,  // NEW: Tambah settlement_id
                    'recipient_id' => $approver->id,
                    'recipient_email' => $approver->email,
                    'status' => 'sent',
                    'message' => 'Settlement info notification sent to approver',
                    'sent_at' => now()
                ]);
                
            } catch (\Exception $e) {
                EmailNotificationLog::create([
                    'pengajuan_id' => $settlement->pengajuan_id,
                    'settlement_id' => $settlement->id,  // NEW: Tambah settlement_id
                    'recipient_id' => $approver->id,
                    'recipient_email' => $approver->email,
                    'status' => 'failed',
                    'message' => 'Failed to send settlement info notification',
                    'error_details' => ['error' => $e->getMessage()],
                    'sent_at' => now()
                ]);
            }
        }
    }
}