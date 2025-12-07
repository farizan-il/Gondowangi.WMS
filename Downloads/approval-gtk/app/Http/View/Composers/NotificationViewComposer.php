<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoryPengajuan;
use App\Models\Karyawan;
use Carbon\Carbon;

class NotificationViewComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $newNotifications = collect();
        $earlierNotifications = collect();
        $unreadCount = 0;

        try {
            $user = Auth::user();
            
            if ($user && $user->karyawan_id) {
                $karyawan = Karyawan::find($user->karyawan_id);
                
                if ($karyawan) {
                    // Ambil notifikasi untuk pengajuan yang dibuat oleh karyawan ini
                    $allNotifications = HistoryPengajuan::whereHas('pengajuan', function($query) use ($karyawan) {
                            $query->where('requester_id', $karyawan->id);
                        })
                        ->where('action', 'status_update')
                        ->orderBy('created_at', 'desc')
                        ->with(['pengajuan', 'actor'])
                        ->limit(20)
                        ->get();

                    // Hitung notifikasi yang belum dibaca
                    $unreadCount = $allNotifications->where('is_read', false)->count();

                    // Pisahkan notifikasi baru (24 jam terakhir) dan yang lama
                    $cutoffTime = Carbon::now()->subDay();
                    
                    $newNotifications = $allNotifications->filter(function ($notification) use ($cutoffTime) {
                        return $notification->created_at >= $cutoffTime;
                    })->take(5); // Batasi 5 notifikasi terbaru

                    $earlierNotifications = $allNotifications->filter(function ($notification) use ($cutoffTime) {
                        return $notification->created_at < $cutoffTime;
                    })->take(5); // Batasi 5 notifikasi lama
                }
            }
        } catch (\Exception $e) {
            // Log error jika perlu
            \Log::error('NotificationViewComposer Error: ' . $e->getMessage());
        }

        $view->with([
            'newNotifications' => $newNotifications,
            'earlierNotifications' => $earlierNotifications,
            'unreadCount' => $unreadCount
        ]);
    }
}