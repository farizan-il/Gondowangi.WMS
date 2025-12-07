<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailNotificationLog extends Model
{
    use HasFactory;

    protected $table = 'email_notification_logs';

    protected $fillable = [
        'pengajuan_id',
        'settlement_id', // bisa null
        'recipient_id',
        'recipient_email',
        'type', // 'notifikasi', 'payment', 'argo_settlement_reminder'
        'status', // 'success', 'failed', 'pending'
        'is_read',
        'message',
        'error_details',
        'sent_at',
        'retry_count', // tambahan untuk retry mechanism
        'last_retry_at', // tambahan untuk tracking retry
    ];

    protected $casts = [
        'pengajuan_id' => 'integer',
        'settlement_id' => 'integer',
        'recipient_id' => 'integer',
        'sent_at' => 'datetime',
        'is_read' => 'boolean',
        'error_details' => 'array',
        'last_retry_at' => 'datetime',
        'retry_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }

    public function recipient()
    {
        return $this->belongsTo(Karyawan::class, 'recipient_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeSettlementEmails($query)
    {
        return $query->where('type', 'settlement_approved')
                    ->orWhere('message', 'like', '%Settlement approved notification%');
    }

    // Scope untuk argo reminder emails
    public function scopeArgoReminders($query)
    {
        return $query->where('type', 'argo_settlement_reminder');
    }

    // Scope untuk recent notifications (dalam X jam terakhir)
    public function scopeRecent($query, $hours = 5)
    {
        return $query->where('sent_at', '>', now()->subHours($hours));
    }

    // Helper methods
    public function isSuccess()
    {
        return $this->status === 'success';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function canRetry()
    {
        return $this->status === 'failed' && $this->retry_count < 3;
    }

    public function incrementRetry()
    {
        $this->update([
            'retry_count' => $this->retry_count + 1,
            'last_retry_at' => now()
        ]);
    }

    public function markAsSuccess()
    {
        $this->update([
            'status' => 'success',
            'sent_at' => now()
        ]);
    }

    public function markAsFailed($errorMessage = null, $errorDetails = null)
    {
        $this->update([
            'status' => 'failed',
            'message' => $errorMessage ?: $this->message,
            'error_details' => $errorDetails ?: $this->error_details
        ]);
    }

    // Static methods for creating logs
    public static function createSettlementApprovalLog($pengajuanId, $settlementId, $recipientId, $recipientEmail, $status, $message = null, $errorDetails = null)
    {
        return self::create([
            'pengajuan_id' => $pengajuanId,
            'settlement_id' => $settlementId,
            'recipient_id' => $recipientId,
            'recipient_email' => $recipientEmail,
            'type' => 'settlement_approved',
            'status' => $status,
            'message' => $message ?: 'Settlement approved notification',
            'error_details' => $errorDetails,
            'sent_at' => $status === 'success' ? now() : null,
            'retry_count' => 0
        ]);
    }

    public static function createStatusUpdateLog($pengajuanId, $recipientId, $recipientEmail, $status, $message = null, $errorDetails = null)
    {
        return self::create([
            'pengajuan_id' => $pengajuanId,
            'settlement_id' => null,
            'recipient_id' => $recipientId,
            'recipient_email' => $recipientEmail,
            'type' => 'status_update',
            'status' => $status,
            'message' => $message ?: 'Status update notification',
            'error_details' => $errorDetails,
            'sent_at' => $status === 'success' ? now() : null,
            'retry_count' => 0
        ]);
    }

    // Static method untuk membuat argo settlement reminder log
    public static function createArgoReminderLog($pengajuanId, $recipientId, $recipientEmail, $status, $message = null, $errorDetails = null)
    {
        return self::create([
            'pengajuan_id' => $pengajuanId,
            'settlement_id' => null,
            'recipient_id' => $recipientId,
            'recipient_email' => $recipientEmail,
            'type' => 'argo_settlement_reminder',
            'status' => $status,
            'message' => $message ?: 'Argo settlement reminder notification',
            'error_details' => $errorDetails,
            'sent_at' => $status === 'success' ? now() : null,
            'retry_count' => 0
        ]);
    }

    // Helper method untuk cek apakah argo reminder sudah dikirim dalam X jam terakhir
    public static function hasRecentArgoReminder($pengajuanId, $hours = 5)
    {
        return self::where('pengajuan_id', $pengajuanId)
                  ->where('type', 'argo_settlement_reminder')
                  ->where('status', 'success')
                  ->where('sent_at', '>', now()->subHours($hours))
                  ->exists();
    }
}