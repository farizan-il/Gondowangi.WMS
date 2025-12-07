<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoryPengajuan;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getAllNotifications(Request $request)
    {
        try {
            // Ambil user yang sedang login
            $user = Auth::user();
            
            // Pastikan user memiliki karyawan_id
            if (!$user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak memiliki data karyawan'
                ]);
            }

            $karyawan = Karyawan::find($user->id);
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ]);
            }
            
            // Ambil notifikasi berdasarkan pengajuan yang dibuat oleh user ini
            $notifications = HistoryPengajuan::whereHas('pengajuan', function($query) use ($karyawan) {
                    $query->where('requester_id', $karyawan->id);
                })
                ->where('action', 'status_update')
                ->orderBy('created_at', 'desc')
                ->with(['pengajuan', 'actor'])
                ->paginate(20);
                
            return response()->json([
                'success' => true,
                'data' => $notifications->items(),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
    
    public function markAsRead(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak memiliki data karyawan'
                ]);
            }

            $karyawan = Karyawan::find($user->id);
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ]);
            }
            
            $notificationIds = $request->input('notification_ids', []);
            
            if (empty($notificationIds)) {
                // Mark all as read - hanya untuk pengajuan milik user ini
                HistoryPengajuan::whereHas('pengajuan', function($query) use ($karyawan) {
                        $query->where('requester_id', $karyawan->id);
                    })
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
                    
                $message = 'Semua notifikasi telah ditandai sebagai dibaca';
            } else {
                // Mark specific notifications as read - pastikan hanya milik user ini
                HistoryPengajuan::whereHas('pengajuan', function($query) use ($karyawan) {
                        $query->where('requester_id', $karyawan->id);
                    })
                    ->whereIn('id', $notificationIds)
                    ->update(['is_read' => true]);
                    
                $message = 'Notifikasi telah ditandai sebagai dibaca';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
    
    public function clearAll()
    {
        try {
            $user = Auth::user();
            
            if (!$user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak memiliki data karyawan'
                ]);
            }

            $karyawan = Karyawan::find($user->id);
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ]);
            }
            
            // Hapus semua notifikasi untuk pengajuan milik user ini
            HistoryPengajuan::whereHas('pengajuan', function($query) use ($karyawan) {
                    $query->where('requester_id', $karyawan->id);
                })
                ->delete();
                
            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi telah dihapus'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}