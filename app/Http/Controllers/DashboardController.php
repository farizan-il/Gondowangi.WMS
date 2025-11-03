<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // PENTING: Gunakan 'material_id' sebagai local key untuk relasi movements
        $inventoryItems = InventoryStock::with([
            'material', 
            'bin', 
            'movements.executedBy', 
            'movements.fromBin', 
            'movements.toBin'
        ])->get()->map(function ($item) {
            
            // Logika pemrosesan history (sama seperti sebelumnya)
            $history = $item->movements->map(function ($movement) {
                // ... (Logika mapping history) ...
                $detail = '';
                if ($movement->movement_type === 'GR') {
                    $detail = "Penerimaan $movement->qty $movement->uom di lokasi " . ($movement->toBin->bin_code ?? 'N/A') . ". Ref: " . ($movement->reference_document ?? 'N/A');
                } elseif ($movement->movement_type === 'B2B') {
                    $detail = "Transfer Bin to Bin: Pindah $movement->qty $movement->uom dari " . ($movement->fromBin->bin_code ?? 'N/A') . " ke " . ($movement->toBin->bin_code ?? 'N/A') . ". Ref: " . ($movement->reference_document ?? 'N/A');
                } else {
                    $detail = "$movement->movement_type $movement->qty $movement->uom. Lokasi: " . ($movement->toBin->bin_code ?? 'N/A') . ". Ref: " . ($movement->reference_document ?? 'N/A');
                }

                return [
                    'id' => $movement->id,
                    'date' => $movement->movement_date->toISOString(),
                    'action' => $movement->movement_type,
                    'detail' => $detail,
                    'user' => $movement->executedBy->name ?? 'System',
                ];
            });

            // LOGIKA PUT AWAY CERDAS BARU
            $requiresPutAway = false;
            $binCode = $item->bin->bin_code ?? '';
            
            if ($item->status === 'RELEASED' && str_starts_with($binCode, 'QRT-')) {
                $requiresPutAway = true;
            }
            
            return [
                'id' => $item->id,
                'type' => $item->material->kategori,
                'kode' => $item->material->kode_item,
                'nama' => $item->material->nama_material,
                'lot' => $item->batch_lot,
                'lokasi' => $item->bin ? $item->bin->bin_code : 'N/A',
                'qty' => $item->qty_on_hand,
                'uom' => $item->uom,
                'expiredDate' => $item->exp_date->toDateString(),
                'status' => $item->status,
                'history' => $history,
                'qr_data' => json_encode([
                    'id' => $item->id,
                    'kode' => $item->material->kode_item,
                    'lot' => $item->batch_lot,
                    'status' => $item->status,
                ]),
                'qr_type' => $item->status,
                'requiresPutAway' => $requiresPutAway, // BARU
            ];
        });

        // Hitung total item yang membutuhkan Put Away untuk Notifikasi Global
        $putAwayCount = $inventoryItems->filter(fn($item) => $item['requiresPutAway'])->count();
        $expiredCount = InventoryStock::where('exp_date', '<=', now())->count();
        $expiringSoonCount = InventoryStock::where('exp_date', '<=', now()->addDays(30))->where('exp_date', '>', now())->count();

        $alerts = [];
        // Notifikasi Cerdas Put Away
        if ($putAwayCount > 0) {
             $alerts[] = ['id' => '0', 'type' => 'info', 'message' => "💡 **$putAwayCount item** status Released tapi masih di Bin Karantina (`QRT-*`). Segera lakukan **Put Away**."];
        }
        
        if ($expiringSoonCount > 0) {
            $alerts[] = ['id' => '1', 'type' => 'warning', 'message' => "$expiringSoonCount item akan expired dalam 30 hari"];
        }
        if ($expiredCount > 0) {
            $alerts[] = ['id' => '2', 'type' => 'error', 'message' => "$expiredCount item sudah expired!"];
        }

        return Inertia::render('Dashboard', [
            'materialItems' => $inventoryItems,
            'alerts' => $alerts,
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
