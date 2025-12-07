<?php
namespace App\Http\Controllers\Employments;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\Settlement;
use App\Models\ProgressApproval;
use App\Models\TransactionRequest;
use App\Models\TransactionRequestGroup;
use App\Models\KategoriPengajuan;
use App\Models\Karyawan;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardOverviewController extends Controller
{
    public function index()
    {
        // Overview Stats
        $overviewStats = $this->getOverviewStats();
        
        // Department Performance
        $departmentStats = $this->getDepartmentStats();
        
        // Top Requesters
        $topRequesters = $this->getTopRequesters();
        
        // Recent Activities (All Users)
        $recentActivities = $this->getRecentActivities();
        
        // Pending Items (All)
        $pendingItems = $this->getPendingItems();
        
        // Chart Data
        $chartData = $this->getChartData();
        
        // Financial Summary
        $financialSummary = $this->getFinancialSummary();
        
        // Category Performance
        $categoryPerformance = $this->getCategoryPerformance();
        
        // Monthly Trends
        $monthlyTrends = $this->getMonthlyTrends();
        
        // Hitung data belum completed
        $pendingPengajuan = \App\Models\Pengajuan::where('status_pengajuan', '!=', 'completed')->count();
        $pendingSettlement = \App\Models\Settlement::where('status_settlement', '!=', 'approved')->count();
        
        // Pass ke view
        $totalPending = $pendingPengajuan + $pendingSettlement;

        return view('Approval-app.Karyawan.Dashboard.overview', compact(
            'overviewStats',
            'departmentStats', 
            'topRequesters',
            'recentActivities',
            'pendingItems',
            'chartData',
            'financialSummary',
            'categoryPerformance',
            'monthlyTrends',
            'totalPending', 'pendingPengajuan', 'pendingSettlement'
        ));
    }

    private function getOverviewStats()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $lastMonth = Carbon::now()->subMonth();

        return [
            'total_pengajuan' => Pengajuan::count(),
            'total_karyawan' => Karyawan::where('status', 'aktif')->count(),
            'total_department' => Department::count(),
            'pengajuan_approved' => Pengajuan::where('status_pengajuan', 'approved')->count(),
            'pengajuan_pending' => Pengajuan::where('status_pengajuan', 'proses')->count(),
            'pengajuan_rejected' => Pengajuan::where('status_pengajuan', 'rejected')->count(),
            'pengajuan_completed' => Pengajuan::where('status_pengajuan', 'completed')->count(),
            
            // Monthly comparisons
            'pengajuan_this_month' => Pengajuan::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count(),
            'pengajuan_last_month' => Pengajuan::whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->count(),
            
            // Financial
            'total_nominal_approved' => Pengajuan::where('status_pengajuan', 'completed')->sum('nominal_pengajuan'),
            'total_nominal_pending' => Pengajuan::where('status_pengajuan', 'proses')->sum('nominal_pengajuan'),
            'total_settlement' => Settlement::sum('total_actual'),
            
            // Transaction Requests
            'total_tr_groups' => TransactionRequestGroup::count(),
            'pending_tr' => TransactionRequest::where('status', 'waiting')->count(),
            'paid_tr' => TransactionRequest::where('status', 'paid')->count(),
        ];
    }

    private function getDepartmentStats()
    {
        return DB::table('Pengajuan as p')
            ->join('Karyawan as k', 'p.requester_id', '=', 'k.id')
            ->join('Department as d', 'k.department_id', '=', 'd.id')
            ->select(
                'd.nama as department_name',
                'd.id as department_id',
                DB::raw('COUNT(p.id) as total_pengajuan'),
                DB::raw('SUM(CASE WHEN p.status_pengajuan = "completed" THEN p.nominal_pengajuan ELSE 0 END) as total_nominal'),
                DB::raw('COUNT(CASE WHEN p.status_pengajuan = "completed" THEN 1 END) as completed'),
                DB::raw('COUNT(CASE WHEN p.status_pengajuan = "proses" THEN 1 END) as pending'),
                DB::raw('COUNT(CASE WHEN p.status_pengajuan = "rejected" THEN 1 END) as rejected'),
                DB::raw('ROUND(AVG(DATEDIFF(p.updated_at, p.created_at)), 1) as avg_processing_days')
            )
            ->groupBy('d.id', 'd.nama')
            ->orderBy('total_pengajuan', 'desc')
            ->get();
    }

    private function getTopRequesters()
    {
        return DB::table('Pengajuan as p')
            ->join('Karyawan as k', 'p.requester_id', '=', 'k.id')
            ->join('Department as d', 'k.department_id', '=', 'd.id')
            ->select(
                'k.nama as requester_name',
                'k.id as requester_id',
                'd.nama as department_name',
                DB::raw('COUNT(p.id) as total_pengajuan'),
                DB::raw('SUM(CASE WHEN p.status_pengajuan = "completed" THEN p.nominal_pengajuan ELSE 0 END) as total_nominal'),
                DB::raw('COUNT(CASE WHEN p.status_pengajuan = "completed" THEN 1 END) as completed'),
                DB::raw('ROUND((COUNT(CASE WHEN p.status_pengajuan = "completed" THEN 1 END) / COUNT(p.id)) * 100, 1) as approval_rate')
            )
            ->groupBy('k.id', 'k.nama', 'd.nama')
            ->orderBy('total_pengajuan', 'desc')
            ->limit(10)
            ->get();
    }

    private function getRecentActivities()
    {
        return [
            'recent_pengajuan' => Pengajuan::with(['requester.department', 'kategoriPengajuan'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
                
            'recent_approvals' => ProgressApproval::with(['pengajuan.requester', 'approver', 'pengajuan.kategoriPengajuan'])
                ->where('status', '!=', 'pending')
                ->orderBy('tanggal_approval', 'desc')
                ->limit(10)
                ->get(),
                
            'recent_settlements' => Settlement::with(['pengajuan.requester', 'pengajuan.kategoriPengajuan'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
        ];
    }

    private function getPendingItems()
    {
        return [
            'pending_approvals' => ProgressApproval::with(['pengajuan.requester', 'approver', 'pengajuan.kategoriPengajuan'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get(),
                
            'pending_settlements' => Settlement::with(['pengajuan.requester'])
                ->where('status_settlement', 'draft')
                ->orderBy('created_at', 'asc')
                ->get(),
                
            'pending_transactions' => TransactionRequest::with(['pengajuan.requester', 'settlement.pengajuan.requester'])
                ->where('status', 'waiting')
                ->orderBy('created_at', 'asc')
                ->get()
        ];
    }

    private function getChartData()
    {
        // Monthly trend (12 months) - hanya completed kecuali rejected
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            // Pengajuan completed (untuk nominal asli dan revisi)
            $completedPengajuan = Pengajuan::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status_pengajuan', 'completed');
                
            // Pengajuan rejected (untuk filter rejected)
            $rejectedPengajuan = Pengajuan::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status_pengajuan', 'rejected');
            
            $pengajuanCount = $completedPengajuan->count();
            $rejectedCount = $rejectedPengajuan->count();
            
            // Nominal asli (sebelum revisi) - gunakan nominal_sblm_revisi jika ada, atau nominal_pengajuan
            $nominalOriginal = $completedPengajuan->get()->sum(function($pengajuan) {
                return $pengajuan->nominal_sblm_revisi ?: $pengajuan->nominal_pengajuan;
            });
            
            // Nominal revisi (setelah intervensi finance)
            $nominalRevised = $completedPengajuan->sum('nominal_pengajuan');
            
            // Nominal rejected
            $rejectedNominal = $rejectedPengajuan->sum('nominal_pengajuan');
                
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'pengajuan_count' => $pengajuanCount,
                'rejected_count' => $rejectedCount,
                'nominal_original' => $nominalOriginal,
                'nominal_revised' => $nominalRevised,
                'rejected_nominal' => $rejectedNominal
            ];
        }
    
        // Status Distribution (tetap sama)
        $statusData = [
            ['label' => 'Proses', 'value' => Pengajuan::where('status_pengajuan', 'proses')->count(), 'color' => '#ffc107'],
            ['label' => 'Ditolak', 'value' => Pengajuan::where('status_pengajuan', 'rejected')->count(), 'color' => '#dc3545'],
            ['label' => 'Selesai', 'value' => Pengajuan::where('status_pengajuan', 'completed')->count(),'color' => '#198754']
        ];
    
        // Department Distribution (tetap sama)
        $departmentData = DB::table('Pengajuan as p')
            ->join('Karyawan as k', 'p.requester_id', '=', 'k.id')
            ->join('Department as d', 'k.department_id', '=', 'd.id')
            ->select('d.nama as department', DB::raw('COUNT(*) as count'))
            ->groupBy('d.id', 'd.nama')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    
        return [
            'monthly' => $monthlyData,
            'status' => $statusData,
            'department' => $departmentData
        ];
    }

    private function getFinancialSummary()
    {
        $currentYear = Carbon::now()->year;
        
        return [
            'total_budget_requested' => Pengajuan::sum('nominal_sblm_revisi'),
            'total_budget_approved' => Pengajuan::where('status_pengajuan', 'completed')->sum('nominal_pengajuan'),
            'total_actual_spent' => Settlement::sum('total_actual'),
            'total_pending_amount' => Pengajuan::where('status_pengajuan', 'proses')->sum('nominal_pengajuan'),
            
            // This year
            'ytd_requested' => Pengajuan::whereYear('created_at', $currentYear)->sum('nominal_pengajuan'),
            'ytd_approved' => Pengajuan::whereYear('created_at', $currentYear)->where('status_pengajuan', 'approved')->sum('nominal_pengajuan'),
            'ytd_spent' => Settlement::whereYear('created_at', $currentYear)->sum('total_actual'),
            
            // Savings/Overspend
            'total_savings' => Settlement::where('selisih', '<', 0)->sum('selisih'),
            'total_overspend' => Settlement::where('selisih', '>', 0)->sum('selisih'),
        ];
    }

    private function getCategoryPerformance()
    {
        return DB::table('Pengajuan as p')
            ->join('KategoriPengajuan as kp', 'p.kategori_pengajuan_id', '=', 'kp.id')
            ->select(
                'kp.nama as category_name',
                'kp.icon',
                'kp.warna',
                DB::raw('COUNT(p.id) as total_pengajuan'),
                DB::raw('SUM(CASE WHEN p.status_pengajuan = "completed" THEN p.nominal_pengajuan ELSE 0 END) as total_nominal'),
                DB::raw('AVG(p.nominal_pengajuan) as avg_nominal'),
                DB::raw('COUNT(CASE WHEN p.status_pengajuan = "approved" THEN 1 END) as approved'),
                DB::raw('ROUND((COUNT(CASE WHEN p.status_pengajuan = "approved" THEN 1 END) / COUNT(p.id)) * 100, 1) as approval_rate'),
                DB::raw('ROUND(AVG(DATEDIFF(p.updated_at, p.created_at)), 1) as avg_processing_days')
            )
            ->groupBy('kp.id', 'kp.nama', 'kp.icon', 'kp.warna')
            ->orderBy('total_pengajuan', 'desc')
            ->get();
    }

    private function getMonthlyTrends()
    {
        $trends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $data = [
                'month' => $date->format('M Y'),
                'pengajuan_created' => Pengajuan::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
                'pengajuan_approved' => Pengajuan::whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                    ->where('status_pengajuan', 'approved')->count(),
                'settlements_created' => Settlement::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
                'amount_requested' => Pengajuan::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('nominal_pengajuan'),
                'amount_approved' => Pengajuan::whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                    ->where('status_pengajuan', 'approved')->sum('nominal_pengajuan')
            ];
            
            $trends[] = $data;
        }
        
        return $trends;
    }

    // Additional methods for AJAX calls
    public function getDepartmentDetail($departmentId)
    {
        $department = Department::with(['karyawans'])->find($departmentId);
        
        $stats = [
            'total_employees' => $department->karyawans->count(),
            'active_employees' => $department->karyawans->where('status', 'active')->count(),
            'total_pengajuan' => Pengajuan::whereHas('requester', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })->count(),
            'total_nominal' => Pengajuan::whereHas('requester', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })->sum('nominal_pengajuan'),
        ];
        
        $employees = Karyawan::where('department_id', $departmentId)
            ->withCount('pengajuan')
            ->with('roleLevel')
            ->get();
            
        return response()->json([
            'department' => $department,
            'stats' => $stats,
            'employees' => $employees
        ]);
    }

    public function getEmployeeDetail($employeeId)
    {
        $employee = Karyawan::with(['department', 'roleLevel', 'atasan'])
            ->withCount(['pengajuan'])
            ->find($employeeId);
            
        $pengajuanStats = [
            'total' => $employee->pengajuan()->count(),
            'approved' => $employee->pengajuan()->where('status_pengajuan', 'approved')->count(),
            'pending' => $employee->pengajuan()->where('status_pengajuan', 'proses')->count(),
            'rejected' => $employee->pengajuan()->where('status_pengajuan', 'rejected')->count(),
            'total_nominal' => $employee->pengajuan()->sum('nominal_pengajuan')
        ];
        
        $recentPengajuan = $employee->pengajuan()
            ->with('kategoriPengajuan')
            ->latest()
            ->limit(5)
            ->get();
            
        return response()->json([
            'employee' => $employee,
            'pengajuan_stats' => $pengajuanStats,
            'recent_pengajuan' => $recentPengajuan
        ]);
    }
}