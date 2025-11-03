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

class ActivityLogController extends Controller
{
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
                'bin_from' => $log->bin_from,
                'bin_to' => $log->bin_to,
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
                'bin_from' => $log->bin_from,
                'bin_to' => $log->bin_to,
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
                'bin_from' => $log->bin_from,
                'bin_to' => $log->bin_to,
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
                'bin_from' => $log->bin_from,
                'bin_to' => $log->bin_to,
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
                'bin_from' => $log->bin_from,
                'bin_to' => $log->bin_to,
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