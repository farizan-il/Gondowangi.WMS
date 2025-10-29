<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\TransferOrder;
use App\Models\TransferOrderItem;
use App\Models\WarehouseBin;
use App\Models\InventoryStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Traits\ActivityLogger;

class PutawayTransferController extends Controller
{
    use ActivityLogger;
    
    public function index()
    {
        $transferOrders = TransferOrder::with([
            'warehouse',
            'items.material',
            'items.sourceBin',
            'items.destinationBin',
            'createdBy',
            'executedBy'
        ])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($to) {
            return [
                'id' => $to->id,
                'toNumber' => $to->to_number,
                'creationDate' => $to->creation_date?->format('Y-m-d H:i:s'),
                'warehouse' => $to->warehouse 
                    ? $to->warehouse->warehouse_code . ' - ' . $to->warehouse->warehouse_name 
                    : 'N/A',
                'type' => $to->transaction_type ?? 'N/A',
                'status' => $to->status ?? 'Pending',
                'reservationNo' => $to->reservation_no,
                'items' => $to->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'itemCode' => $item->material?->kode_item ?? 'N/A',
                        'materialName' => $item->material?->nama_material ?? 'N/A',
                        'sourceBin' => $item->sourceBin?->bin_code ?? 'N/A',
                        'destBin' => $item->destinationBin?->bin_code ?? 'N/A',
                        'qty' => $item->qty_planned,
                        'actualQty' => $item->qty_actual,
                        'uom' => $item->uom,
                        'status' => $item->status,
                        'boxScanned' => (bool)$item->box_scanned,
                        'sourceBinScanned' => (bool)$item->source_bin_scanned,
                        'destBinScanned' => (bool)$item->dest_bin_scanned,
                    ];
                })->toArray()
            ];
        })->toArray();

        return Inertia::render('PutAwasTO', [
            'transferOrders' => $transferOrders
        ]);
    }

    public function getQcReleasedMaterials()
    {
        $materials = InventoryStock::with([
            'material',
            'warehouse',
            'bin'
        ])
        ->where('status', 'RELEASED')
        ->where('qty_available', '>', 0)
        ->whereHas('bin', function ($query) {
            $query->where('bin_code', 'LIKE', 'QRT-%');
        })
        ->get()
        ->map(function ($stock) {
            return [
                'itemCode' => $stock->material->kode_item,
                'materialName' => $stock->material->nama_material,
                'currentBin' => $stock->bin->bin_code,
                'qty' => $stock->qty_available,
                'uom' => $stock->uom,
                'batchLot' => $stock->batch_lot,
                'expDate' => $stock->exp_date,
                'stockId' => $stock->id,
                'selected' => false,
                'destinationBin' => ''
            ];
        });

        return response()->json($materials);
    }

    public function getAvailableBins(Request $request)
    {
        $itemCode = $request->query('itemCode');
        
        $bins = WarehouseBin::with(['zone', 'warehouse'])
            ->where('status', 'available')
            ->get()
            ->map(function ($bin) {
                return [
                    'code' => $bin->bin_code,
                    'name' => $bin->bin_name,
                    'warehouse' => $bin->warehouse->warehouse_code,
                    'zone' => $bin->zone->zone_code,
                    'capacity' => $bin->capacity,
                    'currentItems' => $bin->current_items,
                    'materials' => []
                ];
            });

        return response()->json($bins);
    }

    public function getBinDetails(Request $request)
    {
        $binCode = $request->query('binCode');
        
        $bin = WarehouseBin::with(['zone', 'warehouse'])
            ->where('bin_code', $binCode)
            ->first();
        
        if (!$bin) {
            return response()->json(['error' => 'Bin not found'], 404);
        }
        
        $materials = InventoryStock::with('material')
            ->where('bin_id', $bin->id)
            ->where('qty_on_hand', '>', 0)
            ->get()
            ->map(function ($stock) {
                return [
                    'itemCode' => $stock->material->kode_item,
                    'materialName' => $stock->material->nama_material,
                    'qty' => $stock->qty_on_hand,
                    'uom' => $stock->uom
                ];
            });
        
        return response()->json([
            'code' => $bin->bin_code,
            'name' => $bin->bin_name,
            'warehouse' => $bin->warehouse->warehouse_code,
            'zone' => $bin->zone->zone_code,
            'capacity' => $bin->capacity,
            'currentItems' => $bin->current_items,
            'materials' => $materials
        ]);
    }

    public function generateAutoPutaway(Request $request)
    {
        $request->validate([
            'materials' => 'required|array',
            'materials.*.stockId' => 'required|exists:inventory_stock,id',
            'materials.*.destinationBin' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $toNumber = $this->generateTONumber();
            
            $transferOrder = TransferOrder::create([
                'to_number' => $toNumber,
                'transaction_type' => 'Putaway - QC Release',
                'warehouse_id' => 1,
                'creation_date' => now(),
                'status' => 'Pending',
                'created_by' => Auth::id(),
                'notes' => 'Auto-generated from QC Released materials'
            ]);

            foreach ($request->materials as $material) {
                $stock = InventoryStock::find($material['stockId']);
                $sourceBin = $stock->bin;
                $destBin = WarehouseBin::where('bin_code', $material['destinationBin'])->firstOrFail();

                $transferOrder->items()->create([
                    'material_id' => $stock->material_id,
                    'batch_lot' => $stock->batch_lot,
                    'source_bin_id' => $sourceBin->id,
                    'destination_bin_id' => $destBin->id,
                    'qty_planned' => $material['qty'],
                    'uom' => $stock->uom,
                    'status' => 'pending',
                    'box_scanned' => false,
                    'source_bin_scanned' => false,
                    'dest_bin_scanned' => false
                ]);

                $stock->update([
                    'qty_reserved' => $stock->qty_reserved + $material['qty'],
                    'qty_available' => $stock->qty_on_hand - ($stock->qty_reserved + $material['qty'])
                ]);

                $this->logActivity($transferOrder, 'Create Putaway TO', [
                    'description' => "Created Putaway TO for {$material['qty']} {$stock->uom} of {$stock->material->nama_material}",
                    'material_id' => $stock->material_id,
                    'batch_lot' => $stock->batch_lot,
                    'qty_after' => $material['qty'],
                    'bin_from' => $sourceBin->bin_code,
                    'bin_to' => $destBin->bin_code,
                    'reference_document' => $toNumber,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Transfer Order {$toNumber} berhasil dibuat",
                'to_number' => $toNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Transfer Order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function completeTO(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:transfer_order_items,id',
            'items.*.actualQty' => 'required|numeric|min:0',
            'items.*.boxScanned' => 'required|boolean',
            'items.*.sourceBinScanned' => 'required|boolean',
            'items.*.destBinScanned' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $transferOrder = TransferOrder::with(['items.material', 'items.sourceBin', 'items.destinationBin'])
                ->findOrFail($id);

            // Validasi semua item sudah di-scan (tetap dipertahankan)
            foreach ($request->items as $itemData) {
                if (!$itemData['boxScanned'] || !$itemData['sourceBinScanned'] || !$itemData['destBinScanned']) {
                    throw new \Exception('Semua item harus di-scan sebelum menyelesaikan TO!');
                }
            }

            // Update TO items dan proses inventory movement
            foreach ($request->items as $itemData) {
                $item = TransferOrderItem::findOrFail($itemData['id']);
                $actualQty = $itemData['actualQty'];
                
                // 1. Kurangi dari source bin
                $sourceStock = InventoryStock::where('material_id', $item->material_id)
                    ->where('bin_id', $item->source_bin_id)
                    ->where('batch_lot', $item->batch_lot)
                    ->firstOrFail();

                // Validasi kuantitas mencukupi sebelum mengurangi
                if ($sourceStock->qty_on_hand < $actualQty) {
                    throw new \Exception("Stok di Bin asal ({$sourceStock->bin->bin_code}) tidak cukup untuk memindahkan {$actualQty} {$item->uom}.");
                }
                
                // Update item TO (dilakukan di awal loop)
                $item->update([
                    'qty_actual' => $actualQty,
                    'status' => 'completed',
                    'box_scanned' => $itemData['boxScanned'],
                    'source_bin_scanned' => $itemData['sourceBinScanned'],
                    'dest_bin_scanned' => $itemData['destBinScanned'],
                    'completed_at' => now(),
                    'scanned_at' => now()
                ]);

                // Hitung sisa stok sebelum pengurangan
                $remainingQtyOnHandInSource = $sourceStock->qty_on_hand - $actualQty;

                // Kurangi stok dan reserved di source
                $sourceStock->update([
                    'qty_on_hand' => $remainingQtyOnHandInSource,
                    'qty_reserved' => $sourceStock->qty_reserved - $item->qty_planned,
                    'qty_available' => $remainingQtyOnHandInSource - ($sourceStock->qty_reserved - $item->qty_planned),
                    'last_movement_date' => now()
                ]);

                // --- PERBAIKAN LOGIKA DUPLIKASI (TAMBAH KE DESTINATION BIN) ---

                // Cek apakah Batch/Lot yang sama sudah ada di Bin tujuan
                $destStock = InventoryStock::where('material_id', $item->material_id)
                    ->where('bin_id', $item->destination_bin_id)
                    ->where('batch_lot', $item->batch_lot)
                    ->first();
                
                $isNewStockEntry = false;

                if ($destStock) {
                    // Kasus 1: Stok sudah ada di Bin tujuan -> UPDATE / GABUNG
                    $destStock->update([
                        'qty_on_hand' => $destStock->qty_on_hand + $actualQty,
                        'qty_available' => $destStock->qty_available + $actualQty,
                        'last_movement_date' => now()
                    ]);
                } else {
                    // Kasus 2: Stok Belum ada di Bin tujuan -> CREATE
                    $destStock = InventoryStock::create([
                        'material_id' => $item->material_id,
                        'bin_id' => $item->destination_bin_id,
                        'batch_lot' => $item->batch_lot,
                        'warehouse_id' => $transferOrder->warehouse_id,
                        'qty_on_hand' => $actualQty,
                        'qty_reserved' => 0,
                        'qty_available' => $actualQty,
                        'uom' => $item->uom,
                        'status' => 'RELEASED',
                        // Asumsi exp_date dan gr_id diambil dari stok asal yang ada di quarantine
                        'exp_date' => $sourceStock->exp_date,
                        'gr_id' => $sourceStock->gr_id, 
                        'last_movement_date' => now()
                    ]);
                    $isNewStockEntry = true;
                }
                
                // 3. Hapus stok asal jika kuantitasnya menjadi 0
                if ($sourceStock->qty_on_hand <= 0) {
                    $sourceStock->delete();
                }

                // 4. Update bin occupancy
                $sourceBin = $item->sourceBin;
                $destBin = $item->destinationBin;

                // Logic ini harus disesuaikan untuk TO
                // Jika stok asal dihapus, kurangi current_items di source bin
                if ($remainingQtyOnHandInSource <= 0 && $sourceStock->wasRecentlyDeleted) {
                     $sourceBin->update([
                         'current_items' => max(0, $sourceBin->current_items - 1)
                     ]);
                }

                // Jika entri stok baru dibuat di dest bin, tambah current_items
                if ($isNewStockEntry) {
                     $destBin->update([
                         'current_items' => $destBin->current_items + 1
                     ]);
                }

                // Log activity
                $this->logActivity($transferOrder, 'Complete TO Item', [
                    'description' => "Completed transfer of {$actualQty} {$item->uom} from {$sourceBin->bin_code} to {$destBin->bin_code}",
                    'material_id' => $item->material_id,
                    'batch_lot' => $item->batch_lot,
                    'qty_after' => $destStock->qty_on_hand,
                    'bin_from' => $sourceBin->bin_code,
                    'bin_to' => $destBin->bin_code,
                    'reference_document' => $transferOrder->to_number,
                ]);
            }

            // Update TO status
            $transferOrder->update([
                'status' => 'Completed',
                'completion_date' => now(),
                'executed_by' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Transfer Order {$transferOrder->to_number} berhasil diselesaikan"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyelesaikan Transfer Order: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateTONumber()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastTO = TransferOrder::whereYear('creation_date', $year)
            ->whereMonth('creation_date', $month)
            ->orderBy('to_number', 'desc')
            ->first();

        if ($lastTO) {
            $lastNumber = intval(substr($lastTO->to_number, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "TO-{$year}-{$month}-{$newNumber}";
    }
}