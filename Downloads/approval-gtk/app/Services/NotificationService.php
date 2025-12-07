<?php 

namespace App\Services;

use App\Models\HistoryPengajuan;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Get notifications for user (from HistoryPengajuan)
     */
    public function getUserNotifications($userId, $limit = 5, $unreadOnly = false)
    {
        $query = HistoryPengajuan::forRequester($userId)
            ->approvalUpdates()
            ->with(['pengajuan.kategoriPengajuan', 'actor'])
            ->orderBy('created_at', 'desc');

        if ($unreadOnly) {
            $query->unread();
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get()->map(function ($history) {
            return [
                'id' => $history->id,
                'pengajuan_id' => $history->pengajuan_id,
                'title' => $history->getNotificationTitle(),
                'message' => $history->getNotificationMessage(),
                'type' => $history->getNotificationType(),
                'is_read' => $history->is_read,
                'created_at' => $history->created_at,
                'time_ago' => $history->getTimeAgo(),
                'icon' => $history->getNotificationIcon(),
                'type_class' => $history->getNotificationTypeClass(),
                'actor_name' => $history->actor_name,
                'step_name' => $history->step_name,
                'catatan' => $history->catatan,
                'pengajuan' => [
                    'nomor_pengajuan' => $history->pengajuan->nomor_pengajuan,
                    'judul' => $history->pengajuan->judul,
                    'kategori' => $history->pengajuan->kategoriPengajuan->nama ?? '-'
                ]
            ];
        });
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($historyId, $userId)
    {
        return HistoryPengajuan::whereId($historyId)
            ->forRequester($userId)
            ->update(['is_read' => true]);
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($userId)
    {
        return HistoryPengajuan::forRequester($userId)
            ->approvalUpdates()
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Get unread count
     */
    public function getUnreadCount($userId)
    {
        return HistoryPengajuan::forRequester($userId)
            ->approvalUpdates()
            ->unread()
            ->count();
    }

    /**
     * Clear all notifications for user (mark as read)
     */
    public function clearAllNotifications($userId)
    {
        return HistoryPengajuan::forRequester($userId)
            ->approvalUpdates()
            ->update(['is_read' => true]);
    }

    /**
     * Get paginated notifications
     */
    public function getPaginatedNotifications($userId, $page = 1, $perPage = 20)
    {
        $notifications = $this->getUserNotifications($userId, null);
        $total = $notifications->count();
        
        // Simple pagination
        $paginatedNotifications = $notifications->slice(($page - 1) * $perPage, $perPage)->values();
        
        return [
            'notifications' => $paginatedNotifications,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'has_more' => $total > ($page * $perPage)
        ];
    }
}