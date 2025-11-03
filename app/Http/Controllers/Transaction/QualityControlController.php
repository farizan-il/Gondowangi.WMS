<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\GoodReceipt;
use App\Models\IncomingGood;
use App\Models\IncomingGoodsItem;
use App\Models\InventoryStock;
use App\Models\QCChecklist;
use App\Models\QCChecklistDetail;
use App\Models\QCPhoto;
use App\Models\ReturnSlip;
use App\Models\StockMovement;
use App\Models\WarehouseBin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Traits\ActivityLogger;

class QualityControlController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        // Get items that need QC (status_qc = 'To QC')
        $itemsToQC = IncomingGoodsItem::with([
            'incomingGood',
            'incomingGood.purchaseOrder',
            'incomingGood.supplier',
            'material'
        ])
        ->where('status_qc', 'To QC')
        ->orWhere('status_qc', 'PASS')
        ->orWhere('status_qc', 'REJECT')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'shipmentNumber' => $item->incomingGood->incoming_number,
                'noPo' => $item->incomingGood->po_id ?? '',
                'noSuratJalan' => $item->incomingGood->no_surat_jalan,
                'supplier' => $item->incomingGood->supplier->nama_supplier ?? '',
                'kodeItem' => $item->material->kode_item ?? '',
                'namaMaterial' => $item->material->nama_material ?? '',
                'batchLot' => $item->batch_lot,
                'expDate' => $item->exp_date,
                'qtyReceived' => $item->qty_unit,
                'uom' => $item->satuan,
                'statusQC' => $item->status_qc,
                'noKendaraan' => $item->incomingGood->no_kendaraan,
                'namaDriver' => $item->incomingGood->nama_driver,
                'kategori' => $item->incomingGood->kategori,
                'qrCode' => $item->qr_code,
                'tanggalTerima' => $item->incomingGood->tanggal_terima ?? now()->toDateTimeString(),
            ];
        });

        return Inertia::render('QualityControl', [
            'itemsToQC' => $itemsToQC,
        ]);
    }

    public function scanQR(Request $request)
    {
        try {
            $qrCode = $request->input('qr_code');
            
            // Validasi input
            if (empty($qrCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak boleh kosong'
                ], 400);
            }

            // Log untuk debugging
            Log::info('QR Code diterima: ' . $qrCode);
            
            // Parse QR code: IN/20250918/0001|RM-001|BATCH003|25|2025-11-15
            $qrParts = explode('|', $qrCode);
            
            if (count($qrParts) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format QR Code tidak valid!',
                    'detail' => 'Format yang benar: INCOMING_NUMBER|KODE_ITEM (contoh: IN/20250918/0001|RM-001)',
                    'received' => $qrCode
                ], 400);
            }

            $incomingNumber = trim($qrParts[0]);
            $kodeItem = trim($qrParts[1]);

            Log::info('Mencari item dengan Incoming: ' . $incomingNumber . ' dan Kode: ' . $kodeItem);

            // Cari incoming good dulu
            $incomingGood = IncomingGood::where('incoming_number', $incomingNumber)->first();
            
            if (!$incomingGood) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor Incoming tidak ditemukan!',
                    'detail' => "Incoming Number '{$incomingNumber}' tidak ada di database",
                    'hint' => 'Pastikan barang sudah di-input melalui menu Goods Receipt terlebih dahulu'
                ], 404);
            }

            // Cari material
            $material = \App\Models\Material::where('kode_item', $kodeItem)->first();
            
            if (!$material) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode Item tidak ditemukan!',
                    'detail' => "Kode Item '{$kodeItem}' tidak ada di master data",
                    'hint' => 'Pastikan kode item sudah terdaftar di Master Data'
                ], 404);
            }

            // Find the item
            $item = IncomingGoodsItem::with([
                'incomingGood.purchaseOrder',
                'incomingGood.supplier',
                'material'
            ])
            ->where('incoming_id', $incomingGood->id)
            ->where('material_id', $material->id)
            ->first();

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item tidak ditemukan!',
                    'detail' => "Kombinasi Incoming '{$incomingNumber}' dan Kode Item '{$kodeItem}' tidak ditemukan",
                    'hint' => 'Item mungkin belum di-input atau sudah dihapus'
                ], 404);
            }

            // Cek status QC
            if ($item->status_qc !== 'To QC') {
                return response()->json([
                    'success' => false,
                    'message' => 'Item tidak bisa di-QC!',
                    'detail' => "Item ini sudah di-QC dengan status: {$item->status_qc}",
                    'hint' => 'Hanya item dengan status "To QC" yang bisa di-proses'
                ], 400);
            }

            Log::info('Item ditemukan: ID ' . $item->id);

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditemukan!',
                'data' => [
                    'id' => $item->id,
                    'shipmentNumber' => $item->incomingGood->incoming_number,
                    'noPo' => $item->incomingGood->purchaseOrder->no_po ?? '',
                    'noSuratJalan' => $item->incomingGood->no_surat_jalan,
                    'supplier' => $item->incomingGood->supplier->nama_supplier ?? '',
                    'kodeItem' => $item->material->kode_item ?? '',
                    'namaMaterial' => $item->material->nama_material ?? '',
                    'batchLot' => $item->batch_lot,
                    'expDate' => $item->exp_date,
                    'qtyReceived' => $item->qty_unit,
                    'uom' => $item->satuan,
                    'statusQC' => $item->status_qc,
                    'noKendaraan' => $item->incomingGood->no_kendaraan,
                    'namaDriver' => $item->incomingGood->nama_driver,
                    'kategori' => $item->incomingGood->kategori,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error scanning QR: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem!',
                'detail' => $e->getMessage(),
                'hint' => 'Silakan hubungi IT Support jika masalah berlanjut'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'incoming_item_id' => 'required|exists:incoming_goods_items,id',
                'reference' => 'nullable|string|max:100',
                'kategori' => 'required|string',
                'jumlah_box_utuh' => 'required|integer|min:0',
                'qty_box_utuh' => 'required|numeric|min:0',
                'jumlah_box_tidak_utuh' => 'nullable|integer|min:0',
                'qty_box_tidak_utuh' => 'nullable|numeric|min:0',
                'defect_count' => 'nullable|integer|min:0',
                'catatan_qc' => 'nullable|string',
                'hasil_qc' => 'required|in:PASS,REJECT',
                'photos' => 'nullable|array',
                'photos.*' => 'image|max:5120',
            ]);

            DB::beginTransaction();

            // Get incoming item
            $incomingItem = IncomingGoodsItem::with(['incomingGood', 'material'])->findOrFail($validated['incoming_item_id']);

            // Cek apakah item sudah di-QC
            if ($incomingItem->status_qc !== 'To QC') {
                throw new \Exception("Item ini sudah di-QC dengan status: {$incomingItem->status_qc}");
            }

            // Generate checklist number
            $date = date('Ymd');
            $lastChecklist = QCChecklist::whereDate('created_at', today())->latest()->first();
            $sequence = $lastChecklist ? (intval(substr($lastChecklist->no_form_checklist, -4)) + 1) : 1;
            $checklistNumber = "QC/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // Create QC Checklist
            $qcChecklist = QCChecklist::create([
                'no_form_checklist' => $checklistNumber,
                'incoming_item_id' => $incomingItem->id,
                'incoming_id' => $incomingItem->incoming_id,
                'po_id' => $incomingItem->incomingGood->po_id,
                'no_surat_jalan' => $incomingItem->incomingGood->no_surat_jalan,
                'material_id' => $incomingItem->material_id,
                'supplier_id' => $incomingItem->incomingGood->supplier_id,
                'reference' => $validated['reference'] ?? null,
                'kategori' => $validated['kategori'],
                'no_kendaraan' => $incomingItem->incomingGood->no_kendaraan,
                'nama_driver' => $incomingItem->incomingGood->nama_driver,
                'tanggal_qc' => now(),
                'qc_by' => Auth::id(),
                'status' => 'Completed',
            ]);

            // Calculate total
            $totalIncoming = ($validated['qty_box_utuh'] ?? 0) + ($validated['qty_box_tidak_utuh'] ?? 0);

            // Create QC Detail
            $qcDetail = QCChecklistDetail::create([
                'qc_checklist_id' => $qcChecklist->id,
                'jumlah_box_utuh' => $validated['jumlah_box_utuh'],
                'qty_box_utuh' => $validated['qty_box_utuh'],
                'jumlah_box_tidak_utuh' => $validated['jumlah_box_tidak_utuh'] ?? 0,
                'qty_box_tidak_utuh' => $validated['qty_box_tidak_utuh'] ?? 0,
                'total_incoming' => $totalIncoming,
                'uom' => $incomingItem->satuan,
                'defect_count' => $validated['defect_count'] ?? 0,
                'catatan_qc' => $validated['catatan_qc'] ?? null,
                'hasil_qc' => $validated['hasil_qc'],
                'qc_date' => now(),
                'qc_by' => Auth::id(),
            ]);

            // Update incoming item status
            $incomingItem->update([
                'status_qc' => $validated['hasil_qc']
            ]);

            // Handle photo uploads
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = time() . '_' . uniqid() . '_' . $photo->getClientOriginalName();
                    $filePath = $photo->storeAs('qc_photos', $fileName, 'public');

                    QCPhoto::create([
                        'qc_checklist_id' => $qcChecklist->id,
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'file_size' => $photo->getSize(),
                        'mime_type' => $photo->getMimeType(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            // ========================================
            // PROSES BERDASARKAN HASIL QC
            // ========================================

            // Log the QC activity
            $this->logActivity($qcChecklist, $validated['hasil_qc'], [
                'description' => "QC Check for {$incomingItem->material->nama_material} resulted in {$validated['hasil_qc']}.",
                'material_id' => $incomingItem->material_id,
                'batch_lot' => $incomingItem->batch_lot,
                'qty_after' => $totalIncoming,
                'reference_document' => $qcChecklist->no_form_checklist,
            ]);
            
            if ($validated['hasil_qc'] === 'PASS') {
                // 1. CREATE GOOD RECEIPT
                $grNumber = $this->generateGRNumber();
                
                $goodReceipt = GoodReceipt::create([
                    'gr_number' => $grNumber,
                    'qc_checklist_id' => $qcChecklist->id,
                    'incoming_item_id' => $incomingItem->id,
                    'material_id' => $incomingItem->material_id,
                    'batch_lot' => $incomingItem->batch_lot,
                    'qty_received' => $totalIncoming,
                    'uom' => $incomingItem->satuan,
                    'status_material' => 'RELEASED', // Otomatis RELEASED jika PASS
                    'warehouse_location' => 'QRT', // Default quarantine bin, nanti dipindah via Putaway
                    'tanggal_gr' => now(),
                    'created_by' => Auth::id(),
                ]);

                // 2. CREATE/UPDATE INVENTORY STOCK
                $quarantineBin = WarehouseBin::where('bin_code', 'LIKE', 'QRT-HALAL')
                    ->where('status', 'available')
                    ->first();

                if (!$quarantineBin) {
                    throw new \Exception('Bin Karantina tidak ditemukan! Pastikan sudah dibuat di master warehouse bins.');
                }

                // Cek apakah sudah ada stock dengan batch/lot yang sama
                $inventoryStock = InventoryStock::where([
                    'material_id' => $incomingItem->material_id,
                    'warehouse_id' => $quarantineBin->warehouse_id,
                    'bin_id' => $quarantineBin->id,
                    'batch_lot' => $incomingItem->batch_lot,
                    'status' => 'RELEASED'
                ])->first();

                if ($inventoryStock) {
                    // Update existing stock
                    $inventoryStock->update([
                        'qty_on_hand' => $inventoryStock->qty_on_hand + $totalIncoming,
                        'qty_available' => ($inventoryStock->qty_on_hand + $totalIncoming) - $inventoryStock->qty_reserved,
                        'last_movement_date' => now(),
                    ]);
                } else {
                    // Create new stock
                    $inventoryStock = InventoryStock::create([
                        'material_id' => $incomingItem->material_id,
                        'warehouse_id' => $quarantineBin->warehouse_id,
                        'bin_id' => $quarantineBin->id,
                        'batch_lot' => $incomingItem->batch_lot,
                        'exp_date' => $incomingItem->exp_date,
                        'qty_on_hand' => $totalIncoming,
                        'qty_reserved' => 0,
                        'qty_available' => $totalIncoming,
                        'uom' => $incomingItem->satuan,
                        'status' => 'RELEASED',
                        'gr_id' => $goodReceipt->id,
                        'last_movement_date' => now(),
                    ]);
                }

                // 3. UPDATE BIN OCCUPANCY
                $quarantineBin->increment('current_items');

                // 4. CREATE STOCK MOVEMENT
                $movementNumber = $this->generateMovementNumber();
                
                StockMovement::create([
                    'movement_number' => $movementNumber,
                    'movement_type' => 'QC_RELEASE',
                    'material_id' => $incomingItem->material_id,
                    'batch_lot' => $incomingItem->batch_lot,
                    'from_warehouse_id' => null,
                    'from_bin_id' => null,
                    'to_warehouse_id' => $quarantineBin->warehouse_id,
                    'to_bin_id' => $quarantineBin->id,
                    'qty' => $totalIncoming,
                    'uom' => $incomingItem->satuan,
                    'reference_type' => 'good_receipt',
                    'reference_id' => $goodReceipt->id,
                    'movement_date' => now(),
                    'executed_by' => Auth::id(),
                    'notes' => "QC PASS - Material masuk ke quarantine bin {$quarantineBin->bin_code}",
                ]);

                $successMessage = "QC PASS berhasil! GR Number: {$grNumber}. Material siap untuk di-putaway.";
            } else {
                // HASIL QC = REJECT
                // 1. CREATE RETURN SLIP
                $returnNumber = $this->generateReturnNumber();
                
                ReturnSlip::create([
                    'return_number' => $returnNumber,
                    'qc_checklist_id' => $qcChecklist->id,
                    'incoming_item_id' => $incomingItem->id,
                    'material_id' => $incomingItem->material_id,
                    'supplier_id' => $incomingItem->incomingGood->supplier_id,
                    'batch_lot' => $incomingItem->batch_lot,
                    'qty_return' => $totalIncoming,
                    'uom' => $incomingItem->satuan,
                    'alasan_reject' => $validated['catatan_qc'] ?? 'Material tidak memenuhi standar QC',
                    'status' => 'Pending Return',
                    'tanggal_return' => now(),
                    'created_by' => Auth::id(),
                ]);

                $successMessage = "QC REJECT! Return Slip: {$returnNumber}. Material akan dikembalikan ke supplier.";
            }

            DB::commit();

            Log::info("QC berhasil disimpan: {$checklistNumber} dengan status {$validated['hasil_qc']}");

            return redirect()->back()->with('success', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            $errors = $e->errors();
            $errorMessage = "Validasi gagal:\n";
            foreach ($errors as $field => $messages) {
                $errorMessage .= "- " . implode(', ', $messages) . "\n";
            }
            
            return redirect()->back()->with('error', $errorMessage);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error menyimpan QC: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Gagal menyimpan QC: ' . $e->getMessage());
        }
    }

    // Helper methods untuk generate nomor
    private function generateGRNumber()
    {
        $date = date('Ymd');
        $lastGR = GoodReceipt::whereDate('created_at', today())->latest()->first();
        $sequence = $lastGR ? (intval(substr($lastGR->gr_number, -4)) + 1) : 1;
        return "GR/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    private function generateReturnNumber()
    {
        $date = date('Ymd');
        $lastReturn = ReturnSlip::whereDate('created_at', today())->latest()->first();
        $sequence = $lastReturn ? (intval(substr($lastReturn->return_number, -4)) + 1) : 1;
        return "RTN/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    private function generateMovementNumber()
    {
        $date = date('Ymd');
        $lastMovement = StockMovement::whereDate('created_at', today())->latest()->first();
        $sequence = $lastMovement ? (intval(substr($lastMovement->movement_number, -4)) + 1) : 1;
        return "MOV/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}