<?php

namespace App\Http\Controllers;

use App\Models\IncomingActivityLog;
use App\Models\QcActivityLog;
use App\Models\ReservationActivityLog;
use App\Models\ReturnActivityLog;
use App\Models\WarehouseActivityLog;
use App\Models\ActivityLog;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index()
    {
        $incomingLogs = IncomingActivityLog::with(['user.role', 'material'])->get();
        $qcLogs = QcActivityLog::with(['user.role', 'material'])->get();
        $reservationLogs = ReservationActivityLog::with(['user.role', 'material'])->get();
        $returnLogs = ReturnActivityLog::with(['user.role', 'material'])->get();
        $warehouseLogs = WarehouseActivityLog::with(['user.role', 'material'])->get();
        
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
            ->concat($this->mapMasterDataLogs($masterDataLogs))
            ->sortByDesc('timestamp')
            ->values();

        return Inertia::render('RiwayatAktivitas', [
            'activities' => $activities,
        ]);
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

            if ($log->material_id && $log->material) {
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