<?php
namespace App\Http\Controllers\Employments;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\Settlement;
use App\Models\ProgressApproval;
use App\Models\TransactionRequest;
use App\Models\KategoriPengajuan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;

        // Stats Overview
        $stats = $this->getDashboardStats($userId);

        // Recent Activities
        $recentPengajuan = $this->getRecentPengajuan($userId);
        $pendingApprovals = $this->getPendingApprovals($userId);
        $recentSettlements = $this->getRecentSettlements($userId);

        // Chart Data
        $chartData = $this->getChartData($userId);

        // Kategori Stats
        $kategoriStats = $this->getKategoriStats($userId);

        // Hitung data belum completed
        $pendingPengajuan = \App\Models\Pengajuan::where('status_pengajuan', '!=', 'completed')->count();
        $pendingSettlement = \App\Models\Settlement::where('status_settlement', '!=', 'approved')->count();

        // Ambil notifikasi payment yang belum dibaca untuk user yang sedang login
        $notifikasiPayments = \App\Models\EmailNotificationLog::where('type', 'payment')
            ->where('recipient_id', $userId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        // Pass ke view
        $totalPending = $pendingPengajuan + $pendingSettlement;

        return view('Approval-app.Karyawan.Dashboard.index', compact(
            'userId',
            'stats',
            'recentPengajuan',
            'pendingApprovals',
            'recentSettlements',
            'chartData',
            'kategoriStats',
            'totalPending', 
            'pendingPengajuan', 
            'pendingSettlement', 
            'notifikasiPayments'
        ));
    }

    // Method baru untuk mark notification as read
    public function markNotificationAsRead(Request $request)
    {
        try {
            $notificationId = $request->notification_id;
            $userId = Auth::id();
            
            $notification = EmailNotificationLog::where('id', $notificationId)
                ->where('recipient_id', $userId)
                ->where('type', 'payment')
                ->first();
                
            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }
            
            $notification->update(['is_read' => 1]);
            
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil ditandai sebagai dibaca'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getDashboardStats($userId)
    {
        return [
            'total_pengajuan' => Pengajuan::where('requester_id', $userId)->count(),
            'pengajuan_approved' => Pengajuan::where('requester_id', $userId)->where('status_pengajuan', 'completed')->count(),
            'pengajuan_pending' => Pengajuan::where('requester_id', $userId)->whereIn('status_pengajuan', ['proses', 'settlement_create', 'proses_settlement'])->count(),
            'pengajuan_rejected' => Pengajuan::where('requester_id', $userId)->where('status_pengajuan', 'rejected')->count(),
            'pending_approvals' => ProgressApproval::where('approver_id', $userId)->where('status', 'pending')->count(),
            'total_nominal' => Pengajuan::where('requester_id', $userId)->sum('nominal_pengajuan'),
            'settlement_count' => Settlement::whereHas('pengajuan', function($q) use ($userId) {
                $q->where('requester_id', $userId);
            })->count(),
            'transaction_requests' => TransactionRequest::whereHas('pengajuan', function($q) use ($userId) {
                $q->where('requester_id', $userId);
            })->count()
        ];
    }

    private function getRecentPengajuan($userId)
    {
        return Pengajuan::with(['kategoriPengajuan', 'requester'])
            ->where('requester_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

   private function getPendingApprovals($userId)
    {
        return ProgressApproval::with(['pengajuan.kategoriPengajuan', 'pengajuan.requester'])
            ->where('approver_id', $userId)
            ->whereIn('status', ['proses', 'pending'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }


    private function getRecentSettlements($userId)
    {
        return Settlement::with(['pengajuan.kategoriPengajuan'])
            ->whereHas('pengajuan', function($q) use ($userId) {
                $q->where('requester_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getChartData($userId)
    {
        // Data untuk chart pengajuan per bulan (6 bulan terakhir)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Pengajuan::where('requester_id', $userId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }

        // Data untuk pie chart status
        $statusData = [
            ['label' => 'Proses', 'value' => Pengajuan::where('requester_id', $userId)->where('status_pengajuan', 'proses')->count()],
            ['label' => 'Ditolak', 'value' => Pengajuan::where('requester_id', $userId)->where('status_pengajuan', 'rejected')->count()],
            ['label' => 'Selesai', 'value' => Pengajuan::where('requester_id', $userId)->where('status_pengajuan', 'completed')->count()],
            ['label' => 'Proses Settlement', 'value' => Pengajuan::where('requester_id', $userId)->where('status_pengajuan', 'proses_settlement')->count()]
        ];

        return [
            'monthly' => $monthlyData,
            'status' => $statusData
        ];
    }

    private function getKategoriStats($userId)
    {
        return DB::table('Pengajuan as p')
            ->join('KategoriPengajuan as kp', 'p.kategori_pengajuan_id', '=', 'kp.id')
            ->where('p.requester_id', $userId)
            ->select('kp.nama', 'kp.icon', 'kp.warna', DB::raw('COUNT(*) as total'), DB::raw('SUM(p.nominal_pengajuan) as total_nominal'))
            ->groupBy('kp.id', 'kp.nama', 'kp.icon', 'kp.warna')
            ->orderBy('total', 'desc')
            ->get();
    }
}