<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\StockMovement;
use App\Models\WarehouseBin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Traits\ActivityLogger;


class BintobinController extends Controller
{
    use ActivityLogger;

    public function index(Request $request)
    {
        // 1. Ambil data Status Bin (untuk sidebar)
        $bins = WarehouseBin::with('warehouse')->get()->map(function ($bin) {
            $capacityPercent = $bin->capacity > 0 ? round(($bin->current_items / $bin->capacity) * 100) : 0;
            return [
                'code' => $bin->bin_code,
                'location' => $bin->warehouse->nama_warehouse ?? $bin->bin_name,
                'capacity' => $capacityPercent,
            ];
        });

        // 2. Ambil data Riwayat Transfer (untuk sidebar)
        $history = StockMovement::with(['material', 'fromBin', 'toBin'])
            ->where('movement_type', 'BIN_TO_BIN')
            ->latest('movement_date')
            ->take(20)
            ->get();

        $formattedHistory = $history->map(function ($transfer) {
            return [
                'materialCode' => $transfer->material->kode_material ?? 'N/A',
                'materialName' => $transfer->material->nama_material ?? 'N/A',
                'quantity' => (float) $transfer->qty,
                'unit' => $transfer->uom,
                'fromBin' => $transfer->fromBin->bin_code ?? 'N/A',
                'toBin' => $transfer->toBin->bin_code ?? 'N/A',
                'timestamp' => $transfer->movement_date->format('H:i, d/m'),
            ];
        });

        // --- INI BAGIAN BARU UNTUK PROSES SCAN ---
        
        $scannedMaterialData = null;
        $destinationBinData = null;

        // 3. Cek apakah ada request scan material
        if ($request->has('material_batch')) {
            // Asumsi QR code berisi 'batch_lot' yang unik
            $stock = InventoryStock::with(['material', 'bin'])
                ->where('batch_lot', $request->input('material_batch'))
                ->first();

            if (!$stock) {
                // Jika tidak ada, kembali dengan error flash
                return redirect()->route('bintobin.index')
                    ->with('error', 'Stok material (Batch: ' . $request->input('material_batch') . ') tidak ditemukan.');
            }
            if ($stock->qty_available <= 0) {
                return redirect()->route('bintobin.index')
                    ->with('error', 'Stok material ini (Batch: ' . $stock->batch_lot . ') sudah habis.');
            }

            // Jika ada, format datanya
            $scannedMaterialData = [
                'stock_id' => $stock->id,
                'code' => $stock->material->kode_material ?? 'N/A',
                'name' => $stock->material->nama_material ?? 'N/A',
                'category' => $stock->material->category->name ?? 'Uncategorized',
                'quantity' => (float) $stock->qty_available, // Kirim kuantitas yang tersedia
                'unit' => $stock->uom,
                'currentBin' => $stock->bin->bin_code ?? 'N/A',
                'current_bin_id' => $stock->bin_id,
                'batchNo' => $stock->batch_lot,
                'expiryDate' => $stock->exp_date ? $stock->exp_date->format('d/m/Y') : 'N/A',
            ];

            // 4. Cek apakah ada request scan bin (scan langkah kedua)
            if ($request->has('bin_code')) {
                $bin = WarehouseBin::where('bin_code', $request->input('bin_code'))->first();

                if (!$bin) {
                    // Kembali dengan error, tapi 'tahan' data material yang sudah discan
                    return redirect()->route('bintobin.index', ['material_batch' => $request->input('material_batch')])
                        ->with('error', 'Lokasi Bin (' . $request->input('bin_code') . ') tidak ditemukan.');
                }

                if ($bin->id === $stock->bin_id) {
                    return redirect()->route('bintobin.index', ['material_batch' => $request->input('material_batch')])
                        ->with('error', 'Bin tujuan tidak boleh sama dengan bin asal.');
                }
                
                // Jika ada, format datanya
                $destinationBinData = [
                    'id' => $bin->id,
                    'code' => $bin->bin_code,
                ];
            }
        }
        
        // --- AKHIR BAGIAN BARU ---

        return Inertia::render('Bintobin', [
            'title' => 'Perpindahan Barang',
            'initialBins' => $bins,
            'initialTransferHistory' => $formattedHistory,
            
            // 5. Kirim data hasil scan (bisa null) sebagai props
            'scannedMaterial' => $scannedMaterialData,
            'destinationBin' => $destinationBinData,
        ]);
    }

    /**
     * Ambil detail material/stok berdasarkan kode unik (misal: batch_lot atau ID stok).
     * Kita asumsikan QR Code berisi 'batch_lot'.
     */
    public function getMaterialDetails(string $code)
    {
        // Gunakan 'batch_lot' untuk mencari stok. Anda bisa ubah ke 'id' jika QR code berisi ID stok.
        $stock = InventoryStock::with(['material', 'bin']) // Hapus 'material.category' jika tidak ada relasinya
            ->where('batch_lot', $code)
            // ->orWhere('id', $code) // Aktifkan jika QR bisa jadi ID
            ->first();

        if (!$stock) {
            return response()->json(['message' => 'Stok material tidak ditemukan.'], 404);
        }

        if ($stock->qty_available <= 0) {
            return response()->json(['message' => 'Stok material ini (Batch: ' . $code . ') sudah habis.'], 422);
        }
        
        // Format data sesuai ekspektasi frontend
        $formattedData = [
            'stock_id' => $stock->id,
            'code' => $stock->material->kode_material ?? 'N/A',
            'name' => $stock->material->nama_material ?? 'N/A',
            'category' => $stock->material->category->name ?? 'Uncategorized', // Asumsi relasi material->category->name
            'quantity' => (float) $stock->qty_available, // Kirim kuantitas yang tersedia
            'unit' => $stock->uom,
            'currentBin' => $stock->bin->bin_code ?? 'N/A',
            'current_bin_id' => $stock->bin_id,
            'batchNo' => $stock->batch_lot,
            'expiryDate' => $stock->exp_date ? $stock->exp_date->format('d/m/Y') : 'N/A',
        ];

        return response()->json($formattedData);
    }

    /**
     * Validasi kode bin.
     */
    public function getBinDetails(string $code)
    {
        $bin = WarehouseBin::where('bin_code', $code)->first();

        if (!$bin) {
            return response()->json(['message' => 'Lokasi Bin tidak ditemukan.'], 404);
        }

        // Kirim data minimal yang dibutuhkan frontend
        return response()->json([
            'id' => $bin->id,
            'code' => $bin->bin_code,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     * (Method 'store' Anda sudah SANGAT BAIK. Tidak perlu diubah)
     * (Pastikan Anda sudah meng-import 'ActivityLogger', 'DB', 'Auth', 'StockMovement', 'InventoryStock', 'WarehouseBin')
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_bin_id' => 'required|exists:warehouse_bins,id',
            'to_bin_id' => 'required|exists:warehouse_bins,id|different:from_bin_id',
            'stock_id' => 'required|exists:inventory_stock,id', // 'inventory_stocks' -> 'inventory_stock' sesuai nama tabel Anda
            'quantity' => 'required|numeric|min:0.01',
        ], [
            'to_bin_id.different' => 'Bin tujuan tidak boleh sama dengan bin asal.'
        ]);

        DB::beginTransaction();
        try {
            $stock = InventoryStock::findOrFail($validated['stock_id']);
            $fromBin = WarehouseBin::findOrFail($validated['from_bin_id']);
            $toBin = WarehouseBin::findOrFail($validated['to_bin_id']);

            // 1. Check if enough stock is available
            // Validasi kuantitas yang diminta tidak melebihi yang tersedia
            if ($stock->qty_available < $validated['quantity']) {
                throw new \Exception('Kuantitas transfer melebihi stok yang tersedia (' . $stock->qty_available . ' ' . $stock->uom . ').');
            }

            // 2. Decrement stock from the source bin
            // Kita asumsikan seluruh stok (qty_available) dari batch itu yang dipindah
            // Jika tidak, frontend harus mengirimkan 'quantity' yg spesifik.
            // Kode Anda sudah memvalidasi 'quantity' dari request, jadi kita gunakan $validated['quantity']
            $stock->decrement('qty_on_hand', $validated['quantity']);
            $stock->decrement('qty_available', $validated['quantity']);

            // 3. Increment stock in the destination bin or create new stock entry
            $destinationStock = InventoryStock::firstOrCreate(
                [
                    'material_id' => $stock->material_id,
                    'warehouse_id' => $toBin->warehouse_id,
                    'bin_id' => $toBin->id,
                    'batch_lot' => $stock->batch_lot,
                    'status' => $stock->status,
                ],
                [
                    'exp_date' => $stock->exp_date,
                    'qty_on_hand' => 0,
                    'qty_reserved' => 0,
                    'qty_available' => 0,
                    'uom' => $stock->uom,
                    'gr_id' => $stock->gr_id,
                ]
            );
            $destinationStock->increment('qty_on_hand', $validated['quantity']);
            $destinationStock->increment('qty_available', $validated['quantity']);

            // 4. Create Stock Movement Record
            $movementNumber = $this->generateMovementNumber();
            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'movement_type' => 'BIN_TO_BIN',
                'material_id' => $stock->material_id,
                'batch_lot' => $stock->batch_lot,
                'from_warehouse_id' => $fromBin->warehouse_id,
                'from_bin_id' => $fromBin->id,
                'to_warehouse_id' => $toBin->warehouse_id,
                'to_bin_id' => $toBin->id,
                'qty' => $validated['quantity'],
                'uom' => $stock->uom,
                'reference_type' => 'self',
                'reference_id' => null,
                'movement_date' => now(),
                'executed_by' => Auth::id(),
                'notes' => "Transfer from {$fromBin->bin_code} to {$toBin->bin_code}",
            ]);

            // 5. Log the activity (Jika Anda punya relasi 'material' di InventoryStock)
            if ($stock->relationLoaded('material')) {
                 $this->logActivity($movement, 'Move', [
                    'description' => "Moved {$validated['quantity']} {$stock->uom} of {$stock->material->nama_material} from {$fromBin->bin_code} to {$toBin->bin_code}.",
                    'material_id' => $stock->material_id,
                    'batch_lot' => $stock->batch_lot,
                    'qty_before' => $stock->qty_on_hand + $validated['quantity'],
                    'qty_after' => $stock->qty_on_hand,
                    'bin_from' => $fromBin->bin_code,
                    'bin_to' => $toBin->bin_code,
                    'reference_document' => $movementNumber,
                ]);
            }
           
            DB::commit();

            return redirect()->route('bin-to-bin.index')->with('success', 'Bin transfer successful.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Bin transfer failed: ' . $e->getMessage());
        }
    }

    private function generateMovementNumber()
    {
        $date = date('Ymd');
        // Pastikan 'movement_date' ada di model StockMovement
        $lastMovement = StockMovement::whereDate('movement_date', today())->latest('id')->first();
        $sequence = $lastMovement ? (intval(substr($lastMovement->movement_number, -4)) + 1) : 1;
        return "MOV/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
    
}