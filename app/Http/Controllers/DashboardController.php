<?php

namespace App\Http\Controllers;
use App\Models\InventoryStock;
use App\Models\IncomingGood;
use App\Models\IncomingGoodsItem;
use App\Models\QcReqcHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Daftar status InventoryStock yang ingin ditampilkan di dashboard
        $validStatuses = ['KARANTINA', 'RELEASED', 'HOLD', 'REJECTED'];

        // 1. Ambil Data Inventory Stock dengan Kriteria Pemantauan
        $materialItems = InventoryStock::query()
            ->where('qty_on_hand', '>', 0)
            // 💡 PERBAIKAN UTAMA: Menggunakan status yang dikonfirmasi oleh pengguna
            ->whereIn('status', $validStatuses)
            ->with([
                'material',
                'bin',
                'movements.executedBy',
                'movements.fromBin',
                'movements.toBin'
            ])
            ->get()->map(function ($item) {

                // Logika pemrosesan history (sama seperti sebelumnya)
                $history = $item->movements->map(function ($movement) {
                    $detail = '';
                    if ($movement->movement_type === 'GR') {
                        $detail = "Penerimaan $movement->qty $movement->uom di lokasi " . ($movement->toBin->bin_code ?? 'N/A') . ". Ref: " . ($movement->reference_document ?? 'N/A');
                    } elseif ($movement->movement_type === 'B2B') {
                        $detail = "Transfer Bin to Bin: Pindah $movement->qty $movement->uom dari " . ($movement->fromBin->bin_code ?? 'N/A') . " ke " . ($movement->toBin->bin_code ?? 'N/A') . ". Ref: " . ($movement->reference_document ?? 'N/A');
                    } else {
                        $detail = "$movement->movement_type $movement->qty $movement->uom. Lokasi: " . ($movement->toBin->bin_code ?? 'N/A') . ". Ref: " . ($movement->reference_type ?? 'N/A');
                    }

                    return [
                        'id' => $movement->id,
                        'date' => $movement->movement_date->toISOString(),
                        'action' => $movement->movement_type,
                        'detail' => $detail,
                        'user' => $movement->executedBy->name ?? 'System',
                    ];
                })->toArray(); // Konversi ke array untuk serialisasi

                // --- Logika Status Khusus Dashboard ---
                $binCode = $item->bin->bin_code ?? '';
                $requiresPutAway = false;
                $requiresQC = false;
                $displayStatus = $item->status;
                
                if ($item->status === 'RELEASED' && str_starts_with($binCode, 'QRT-')) {
                    $requiresPutAway = true;
                    // Ubah status yang ditampilkan agar jelas di tabel
                    $displayStatus = 'Released (Perlu Put Away)'; 
                }

                // 2. Cek apakah item KARANTINA (membutuhkan QC/tindak lanjut)
                if ($item->status === 'KARANTINA') {
                    $requiresQC = true;  
                    $displayStatus = 'Karantina (Perlu QC/Tindak Lanjut)';
                }
                
                // 3. Cek jika item HOLD
                if ($item->status === 'HOLD') {
                    $displayStatus = 'HOLD (Menunggu Persetujuan)';
                }

                // 4. Cek jika item REJECT
                if ($item->status === 'REJECT') {  
                    $displayStatus = 'Ditolak (Perlu Tindak Lanjut)';
                }

                return [
                    'id' => 'INV-' . $item->id,
                    'source_table' => 'inventory_stock',
                    'type' => $item->material->kategori,
                    'sub_kategori' => $item->material->sub_kategori,
                    'kode' => $item->material->kode_item,
                    'nama' => $item->material->nama_material,
                    'lot' => $item->batch_lot,
                    'lokasi' => $binCode,
                    'qty' => $item->qty_on_hand,
                    'uom' => $item->uom,
                    'expiredDate' => $item->exp_date ? $item->exp_date->toDateString() : 'N/A',
                    // 💡 PENTING: Menggunakan $displayStatus di sini
                    'status' => $displayStatus, 
                    'history' => $history,
                    'qr_data' => json_encode([
                        'id' => $item->id,
                        'kode' => $item->material->kode_item,
                        'lot' => $item->batch_lot,
                        'status' => $item->status, // Status asli (RELEASED/KARANTINA) untuk QR
                    ]),
                    // 💡 PENTING: Menggunakan status asli (sebelum diubah $displayStatus)
                    'qr_type' => $item->status, 
                    'requiresPutAway' => $requiresPutAway,
                    'requiresQC' => $requiresQC,
                    'entry_date' => $item->created_at->toISOString(), // Added for sorting (Newest/Oldest)
                ];
            });
        
        $putAwayCount = $materialItems->filter(fn($item) => $item['requiresPutAway'])->count();

        // Hitungan QC: Item KARANTINA
        $qcCount = $materialItems->filter(fn($item) => $item['requiresQC'])->count();

        // Hitungan Expired dan Expiring Soon
        // Hitung ulang di sini untuk akurasi penuh
        $expiredCount = InventoryStock::where('qty_on_hand', '>', 0)
            ->where('exp_date', '<=', now())
            ->whereIn('status', $validStatuses) // Tambahkan filter status
            ->count();
        $expiringSoonCount = InventoryStock::where('qty_on_hand', '>', 0)
            ->where('exp_date', '<=', now()->addDays(30))
            ->where('exp_date', '>', now())
            ->whereIn('status', $validStatuses) // Tambahkan filter status
            ->count();

        $alerts = [];
        
        // Notifikasi Cerdas Put Away
        if ($putAwayCount > 0) {
            $alerts[] = ['id' => '0', 'type' => 'info', 'message' => "💡 **$putAwayCount item** status Released tapi masih di Bin Karantina (`QRT-*`). Segera lakukan **Put Away**."];
        }

        // Notifikasi QC yang Dibutuhkan (Karantina)
        if ($qcCount > 0) {
            $alerts[] = ['id' => '3', 'type' => 'info', 'message' => "📝 **$qcCount item** berada di status **KARANTINA** dan membutuhkan Quality Check/Tindak Lanjut."
            ];
        }

        if ($expiringSoonCount > 0) {
            $alerts[] = ['id' => '1', 'type' => 'warning', 'message' => "$expiringSoonCount item akan expired dalam 30 hari"];
        }
        if ($expiredCount > 0) {
            $alerts[] = ['id' => '2', 'type' => 'error', 'message' => "$expiredCount item sudah expired!"];
        }

        return Inertia::render('Dashboard', [
            'materialItems' => $materialItems,
            'alerts' => $alerts,
        ]);
    }

    /**
     * Initiate Re-QC for expired materials
     * Find existing QC items, reset status, create history
     */
    public function initiateReqcForExpiredMaterials(Request $request)
    {
        \Log::info('🚀 Re-QC Initiation Started', ['request' => $request->all()]);
        
        $validated = $request->validate([
            'inventory_stock_ids' => 'required|array|min:1',
            'inventory_stock_ids.*' => 'required|exists:inventory_stock,id',
        ]);

        \Log::info('✅ Validation passed', ['validated' => $validated]);

        DB::beginTransaction();
        try {
            $inventoryStockIds = $validated['inventory_stock_ids'];
            
            // Get all selected inventory stocks
            $stocks = InventoryStock::with(['material', 'bin'])
                ->whereIn('id', $inventoryStockIds)
                ->get();

            \Log::info('📦 Retrieved stocks', ['count' => $stocks->count()]);

            $errors = [];
            $qrtBinCodes = ['QRT-HALAL', 'QRT-NON HALAL', 'QRT-HALAL-AC'];
            $processedCount = 0;

            foreach ($stocks as $stock) {
                // Validate material is expired
                if (!$stock->isExpired()) {
                    $errors[] = "Material {$stock->material->nama_material} (Batch: {$stock->batch_lot}) belum expired.";
                    continue;
                }

                // Validate material is in QRT bin
                $binCode = $stock->bin ? $stock->bin->bin_code : null;
                if (!$binCode || !in_array($binCode, $qrtBinCodes)) {
                    $errors[] = "Material {$stock->material->nama_material} (Batch: {$stock->batch_lot}) belum berada di bin QRT.\n\nSilakan lakukan Bin-to-Bin transfer terlebih dahulu ke salah satu bin: " . implode(', ', $qrtBinCodes);
                    continue;
                }

                // Validate stock has quantity
                if ($stock->qty_on_hand <= 0) {
                    $errors[] = "Material {$stock->material->nama_material} (Batch: {$stock->batch_lot}) tidak memiliki stock.";
                    continue;
                }

                // Find existing IncomingGoodsItem for this material
                // For Re-QC, find ANY incoming item with same material+batch, regardless of status
                $incomingItem = IncomingGoodsItem::where('material_id', $stock->material_id)
                    ->where('batch_lot', $stock->batch_lot)
                    ->orderBy('created_at', 'desc') // Get the latest one
                    ->first();

                if (!$incomingItem) {
                    // This shouldn't happen for Re-QC since material should have been received before
                    $errors[] = "Material {$stock->material->nama_material} (Batch: {$stock->batch_lot}) belum pernah masuk sistem (tidak ada record incoming). Tidak dapat di Re-QC.";
                    \Log::error('Re-QC: No incoming item found', [
                        'material_id' => $stock->material_id,
                        'batch_lot' => $stock->batch_lot
                    ]);
                    continue;
                }

                // Reset status for Re-QC
                $oldStatus = $incomingItem->status_qc;
                $incomingItem->status_qc = 'To QC';
                $incomingItem->qty_unit = $stock->qty_on_hand;
                
                // Append RE-QC marker to keterangan
                $reqcCount = QcReqcHistory::where('inventory_stock_id', $stock->id)->count() + 1;
                $incomingItem->keterangan = ($incomingItem->keterangan ? $incomingItem->keterangan . ' | ' : '') 
                    . "RE-QC #{$reqcCount} - " . now()->format('Y-m-d');
                $incomingItem->save();

                // Create Re-QC history record
                QcReqcHistory::create([
                    'inventory_stock_id' => $stock->id,
                    'incoming_item_id' => $incomingItem->id,
                    'reqc_number' => $this->generateReqcNumber(),
                    'old_status' => $oldStatus,
                    'old_exp_date' => $stock->exp_date,
                    'reason' => 'Material Expired',
                    'initiated_by' => auth()->id(),
                    'initiated_at' => now(),
                    'status' => 'PENDING',
                ]);

                $processedCount++;
            }

            if ($processedCount === 0) {
                DB::rollBack();
                \Log::warning('⚠️ No materials processed', ['errors' => $errors]);
                return redirect()->back()
                    ->withErrors(['message' => implode("\n\n", $errors)]);
            }

            DB::commit();
            \Log::info('✅ Re-QC completed successfully', ['processed_count' => $processedCount]);

            // Success - redirect to QC page using Inertia redirect
            return redirect()->route('transaction.quality-control')
                ->with('success', "{$processedCount} material siap untuk Re-QC.");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ Re-QC failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return back with error message
            return redirect()->back()
                ->withErrors(['message' => 'Gagal memproses Re-QC: ' . $e->getMessage()]);
        }
    }

    /**
     * Create new IncomingGood + IncomingGoodsItem for materials never QC'd before
     */
    private function createNewIncomingForReqc(InventoryStock $stock)
    {
        // Get supplier_id - use material's supplier or find first available supplier
        $supplierId = $stock->material->supplier_id;
        
        if (!$supplierId) {
            // Fallback: get first supplier or create a default one
            $supplier = \App\Models\Supplier::first();
            if (!$supplier) {
                throw new \Exception("Tidak ada supplier tersedia di sistem. Tambahkan supplier terlebih dahulu.");
            }
            $supplierId = $supplier->id;
            \Log::warning('⚠️ Material has no supplier, using fallback', [
                'material_id' => $stock->material_id,
                'fallback_supplier_id' => $supplierId
            ]);
        }
        
        // Create IncomingGood container
        $incomingGood = IncomingGood::create([
            'incoming_number' => $this->generateReqcNumber(),
            'no_surat_jalan' => 'RE-QC-' . time(),
            'po_id' => null,
            'supplier_id' => $supplierId, // Now guaranteed to have a value
            'kategori' => 'Raw Material', // Use valid ENUM value
            'status' => 'Received',
            'received_by' => auth()->id(),
            'tanggal_terima' => now(),
        ]);

        // Create IncomingGoodsItem
        $incomingItem = IncomingGoodsItem::create([
            'incoming_id' => $incomingGood->id,
            'material_id' => $stock->material_id,
            'batch_lot' => $stock->batch_lot,
            'exp_date' => $stock->exp_date,
            'qty_wadah' => 1, // Default
            'qty_unit' => $stock->qty_on_hand,
            'satuan' => $stock->uom,
            'status_qc' => 'To QC',
            'bin_target' => $stock->bin->bin_code,
            'keterangan' => 'RE-QC #1 - Material Expired - ' . now()->format('Y-m-d'),
        ]);

        return $incomingItem;
    }

    /**
     * Generate unique REQC number
     */
    private function generateReqcNumber()
    {
        $date = date('Ymd');
        $lastReqc = QcReqcHistory::whereDate('created_at', today())->latest()->first();
        $sequence = $lastReqc ? (intval(substr($lastReqc->reqc_number, -4)) + 1) : 1;
        return "REQC/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}