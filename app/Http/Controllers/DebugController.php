<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryStock;
use App\Models\IncomingGood;

class DebugController extends Controller
{
    public function checkRejectedInventory()
    {
        // Get ALL rejected stocks
        $rejectedStocks = InventoryStock::where('status', 'REJECTED')
            ->with(['material', 'bin', 'incomingGood'])
            ->get()
            ->map(function($stock) {
                return [
                    'id' => $stock->id,
                    'material_code' => $stock->material ? $stock->material->kode_item : 'NULL',
                    'material_name' => $stock->material ? $stock->material->nama_material : 'NULL',
                    'bin_code' => $stock->bin ? $stock->bin->bin_code : 'NULL',
                    'qty' => $stock->qty_on_hand,
                    'gr_id' => $stock->gr_id ?? 'NULL',
                    'incoming_number' => $stock->incomingGood ? $stock->incomingGood->incoming_number : 'NULL',
                    'starts_with_QRT' => $stock->bin ? (str_starts_with($stock->bin->bin_code, 'QRT-') ? 'YES' : 'NO') : 'NULL BIN'
                ];
            });
        
        // Get shipments with rejected materials NOT in QRT bins
        $rejectedShipmentsQuery = IncomingGood::whereHas('inventoryStocks', function ($query) {
            $query->where('status', 'REJECTED')
                ->whereHas('bin', function ($q) {
                    $q->where('bin_code', 'NOT LIKE', 'QRT-%');
                });
        })
        ->select('incoming_number', 'no_surat_jalan')
        ->distinct()
        ->get();
        
        // Get ALL bins that contain rejected materials
        $binsWithRejected = InventoryStock::where('status', 'REJECTED')
            ->with('bin')
            ->get()
            ->pluck('bin.bin_code')
            ->unique()
            ->values();
        
        return response()->json([
            'total_rejected_stocks' => $rejectedStocks->count(),
            'rejected_stocks_detail' => $rejectedStocks,
            'bins_containing_rejected' => $binsWithRejected,
            'qualified_shipments_count' => $rejectedShipmentsQuery->count(),
            'qualified_shipments' => $rejectedShipmentsQuery,
            'explanation' => 'For shipments to appear in dropdown, rejected materials must be in bins NOT starting with QRT-'
        ]);
    }
}
