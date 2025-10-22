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
            $query->where('bin_code', 'LIKE', 'QTN-%');
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
            ->whereRaw('current_items < capacity')
            ->where(function ($query) {
                $query->where('bin_code', 'LIKE', 'STD-%')
                      ->orWhere('bin_code', 'LIKE', 'HAZ-%');
            })
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

            // Validasi semua item sudah di-scan
            foreach ($request->items as $itemData) {
                if (!$itemData['boxScanned'] || !$itemData['sourceBinScanned'] || !$itemData['destBinScanned']) {
                    throw new \Exception('Semua item harus di-scan sebelum menyelesaikan TO!');
                }
            }

            // Update TO items dan proses inventory movement
            foreach ($request->items as $itemData) {
                $item = TransferOrderItem::findOrFail($itemData['id']);
                
                $item->update([
                    'qty_actual' => $itemData['actualQty'],
                    'status' => 'completed',
                    'box_scanned' => $itemData['boxScanned'],
                    'source_bin_scanned' => $itemData['sourceBinScanned'],
                    'dest_bin_scanned' => $itemData['destBinScanned'],
                    'completed_at' => now(),
                    'scanned_at' => now()
                ]);

                // Proses inventory movement
                // 1. Kurangi dari source bin
                $sourceStock = InventoryStock::where('material_id', $item->material_id)
                    ->where('bin_id', $item->source_bin_id)
                    ->where('batch_lot', $item->batch_lot)
                    ->firstOrFail();

                $sourceStock->update([
                    'qty_on_hand' => $sourceStock->qty_on_hand - $itemData['actualQty'],
                    'qty_reserved' => $sourceStock->qty_reserved - $item->qty_planned,
                    'qty_available' => ($sourceStock->qty_on_hand - $itemData['actualQty']) - 
                                      ($sourceStock->qty_reserved - $item->qty_planned),
                    'last_movement_date' => now()
                ]);

                // 2. Tambah ke destination bin
                $destStock = InventoryStock::firstOrCreate(
                    [
                        'material_id' => $item->material_id,
                        'bin_id' => $item->destination_bin_id,
                        'batch_lot' => $item->batch_lot,
                        'warehouse_id' => $transferOrder->warehouse_id
                    ],
                    [
                        'qty_on_hand' => 0,
                        'qty_reserved' => 0,
                        'qty_available' => 0,
                        'uom' => $item->uom,
                        'status' => 'RELEASED',
                        'exp_date' => $sourceStock->exp_date,
                        'gr_id' => $sourceStock->gr_id
                    ]
                );

                $destStock->update([
                    'qty_on_hand' => $destStock->qty_on_hand + $itemData['actualQty'],
                    'qty_available' => $destStock->qty_available + $itemData['actualQty'],
                    'last_movement_date' => now()
                ]);

                // 3. Update bin occupancy
                $sourceBin = $item->sourceBin;
                $destBin = $item->destinationBin;

                // Jika source bin kosong setelah transfer, kurangi current_items
                if ($sourceStock->qty_on_hand <= 0) {
                    $sourceBin->update([
                        'current_items' => max(0, $sourceBin->current_items - 1)
                    ]);
                }

                // Jika ini item baru di dest bin, tambah current_items
                if ($destStock->qty_on_hand == $itemData['actualQty']) {
                    $destBin->update([
                        'current_items' => $destBin->current_items + 1
                    ]);
                }

                // Log activity
                $this->logActivity($transferOrder, 'Complete TO Item', [
                    'description' => "Completed transfer of {$itemData['actualQty']} {$item->uom} from {$sourceBin->bin_code} to {$destBin->bin_code}",
                    'material_id' => $item->material_id,
                    'batch_lot' => $item->batch_lot,
                    'qty_after' => $itemData['actualQty'],
                    'bin_from' => $sourceBin->bin_code,
                    'bin_to' => $destBin->bin_code,
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

    public function scanPutaway(Request $request)
    {
        $validated = $request->validate([
            'quarantine_qr' => 'required|string',
            'material_qr' => 'required|string',
            'destination_qr' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Validate QR Codes
            $sourceBin = WarehouseBin::where('bin_code', $validated['quarantine_qr'])->where('bin_code', 'LIKE', 'QTN-%')->firstOrFail();

            list($incomingNumber, $itemCode, $batchLot, $qty, $expDate) = explode('|', $validated['material_qr']);

            $material = Material::where('kode_item', $itemCode)->firstOrFail();
            $destinationBin = WarehouseBin::where('bin_code', $validated['destination_qr'])->where('status', 'available')->firstOrFail();

            // 2. Find Inventory Stock
            $stock = InventoryStock::where('material_id', $material->id)
                ->where('bin_id', $sourceBin->id)
                ->where('batch_lot', $batchLot)
                ->where('status', 'RELEASED')
                ->firstOrFail();

            // 3. Create Transfer Order
            $toNumber = $this->generateTONumber();
            $transferOrder = TransferOrder::create([
                'to_number' => $toNumber,
                'transaction_type' => 'Putaway - Scan',
                'warehouse_id' => $stock->warehouse_id,
                'creation_date' => now(),
                'status' => 'Completed',
                'created_by' => Auth::id(),
                'executed_by' => Auth::id(),
                'completion_date' => now(),
                'notes' => 'Generated from scan-based putaway'
            ]);

            $transferOrder->items()->create([
                'material_id' => $material->id,
                'batch_lot' => $batchLot,
                'source_bin_id' => $sourceBin->id,
                'destination_bin_id' => $destinationBin->id,
                'qty_planned' => $qty,
                'qty_actual' => $qty,
                'uom' => $stock->uom,
                'status' => 'completed',
                'box_scanned' => true,
                'source_bin_scanned' => true,
                'dest_bin_scanned' => true
            ]);

            // 4. Update Inventory
            $stock->update([
                'qty_on_hand' => $stock->qty_on_hand - $qty,
                'qty_available' => $stock->qty_available - $qty,
            ]);

            $destStock = InventoryStock::firstOrCreate(
                ['material_id' => $material->id, 'bin_id' => $destinationBin->id, 'batch_lot' => $batchLot, 'warehouse_id' => $stock->warehouse_id],
                ['qty_on_hand' => 0, 'qty_available' => 0, 'uom' => $stock->uom, 'status' => 'RELEASED', 'exp_date' => $stock->exp_date]
            );
            $destStock->increment('qty_on_hand', $qty);
            $destStock->increment('qty_available', $qty);

            // 5. Log Activity
            $this->logActivity($transferOrder, 'Complete Putaway TO', [
                'description' => "Completed Putaway TO for {$qty} {$stock->uom} of {$material->nama_material}",
                'material_id' => $material->id,
                'batch_lot' => $batchLot,
                'qty_after' => $qty,
                'bin_from' => $sourceBin->bin_code,
                'bin_to' => $destinationBin->bin_code,
                'reference_document' => $toNumber,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => "Transfer Order {$toNumber} successfully completed."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to complete putaway: ' . $e->getMessage()], 500);
        }
    }
}