<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\TransferOrder;
use App\Models\IncomingGoodsItem;
use App\Models\WarehouseBin;
use App\Models\InventoryStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PutawayTransferController extends Controller
{
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
                        'boxScanned' => $item->box_scanned,
                        'sourceBinScanned' => $item->source_bin_scanned,
                        'destBinScanned' => $item->dest_bin_scanned,
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
        // Ambil material yang sudah QC PASS dan status RELEASED di inventory
        $materials = InventoryStock::with([
            'material',
            'warehouse',
            'bin'
        ])
        ->where('status', 'RELEASED')
        ->where('qty_available', '>', 0)
        ->whereHas('bin', function ($query) {
            // Hanya dari bin karantina yang perlu dipindah ke storage
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
        
        // Ambil bin yang available untuk putaway
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
                    'materials' => [] // Bisa ditambahkan query untuk get materials in bin
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
        
        // Get materials in this bin
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
            'materials.*.destinationBin' => 'required|exists:warehouse_bins,bin_code'
        ]);

        DB::beginTransaction();
        try {
            // Generate TO Number
            $toNumber = $this->generateTONumber();
            
            // Create Transfer Order
            $transferOrder = TransferOrder::create([
                'to_number' => $toNumber,
                'transaction_type' => 'Putaway - QC Release',
                'warehouse_id' => 1, // Adjust sesuai warehouse
                'creation_date' => now(),
                'status' => 'Pending',
                'created_by' => Auth::user()->id,
                'notes' => 'Auto-generated from QC Released materials'
            ]);

            // Create TO Items
            foreach ($request->materials as $material) {
                $stock = InventoryStock::find($material['stockId']);
                $sourceBin = WarehouseBin::where('bin_code', $stock->bin->bin_code)->first();
                $destBin = WarehouseBin::where('bin_code', $material['destinationBin'])->first();

                $transferOrder->items()->create([
                    'material_id' => $stock->material_id,
                    'batch_lot' => $stock->batch_lot,
                    'source_bin_id' => $sourceBin->id,
                    'destination_bin_id' => $destBin->id,
                    'qty_planned' => $material['qty'],
                    'uom' => $stock->uom,
                    'status' => 'pending'
                ]);

                // Reserve qty di inventory
                $stock->update([
                    'qty_reserved' => $stock->qty_reserved + $material['qty'],
                    'qty_available' => $stock->qty_on_hand - ($stock->qty_reserved + $material['qty'])
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