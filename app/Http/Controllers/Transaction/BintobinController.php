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
                'materialCode' => $transfer->material->kode_item ?? 'N/A',
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
                return redirect()->route('transaction.bin-to-bin')
                    ->with('error', 'Stok material (Batch: ' . $request->input('material_batch') . ') tidak ditemukan.');
            }
            // if ($stock->qty_available <= 0) {
            //     return redirect()->route('transaction.bin-to-bin')
            //         ->with('error', 'Stok material ini (Batch: ' . $stock->batch_lot . ') sudah habis.');
            // }

            // Jika ada, format datanya
            $scannedMaterialData = [
                'stock_id' => $stock->id,
                'code' => $stock->material->kode_item ?? 'N/A',
                'name' => $stock->material->nama_material ?? 'N/A',
                'category' => $stock->material->kategori ?? 'Packaging Material', // Asumsi relasi material->category->name
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
                    return redirect()->route('transaction.bin-to-bin', ['material_batch' => $request->input('material_batch')])
                        ->with('error', 'Lokasi Bin (' . $request->input('bin_code') . ') tidak ditemukan.');
                }

                if ($bin->id === $stock->bin_id) {
                    return redirect()->route('transaction.bin-to-bin', ['material_batch' => $request->input('material_batch')])
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

        // if ($stock->qty_available <= 0) {
        //     return response()->json(['message' => 'Stok material ini (Batch: ' . $code . ') sudah habis.'], 422);
        // }
        
        // Format data sesuai ekspektasi frontend
        $formattedData = [
            'stock_id' => $stock->id,
            'code' => $stock->material->kode_item ?? 'N/A',
            'name' => $stock->material->nama_material ?? 'N/A',
            'category' => $stock->material->category->name ?? 'Packaging Material', // Asumsi relasi material->category->name
            'quantity' => (float) $stock->qty_available, // Kirim kuantitas yang tersedia
            'unit' => $stock->uom,
            'currentBin' => $stock->bin->bin_code ?? 'N/A',
            'current_bin_id' => $stock->bin_id,
            'batchNo' => $stock->batch_lot,
            'expiryDate' => $stock->exp_date ? $stock->exp_date->format('d/m/Y') : 'N/A',
        ];

        return response()->json($formattedData);
    }

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_bin_id' => 'required|exists:warehouse_bins,id',
            'to_bin_id' => 'required|exists:warehouse_bins,id|different:from_bin_id',
            'stock_id' => 'required|exists:inventory_stock,id',
            'quantity' => 'required|numeric|min:0.01',
        ], [
            'to_bin_id.different' => 'Bin tujuan tidak boleh sama dengan bin asal.'
        ]);

        DB::beginTransaction();
        try {
            $stock = InventoryStock::findOrFail($validated['stock_id']);
            $fromBin = WarehouseBin::findOrFail($validated['from_bin_id']);
            $toBin = WarehouseBin::findOrFail($validated['to_bin_id']);

            $quantityToMove = $validated['quantity'];

            // 1. Validasi Kuantitas
            if ($stock->qty_available < $quantityToMove) {
                throw new \Exception('Kuantitas transfer melebihi stok yang tersedia (' . $stock->qty_available . ' ' . $stock->uom . ').');
            }

            // Cari apakah sudah ada stok DENGAN BATCH YANG SAMA di bin tujuan
            $existingStockInTargetBin = InventoryStock::where('material_id', $stock->material_id)
                ->where('batch_lot', $stock->batch_lot)
                ->where('bin_id', $toBin->id)
                ->first();

            // --- VALIDASI KAPASITAS BIN TUJUAN ---
            // Cek jika bin tujuan punya kapasitas terbatas (> 0)
            if ($toBin->capacity > 0) {
                // Jika stok baru (tidak merging), cek apakah bin penuh
                if (!$existingStockInTargetBin) {
                    if ($toBin->current_items >= $toBin->capacity) {
                        throw new \Exception("Bin tujuan ({$toBin->bin_code}) sudah penuh! Kapasitas: {$toBin->capacity}, Terisi: {$toBin->current_items}.");
                    }
                }
            }
            // -------------------------------------

            $isNewStockEntry = false;

            if ($existingStockInTargetBin) {
                // Kasus 1: Stok Batch yang Sama SUDAH ADA di Bin Tujuan (Cukup di gabung)
                
                // a. Gabungkan stok: Kurangi dari stok asal ($stock)
                $stock->decrement('qty_on_hand', $quantityToMove);
                $stock->decrement('qty_available', $quantityToMove);

                // b. Tambahkan ke stok tujuan ($existingStockInTargetBin)
                $existingStockInTargetBin->increment('qty_on_hand', $quantityToMove);
                $existingStockInTargetBin->increment('qty_available', $quantityToMove);

                $newStock = $existingStockInTargetBin; // Gunakan ini untuk logging
                
            } elseif ($stock->qty_available == $quantityToMove) {
                // Kasus 2: Pindah SELURUH stok dari Batch ini
                
                // a. Update lokasi stok yang ada langsung ke bin tujuan
                $stock->update([
                    'bin_id' => $toBin->id,
                    'warehouse_id' => $toBin->warehouse_id,
                ]);
                
                $newStock = $stock; // Gunakan stok yang sama untuk logging dan riwayat
                $isNewStockEntry = true; // Dianggap entry baru di bin tujuan (meski record sama)
                
            } else {
                // Kasus 3: Pindah PARSIAL (Sebagian). Perlu memisahkan stok.
                
                // a. Kurangi stok dari entri asal ($stock)
                $stock->decrement('qty_on_hand', $quantityToMove);
                $stock->decrement('qty_available', $quantityToMove);

                // b. Duplikasi/buat entri baru untuk lokasi tujuan
                $newStock = $stock->replicate();
                $newStock->bin_id = $toBin->id;
                $newStock->warehouse_id = $toBin->warehouse_id;
                $newStock->qty_on_hand = $quantityToMove;
                $newStock->qty_available = $quantityToMove;
                $newStock->qty_reserved = 0; // Pastikan reserved nol di entri baru
                $newStock->save();

                $isNewStockEntry = true;
            }

            // --- UPDATE BIN OCCUPANCY (CURRENT ITEMS) ---
            
            // 1. Update Source Bin (Kurangi jika stok habis/pindah semua)
            // Cek apakah stok asal sudah kosong/dihapus (untuk kasus parsial yang sisa 0 nanti dihapus)
            // Atau jika kasus pindah seluruhnya (Kasus 2), maka bin asal kehilangan 1 item
            if ($stock->qty_on_hand == 0 || $stock->bin_id == $toBin->id) {
                 // Jika qty 0, akan dihapus di bawah. Jika bin_id berubah, berarti pindah full.
                 // Maka kurangi current_items dari bin ASAL
                 $fromBin->decrement('current_items');
                 // Pastikan tidak negatif (safety)
                 if ($fromBin->current_items < 0) {
                     $fromBin->update(['current_items' => 0]);
                 }
            }

            // 2. Update Destination Bin (Tambah jika entry baru)
            if ($isNewStockEntry) {
                // Jika ini adalah item baru di bin tujuan (bukan merge), tambah counter
                // Note: Kasus 2 (Pindah Full) juga dianggap new entry bagi bin tujuan
                $toBin->increment('current_items');
            }
            // --------------------------------------------

            // 3. Hapus entri stok asal jika kuantitasnya menjadi nol
            if ($stock->qty_on_hand == 0) {
                $stock->delete();
            }

            // 4. Create Stock Movement Record (Riwayat)
            $movementNumber = $this->generateMovementNumber();
            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'movement_type' => 'BIN_TO_BIN',
                'material_id' => $stock->material_id, // Gunakan material_id dari stok awal
                'batch_lot' => $stock->batch_lot,
                'from_warehouse_id' => $fromBin->warehouse_id,
                'from_bin_id' => $fromBin->id,
                'to_warehouse_id' => $toBin->warehouse_id,
                'to_bin_id' => $toBin->id,
                'qty' => $quantityToMove,
                'uom' => $stock->uom,
                'reference_type' => 'stock_id',
                'reference_id' => $newStock->id, // Referensi ke entri stok yang sekarang
                'movement_date' => now(),
                'executed_by' => Auth::id(),
                'notes' => "Transfer B2B: {$fromBin->bin_code} ke {$toBin->bin_code}",
            ]);

            // 5. Log activity
            if ($stock->relationLoaded('material')) {
                $this->logActivity($movement, 'Move', [
                    'description' => "Memindahkan {$quantityToMove} {$stock->uom} {$stock->material->nama_material} dari {$fromBin->bin_code} ke {$toBin->bin_code}.",
                    'material_id' => $stock->material_id,
                    'batch_lot' => $stock->batch_lot,
                    'qty_before' => $stock->qty_on_hand + $quantityToMove,
                    'qty_after' => $newStock->qty_on_hand,
                    'bin_from' => $fromBin->id,
                    'bin_to' => $toBin->id,
                    'reference_document' => $movementNumber,
                ]);
            }
            
            DB::commit();

            return redirect()->route('transaction.bin-to-bin')->with('success', 'Perpindahan Bin ke Bin berhasil! Stok dipindahkan dari ' . $fromBin->bin_code . ' ke ' . $toBin->bin_code);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Perpindahan Bin ke Bin gagal: ' . $e->getMessage());
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