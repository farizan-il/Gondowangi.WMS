<?php

namespace App\Http\Controllers;

use App\Models\IncomingActivityLog;
use App\Models\QcActivityLog;
use App\Models\ReservationActivityLog;
use App\Models\ReturnActivityLog;
use App\Models\WarehouseActivityLog;
use Illuminate\Http\Request;
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

        $activities = collect([])
            ->concat($incomingLogs)
            ->concat($qcLogs)
            ->concat($reservationLogs)
            ->concat($returnLogs)
            ->concat($warehouseLogs)
            ->sortByDesc('created_at')
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'timestamp' => $log->created_at->toDateTimeString(),
                    'user' => $log->user->nama_lengkap,
                    'role' => $log->user->role->role_name,
                    'module' => $log->getTable(),
                    'action' => $log->action,
                    'sku_code' => $log->material->kode_item,
                    'sku_name' => $log->material->nama_material,
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

        return Inertia::render('RiwayatAktivitas', [
            'activities' => $activities,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
