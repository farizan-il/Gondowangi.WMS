<?php

namespace App\Http\Controllers;

use App\Models\IncomingActivityLog;
use App\Models\QcActivityLog;
use App\Models\ReservationActivityLog;
use App\Models\ReturnActivityLog;
use App\Models\WarehouseActivityLog;
use App\Models\ActivityLog;
use App\Models\StockMovement;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function dashboard()
    {
        // 1. Total Activities (Today, Week, Month)
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Helper to count across all log tables
        $countLogs = function ($queryCallback) {
            return IncomingActivityLog::where($queryCallback)->count() +
                   QcActivityLog::where($queryCallback)->count() +
                   ReservationActivityLog::where($queryCallback)->count() +
                   ReturnActivityLog::where($queryCallback)->count() +
                   WarehouseActivityLog::where($queryCallback)->count() +
                   StockMovement::where($queryCallback)->count() +
                   ActivityLog::where($queryCallback)->count();
        };

        $stats = [
            'total_today' => $countLogs(fn($q) => $q->whereDate('created_at', $today)),
            'total_week' => $countLogs(fn($q) => $q->where('created_at', '>=', $startOfWeek)),
            'total_month' => $countLogs(fn($q) => $q->where('created_at', '>=', $startOfMonth)),
        ];

        // 2. Active Users Today (Unique users who performed an action)
        // Note: This is a bit expensive to query across all tables, so we'll approximate or use a simpler approach
        // For now, let's just count distinct user_ids from the main ActivityLog as a proxy, or union all.
        // Optimization: Just check ActivityLog and StockMovement for now as they cover most.
        $activeUsersCount = ActivityLog::whereDate('created_at', $today)->distinct('user_id')->count('user_id');
        
        // 3. Module Distribution (Pie Chart)
        // We can count total rows in each table for "All Time" or "This Month"
        $moduleStats = [
            'Incoming' => IncomingActivityLog::count(),
            'QC' => QcActivityLog::count(),
            'Reservation' => ReservationActivityLog::count(),
            'Return' => ReturnActivityLog::count(),
            'Warehouse' => WarehouseActivityLog::count(),
            'Stock Movement' => StockMovement::count(),
            'Master Data' => ActivityLog::count(),
        ];

        // 4. Hourly Activity (Line Chart) - Last 24 Hours
        // We will use ActivityLog as a representative sample or union all if needed.
        // For performance, let's use ActivityLog + StockMovement
        $hourlyStats = ActivityLog::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->toArray();
            
        // Fill missing hours with 0
        $chartData = [];
        for ($i = 0; $i < 24; $i++) {
            $chartData[$i] = $hourlyStats[$i] ?? 0;
        }

        // 5. Top Users (Bar Chart)
        $topUsers = ActivityLog::select('user_id', DB::raw('count(*) as total'))
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($log) {
                return [
                    'name' => $log->user->nama_lengkap ?? 'Unknown',
                    'count' => $log->total
                ];
            });

        // 6. Online Users (Last 5 minutes)
        $onlineUsers = \App\Models\User::where('last_seen_at', '>=', Carbon::now()->subMinutes(5))
            ->orderByDesc('last_seen_at')
            ->get(['id', 'name', 'role_id', 'last_seen_at', 'email']);

        // 7. Recent Activities Feed (Merged from all sources)
        // Helper to fetch latest 5 from a model
        $fetchLatest = function($model) {
            return $model::with(['user:id,name', 'material:id,kode_item,nama_material'])
                ->latest()
                ->limit(5)
                ->get();
        };

        $recentLogs = collect()
            ->concat($fetchLatest(IncomingActivityLog::class)->map(fn($l) => $this->formatLogForFeed($l, 'Incoming')))
            ->concat($fetchLatest(QcActivityLog::class)->map(fn($l) => $this->formatLogForFeed($l, 'QC')))
            ->concat($fetchLatest(ReservationActivityLog::class)->map(fn($l) => $this->formatLogForFeed($l, 'Reservation')))
            ->concat($fetchLatest(ReturnActivityLog::class)->map(fn($l) => $this->formatLogForFeed($l, 'Return')))
            ->concat($fetchLatest(WarehouseActivityLog::class)->map(fn($l) => $this->formatLogForFeed($l, 'Warehouse')))
            ->concat($fetchLatest(StockMovement::class)->map(fn($l) => $this->formatLogForFeed($l, 'Stock Movement')))
            ->concat($fetchLatest(ActivityLog::class)->map(fn($l) => $this->formatLogForFeed($l, 'Master Data')))
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        return Inertia::render('ITDashboard', [
            'stats' => $stats,
            'activeUsers' => $activeUsersCount,
            'onlineUsers' => $onlineUsers,
            'moduleStats' => $moduleStats,
            'hourlyStats' => array_values($chartData),
            'topUsers' => $topUsers,
            'recentActivities' => $recentLogs,
        ]);
    }

    private function formatLogForFeed($log, $module)
    {
        // Handle StockMovement which might use 'executedBy' instead of 'user'
        $user = $log->user ?? $log->executedBy ?? null;
        $userName = $user ? ($user->name ?? $user->nama_lengkap ?? 'System') : 'System';
        
        // Handle Action/Description
        $action = $log->action ?? $log->movement_type ?? 'Unknown';
        $desc = $log->description ?? $log->remarks ?? '';

        if ($module === 'Stock Movement') {
            $desc = "{$log->qty} {$log->uom} moved";
        }
        
        return [
            'id' => $log->id,
            'module' => $module,
            'user' => $userName,
            'action' => $action,
            'description' => $desc,
            'created_at' => $log->created_at->diffForHumans(), // Human readable time
            'timestamp' => $log->created_at, // For sorting
        ];

    }

    public function index()
    {
        $incomingLogs = IncomingActivityLog::with(['user.role', 'material'])->get();
        $qcLogs = QcActivityLog::with(['user.role', 'material'])->get();
        $reservationLogs = ReservationActivityLog::with(['user.role', 'material'])->get();
        $returnLogs = ReturnActivityLog::with(['user.role', 'material'])->get();
        $warehouseLogs = WarehouseActivityLog::with(['user.role', 'material'])->get();
        
        $stockMovementLogs = StockMovement::with([
            'executedBy.role', // Ambil user dan role
            'material',
            'fromBin',
            'toBin',
        ])
        ->orderBy('movement_date', 'desc')
        ->get();

        // Tambahkan Master Data Activity Logs
        $masterDataLogs = ActivityLog::with([
            'user.role', 
            'material', 
            'supplier', 
            'warehouseBin.zone'
        ])->get();

        $activities = collect([])
            ->concat($this->mapIncomingLogs($incomingLogs))
            ->concat($this->mapQcLogs($qcLogs))
            ->concat($this->mapReservationLogs($reservationLogs))
            ->concat($this->mapReturnLogs($returnLogs))
            ->concat($this->mapWarehouseLogs($warehouseLogs))
            // --- BARU: Gabungkan StockMovement Logs ---
            ->concat($this->mapStockMovementLogs($stockMovementLogs))
            // ----------------------------------------
            ->concat($this->mapMasterDataLogs($masterDataLogs))
            ->sortByDesc('timestamp')
            ->values();

        return Inertia::render('RiwayatAktivitas', [
            'activities' => $activities,
        ]);
    }

    private function mapStockMovementLogs($logs)
    {
        return $logs->map(function ($log) {
            
            // Tentukan aksi dan deskripsi yang lebih detail
            $action = $log->movement_type;
            $binFromCode = $log->fromBin->bin_code ?? 'N/A';
            $binToCode = $log->toBin->bin_code ?? 'N/A';
            
            if ($log->movement_type === 'B2B') {
                $description = "Transfer Bin-to-Bin: Pindah {$log->qty} {$log->uom} dari {$binFromCode} ke {$binToCode}.";
            } elseif ($log->movement_type === 'GR') {
                $description = "Penerimaan Barang (GR): Stok {$log->qty} {$log->uom} masuk ke {$binToCode}.";
            } elseif ($log->movement_type === 'PUTAWAY') {
                $description = "Putaway: Stok {$log->qty} {$log->uom} dipindahkan dari {$binFromCode} ke lokasi penyimpanan permanen {$binToCode}.";
            } else {
                $description = "Pergerakan Stok {$log->movement_number}: Tipe {$log->movement_type} ({$log->qty} {$log->uom}).";
            }
            
            return [
                'id' => $log->id,
                'timestamp' => $log->movement_date->toDateTimeString(),
                'user' => $log->executedBy->nama_lengkap ?? 'System',
                'role' => $log->executedBy->role->role_name ?? 'N/A',
                'module' => 'Warehouse - Stock Movement', // Kategori yang jelas
                'action' => $action,
                'sku_code' => $log->material->kode_item ?? 'N/A',
                'sku_name' => $log->material->nama_material ?? 'N/A',
                'lot_no' => $log->batch_lot ?? '-',
                'qty_before' => $log->qty_before ?? 0, // StockMovement mungkin tidak punya qty_before/after
                'qty_after' => $log->qty, // Menggunakan qty sebagai qty_after movement
                'bin_from' => $binFromCode,
                'bin_to' => $binToCode,
                'reference_no' => $log->reference_type . ' ' . $log->movement_number,
                'device' => 'N/A (Backend Log)',
                'ip_address' => 'N/A',
                'remarks' => $description, // Menggunakan deskripsi yang dibuat
                'exp_date' => null, // StockMovement mungkin tidak mencatat exp_date
                'old_value' => null,
                'new_value' => null,
            ];
        });
    }

    private function mapIncomingLogs($logs)
    {
        return $logs->map(function ($log) {
            // Convert bin_from and bin_to from ID to bin_code if numeric
            $binFrom = $log->bin_from;
            $binTo = $log->bin_to;
            
            if (is_numeric($binFrom)) {
                $bin = \App\Models\WarehouseBin::find($binFrom);
                $binFrom = $bin ? $bin->bin_code : $binFrom;
            }
            
            if (is_numeric($binTo)) {
                $bin = \App\Models\WarehouseBin::find($binTo);
                $binTo = $bin ? $bin->bin_code : $binTo;
            }
            
            return [
                'id' => $log->id,
                'timestamp' => $log->created_at->toDateTimeString(),
                'user' => $log->user->nama_lengkap ?? 'System',
                'role' => $log->user->role->role_name ?? 'N/A',
                'module' => 'Incoming Goods',
                'action' => $log->action,
                'sku_code' => $log->material->kode_item ?? 'N/A',
                'sku_name' => $log->material->nama_material ?? 'N/A',
                'lot_no' => $log->batch_lot,
                'qty_before' => $log->qty_before,
                'qty_after' => $log->qty_after,
                'bin_from' => $binFrom,
                'bin_to' => $binTo,
                'reference_no' => $log->reference_document,
                'device' => $log->device_info,
                'ip_address' => $log->ip_address,
                'remarks' => $log->description,
                'exp_date' => $log->exp_date,
            ];
        });
    }

    private function mapQcLogs($logs)
    {
        return $logs->map(function ($log) {
            // Convert bin_from and bin_to from ID to bin_code if numeric
            $binFrom = $log->bin_from;
            $binTo = $log->bin_to;
            
            if (is_numeric($binFrom)) {
                $bin = \App\Models\WarehouseBin::find($binFrom);
                $binFrom = $bin ? $bin->bin_code : $binFrom;
            }
            
            if (is_numeric($binTo)) {
                $bin = \App\Models\WarehouseBin::find($binTo);
                $binTo = $bin ? $bin->bin_code : $binTo;
            }
            
            return [
                'id' => $log->id,
                'timestamp' => $log->created_at->toDateTimeString(),
                'user' => $log->user->nama_lengkap ?? 'System',
                'role' => $log->user->role->role_name ?? 'N/A',
                'module' => 'Quality Control',
                'action' => $log->action,
                'sku_code' => $log->material->kode_item ?? 'N/A',
                'sku_name' => $log->material->nama_material ?? 'N/A',
                'lot_no' => $log->batch_lot,
                'qty_before' => $log->qty_before,
                'qty_after' => $log->qty_after,
                'bin_from' => $binFrom,
                'bin_to' => $binTo,
                'reference_no' => $log->reference_document,
                'device' => $log->device_info,
                'ip_address' => $log->ip_address,
                'remarks' => $log->description,
                'exp_date' => $log->exp_date,
            ];
        });
    }

    private function mapReservationLogs($logs)
    {
        return $logs->map(function ($log) {
            // Convert bin_from and bin_to from ID to bin_code if numeric
            $binFrom = $log->bin_from;
            $binTo = $log->bin_to;
            
            if (is_numeric($binFrom)) {
                $bin = \App\Models\WarehouseBin::find($binFrom);
                $binFrom = $bin ? $bin->bin_code : $binFrom;
            }
            
            if (is_numeric($binTo)) {
                $bin = \App\Models\WarehouseBin::find($binTo);
                $binTo = $bin ? $bin->bin_code : $binTo;
            }
            
            return [
                'id' => $log->id,
                'timestamp' => $log->created_at->toDateTimeString(),
                'user' => $log->user->nama_lengkap ?? 'System',
                'role' => $log->user->role->role_name ?? 'N/A',
                'module' => 'Reservation',
                'action' => $log->action,
                'sku_code' => $log->material->kode_item ?? 'N/A',
                'sku_name' => $log->material->nama_material ?? 'N/A',
                'lot_no' => $log->batch_lot,
                'qty_before' => $log->qty_before,
                'qty_after' => $log->qty_after,
                'bin_from' => $binFrom,
                'bin_to' => $binTo,
                'reference_no' => $log->reference_document,
                'device' => $log->device_info,
                'ip_address' => $log->ip_address,
                'remarks' => $log->description,
                'exp_date' => $log->exp_date,
            ];
        });
    }

    private function mapReturnLogs($logs)
    {
        return $logs->map(function ($log) {
            // Convert bin_from and bin_to from ID to bin_code if numeric
            $binFrom = $log->bin_from;
            $binTo = $log->bin_to;
            
            if (is_numeric($binFrom)) {
                $bin = \App\Models\WarehouseBin::find($binFrom);
                $binFrom = $bin ? $bin->bin_code : $binFrom;
            }
            
            if (is_numeric($binTo)) {
                $bin = \App\Models\WarehouseBin::find($binTo);
                $binTo = $bin ? $bin->bin_code : $binTo;
            }
            
            return [
                'id' => $log->id,
                'timestamp' => $log->created_at->toDateTimeString(),
                'user' => $log->user->nama_lengkap ?? 'System',
                'role' => $log->user->role->role_name ?? 'N/A',
                'module' => 'Return',
                'action' => $log->action,
                'sku_code' => $log->material->kode_item ?? 'N/A',
                'sku_name' => $log->material->nama_material ?? 'N/A',
                'lot_no' => $log->batch_lot,
                'qty_before' => $log->qty_before,
                'qty_after' => $log->qty_after,
                'bin_from' => $binFrom,
                'bin_to' => $binTo,
                'reference_no' => $log->reference_document,
                'device' => $log->device_info,
                'ip_address' => $log->ip_address,
                'remarks' => $log->description,
                'exp_date' => $log->exp_date,
            ];
        });
    }

    private function mapWarehouseLogs($logs)
    {
        return $logs->map(function ($log) {
            // Convert bin_from and bin_to from ID to bin_code if numeric
            $binFrom = $log->bin_from;
            $binTo = $log->bin_to;
            
            if (is_numeric($binFrom)) {
                $bin = \App\Models\WarehouseBin::find($binFrom);
                $binFrom = $bin ? $bin->bin_code : $binFrom;
            }
            
            if (is_numeric($binTo)) {
                $bin = \App\Models\WarehouseBin::find($binTo);
                $binTo = $bin ? $bin->bin_code : $binTo;
            }
            
            return [
                'id' => $log->id,
                'timestamp' => $log->created_at->toDateTimeString(),
                'user' => $log->user->nama_lengkap ?? 'System',
                'role' => $log->user->role->role_name ?? 'N/A',
                'module' => 'Warehouse',
                'action' => $log->action,
                'sku_code' => $log->material->kode_item ?? 'N/A',
                'sku_name' => $log->material->nama_material ?? 'N/A',
                'lot_no' => $log->batch_lot,
                'qty_before' => $log->qty_before,
                'qty_after' => $log->qty_after,
                'bin_from' => $binFrom,
                'bin_to' => $binTo,
                'reference_no' => $log->reference_document,
                'device' => $log->device_info,
                'ip_address' => $log->ip_address,
                'remarks' => $log->description,
                'exp_date' => $log->exp_date,
            ];
        });
    }

    private function mapMasterDataLogs($logs)
    {
        return $logs->map(function ($log) {
            // Tentukan module berdasarkan foreign key yang terisi
            $module = 'Master Data';
            $entityCode = 'N/A';
            $entityName = 'N/A';

            if (str_contains($log->action, 'Putaway') || str_contains($log->action, 'TO')) {
                $module = 'Warehouse - Stock Movement';
                
                // Ambil detail material jika ada
                if ($log->material_id && $log->material) {
                    $entityCode = $log->material->kode_item;
                    $entityName = $log->material->nama_material;
                } else {
                    // Jika tidak ada material, ambil dari deskripsi
                    $entityName = 'Transfer Order';
                }
            }

            elseif ($log->material_id && $log->material) {
                $module = 'Master Data - SKU/Material';
                $entityCode = $log->material->kode_item;
                $entityName = $log->material->nama_material;
            } elseif ($log->supplier_id && $log->supplier) {
                $module = 'Master Data - Supplier';
                $entityCode = $log->supplier->kode_supplier;
                $entityName = $log->supplier->nama_supplier;
            } elseif ($log->warehousebin_id && $log->warehouseBin) {
                $module = 'Master Data - Bin Location';
                $entityCode = $log->warehouseBin->bin_code;
                $entityName = $log->warehouseBin->bin_name . ' (' . ($log->warehouseBin->zone->zone_name ?? 'N/A') . ')';
            } elseif ($log->user_id_target) {
                $module = 'Master Data - User';
                // Parse dari description atau old/new value
                $description = $log->description ?? '';
                if (preg_match('/User: (.+)/', $description, $matches)) {
                    $entityName = $matches[1];
                }
            }

            return [
                'id' => $log->id,
                'timestamp' => $log->created_at->toDateTimeString(),
                'user' => $log->user->nama_lengkap ?? 'System',
                'role' => $log->user->role->role_name ?? 'N/A',
                'module' => $module,
                'action' => $log->action,
                'sku_code' => $entityCode,
                'sku_name' => $entityName,

                'lot_no' => $log->batch_lot ?? '-',
                'qty_before' => $log->qty_before ?? 0,
                'qty_after' => $log->qty_after ?? 0,
                'bin_from' => $log->bin_from ?? '-',
                'bin_to' => $log->bin_to ?? '-',
                'reference_no' => $log->reference_document ?? '-',

                'device' => $log->device_info,
                'ip_address' => $log->ip_address,
                'remarks' => $log->description,
                'exp_date' => $log->exp_date,
                'old_value' => $log->old_value,
                'new_value' => $log->new_value,
            ];
        });
    }
}