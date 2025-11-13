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
    private function getQuarantineStock(IncomingGoodsItem $item)
    {
        $quarantineBin = WarehouseBin::where('bin_code', $item->bin_target)->first();

        if (!$quarantineBin) {
            return null;
        }

        $inventoryStock = InventoryStock::where([
            'material_id' => $item->material_id,
            'bin_id' => $quarantineBin->id,
            'status' => 'KARANTINA', 
        ])->first();

        return $inventoryStock;
    }

    private function groupItemsToQC(array $items)
    {
        $groupedItems = [];

        foreach ($items as $item) {
            // Gunakan kombinasi 3 field untuk key pengelompokan yang ketat
            $key = $item['shipmentNumber'] . '|' . $item['noPo'] . '|' . $item['kodeItem'];
            
            $qtyReceived = (float)$item['qtyReceived']; // Ini adalah QTY stok saat ini (QTY unit)

            // QTY Diambil hanya dihitung jika status sudah PASS atau REJECT
            $qtyDiambil = 0;
            if (in_array($item['statusQC'], ['PASS', 'REJECT'])) {
                // Di sini, kita asumsikan 'qty_sample' adalah QTY yang diambil saat QC (sampling)
                // Dan QTY yang tersisa di Inventory sudah dikurangi.
                
                // Mendapatkan QCChecklistDetail untuk menghitung QTY sampel yang diambil.
                $qcDetail = QCChecklistDetail::whereHas('qcChecklist', function($query) use ($item) {
                    $query->where('incoming_item_id', $item['id']);
                })->first();

                // Logika: Qty Diambil adalah Qty Sampel (diambil saat QC)
                $qtyDiambil = $qcDetail ? (float)$qcDetail->qty_sample : 0;
            }

            if (!isset($groupedItems[$key])) {
                // Inisialisasi item baru (mengambil data dari item pertama)
                $groupedItems[$key] = $item;
                $groupedItems[$key]['original_ids'] = [$item['id']]; // Simpan semua ID untuk referensi
                $groupedItems[$key]['qtyDatangTotal'] = (float)$item['qtyDatangTotal'];
                
                // QTY Diambil: 
                // Jika status "To QC", Qty Diambil harus 0.
                // Jika status sudah "PASS" atau "REJECT", kita ambil Qty Sampel dari *item saat ini*
                $groupedItems[$key]['qtyDiambilTotal'] = $qtyDiambil;
                
                // Qty Received total adalah QTY sisa yang sudah di Inventory/GR/Return
                // JIKA status masih To QC, Qty Received = Qty Datang Total.
                // JIKA status sudah PASS/REJECT, Qty Received adalah Qty yang tersisa di QRT (item.qtyReceived)
                $groupedItems[$key]['qtyReceived'] = (float)$item['qtyReceived']; 

                // Tentukan status yang ditampilkan: Ambil status "To QC" jika ada.
                $groupedItems[$key]['displayStatusQC'] = $item['statusQC'];

                // QTY DATANG: Ini adalah QTY fisik total awal.
                $groupedItems[$key]['qtyDatangAwal'] = (float)$item['qtyDatangTotal'];
                
            } else {
                // Agregasi Qty Datang Total dari semua Item di grup yang sama
                $groupedItems[$key]['qtyDatangAwal'] += (float)$item['qtyDatangTotal'];
                
                // Agregasi Qty Diambil Total
                $groupedItems[$key]['qtyDiambilTotal'] += $qtyDiambil;

                // Agregasi Qty Received (Stok yang ada di QRT)
                $groupedItems[$key]['qtyReceived'] += (float)$item['qtyReceived'];

                // Tambahkan ID
                $groupedItems[$key]['original_ids'][] = $item['id'];
                
                // JIKA salah satu item masih 'To QC', maka status grup adalah 'To QC'
                if ($item['statusQC'] === 'To QC') {
                    $groupedItems[$key]['displayStatusQC'] = 'To QC';
                }
            }
        }
        
        // Finalisasi data
        $finalItems = array_values($groupedItems);
        
        // Tentukan nilai Qty Diambil dan Status Display akhir
        foreach ($finalItems as &$item) {
            // Jika ada item dalam grup yang masih To QC, maka Qty Diambilnya 0
            if ($item['displayStatusQC'] === 'To QC') {
                $item['qtyDiambilTotal'] = 0;
            } else {
                // Jika sudah ada yang di QC (PASS/REJECT), ambil Qty Diambil Total yang sudah diakumulasi
            }
            
            // Perbarui Qty Received Display: 
            // Jika To QC, Qty Received adalah total Qty Datang Awal.
            // Jika sudah QC, Qty Received adalah total Qty stok saat ini (yang sudah diakumulasi).
            if ($item['displayStatusQC'] === 'To QC') {
                 $item['qtyReceived'] = $item['qtyDatangAwal']; // Sebelum ada pengambilan sampel
            } else {
                // Nilai yang sudah diakumulasi dari inventory stock QRT (setelah dikurangi sampel)
            }
        }
        
        return $finalItems;
    }

    public function index()
    {
        // Get items that need QC or have completed QC
        $itemsToQCCollection = IncomingGoodsItem::with([
            'incomingGood',
            'incomingGood.purchaseOrder',
            'incomingGood.supplier',
            'material',
            'qcChecklist', 
            'qcChecklist.qcChecklistDetail' 
        ])
        ->whereIn('status_qc', ['To QC', 'PASS', 'REJECT']) 
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($item) {

            // ⭐ LOGIKA 1: Ambil QTY dari Inventory Stock QRT (Qty yang belum di-QC/Qty yang tersisa)
            $inventoryStock = $this->getQuarantineStock($item);
            
            // Qty yang saat ini ada di Bin Karantina (setelah dikurangi sampel)
            $qtyCurrentStock = $inventoryStock ? $inventoryStock->qty_on_hand : $item->qty_unit;

            // Ambil Qty Sampel (Qty Diambil) dari Detail QC, jika ada.
            $qcDetail = $item->qcChecklist->qcChecklistDetail ?? null;
            $qtySampleTaken = $qcDetail ? (float)$qcDetail->qty_sample : 0.0;
            
            // Ambil catatan QC, jika ada
            $catatanQc = $qcDetail ? $qcDetail->catatan_qc : null;
            
            // ⭐ PERUBAHAN UTAMA UNTUK MENGHITUNG QTY DATANG TOTAL (TOTAL AWAL)
            if ($item->status_qc === 'To QC') {
                // Jika To QC, qty_unit item IncomingGoodsItem masih memegang nilai total awal (diisi saat GR)
                // Asumsi: Saat GR, qty_unit diisi dengan QTY TOTAL (qty_wadah * qty_unit per wadah)
                $qtyDatangTotal = (float)$item->getOriginal('qty_unit'); 
                
                // Atau jika Anda ingin menggunakan qty_wadah * qty_unit (asumsi qty_unit adalah QTY per Wadah)
                $qtyDatangTotal = (float)($item->qty_wadah * $item->getOriginal('qty_unit')); 
                // Kita gunakan nilai yang tersimpan di Inventory + Sample yang sudah diambil (jika ada pergerakan di luar QC)
                $qtyDatangTotal = (float)$qtyCurrentStock + $qtySampleTaken;

            } else {
                // Jika sudah di QC (PASS/REJECT), Qty Datang Total adalah Qty Sisa + Qty Sampel yang diambil
                $qtyDatangTotal = (float)$qtyCurrentStock + (float)$qtySampleTaken;
            }
            return [
                'id' => $item->id,
                'shipmentNumber' => $item->incomingGood->incoming_number,
                'noPo' => $item->incomingGood->purchaseOrder->no_po ?? ($item->incomingGood->po_id ?? ''),
                'noSuratJalan' => $item->incomingGood->no_surat_jalan,
                'supplier' => $item->incomingGood->supplier->nama_supplier ?? '',
                'kodeItem' => $item->material->kode_item ?? '',
                'namaMaterial' => $item->material->nama_material ?? '',
                'batchLot' => $item->batch_lot,
                'expDate' => $item->exp_date,
                
                'uom' => $item->satuan,
                'statusQC' => $item->status_qc,
                'qtyReceived' => (float)$qtyCurrentStock,
                'qtyDatangTotal' => (float)$qtyDatangTotal,
                'qcSampleQty' => (float)$qtySampleTaken,

                // 'qtyWadah' => (int)$item->qty_wadah, 
                // 'qtyUnitPerWadah' => (float)$qtyUnitPerWadah,

                'noKendaraan' => $item->incomingGood->no_kendaraan,
                'namaDriver' => $item->incomingGood->nama_driver,
                'kategori' => $item->incomingGood->kategori,
                'qrCode' => $item->qr_code,
                'tanggalTerima' => $item->incomingGood->tanggal_terima ?? now()->toDateTimeString(),
                'catatanQC' => $catatanQc,
            ];
        })
        ->toArray(); // Konversi ke array untuk grouping
        
        // Panggil fungsi pengelompokan
        $itemsToQC = $this->groupItemsToQC($itemsToQCCollection);

        // Sortir kembali agar yang 'To QC' muncul di atas
        usort($itemsToQC, function($a, $b) {
            $statusOrder = ['To QC' => 0, 'PASS' => 1, 'REJECT' => 2];
            return $statusOrder[$a['displayStatusQC']] - $statusOrder[$b['displayStatusQC']];
        });
        
        // Log::info('Grouped Items:', $itemsToQC); // Debugging

        return Inertia::render('QualityControl', [
            // Gunakan hasil pengelompokan
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

            $inventoryStock = $this->getQuarantineStock($item);
            $qtyCurrentStock = $inventoryStock ? $inventoryStock->qty_on_hand : $item->qty_unit;

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

            $qtyWadah = $item->qty_wadah;
            $qtyUnitPerWadah = $item->qty_unit; 
            $qtyDatangTotal = $item->qty_wadah * $item->qty_unit;
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
                
                'qty_sample' => 'required|numeric|min:0', // INPUT QTY SAMPEL

                'defect_count' => 'nullable|integer|min:0',
                'catatan_qc' => 'nullable|string',
                'hasil_qc' => 'required|in:PASS,REJECT',
                'photos' => 'nullable|array',
                'photos.*' => 'image|max:5120',
            ]);
            $movementSequence = $this->getNextMovementSequence();

            DB::beginTransaction();

            // Get incoming item
            $incomingItem = IncomingGoodsItem::with(['incomingGood', 'material'])->findOrFail($validated['incoming_item_id']);            
            
            // Cek apakah item sudah di-QC
            if ($incomingItem->status_qc !== 'To QC') {
                throw new \Exception("Item ini sudah di-QC dengan status: {$incomingItem->status_qc}");
            }
            
            $qtySample = (float) $validated['qty_sample'];
            $qtyIncomingOriginal = (float) $incomingItem->qty_unit;
            // $totalIncoming = (float) $incomingItem->qty_unit; 
            
            // 1. BUAT QC CHECKLIST (HARUS DI AWAL UNTUK MENDAPATKAN ID REFERENSI)
            $date = date('Ymd');
            $lastChecklist = QCChecklist::whereDate('created_at', today())->latest()->first();
            $sequence = $lastChecklist ? (intval(substr($lastChecklist->no_form_checklist, -4)) + 1) : 1;
            $checklistNumber = "QC/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

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

            // 2. CARI STOK QRT DARI INVENTORY (WAJIB DITEMUKAN)
            $quarantineBin = WarehouseBin::where('bin_code', $incomingItem->bin_target)->first();
            
            if (!$quarantineBin) {
                throw new \Exception("Bin Karantina dengan kode '{$incomingItem->bin_target}' tidak ditemukan. Harap cek data bin.");
            }
            
            $inventoryStockQRT = InventoryStock::where([
                'material_id' => $incomingItem->material_id,
                'bin_id' => $quarantineBin->id,
                'batch_lot' => $incomingItem->batch_lot,
                'status' => 'KARANTINA', // Status yang dicari dari proses GR
            ])->first(); 

            if (!$inventoryStockQRT) {
                 // Throw exception jika stok QRT tidak ditemukan
                 throw new \Exception("Stok QRT material ({$incomingItem->material->kode_item}, Batch {$incomingItem->batch_lot}) tidak ditemukan di Inventory. Pastikan proses Goods Receipt (GR) sudah selesai.");
            }

       
            
            // 3. Lakukan Pengurangan Stok Sampel
            if ($qtySample > 0) {
                if ($inventoryStockQRT->qty_on_hand < $qtySample) {
                    throw new \Exception("Stok QRT ({$inventoryStockQRT->qty_on_hand} {$inventoryStockQRT->uom}) tidak cukup untuk sampel {$qtySample} {$inventoryStockQRT->uom}.");
                }
                
                // Kurangi Stok di table INVENTORY_STOCK (Dari 50 jadi 40)
                $inventoryStockQRT->qty_on_hand -= $qtySample;
                $inventoryStockQRT->qty_available -= $qtySample; // Asumsi qty_reserved 0 atau sudah di-update
                $inventoryStockQRT->last_movement_date = now();
                $inventoryStockQRT->save();

                // Log pergerakan stok untuk sampel yang diambil
                $sampleMovementNumber = $this->generateMovementNumber($movementSequence);
                $this->createSampleMovement($incomingItem, $inventoryStockQRT, $qtySample, $qcChecklist->id, $sampleMovementNumber);
                
                // Naikkan sequence untuk pergerakan selanjutnya
                $movementSequence++;
            }
            
            // Hitung QTY yang tersisa setelah sampel diambil
            $qtyAfterSample = $inventoryStockQRT->qty_on_hand;

            // 4. Create QC Detail (Menggunakan qty_sample dan total_incoming tersisa)
            $qcDetail = QCChecklistDetail::create([
                'qc_checklist_id' => $qcChecklist->id,
                'qty_sample' => $qtySample,
                'total_incoming' => $qtyIncomingOriginal,
                
                'jumlah_box_utuh' => 0, 
                'qty_box_utuh' => 0, 
                'jumlah_box_tidak_utuh' => 0, 
                'qty_box_tidak_utuh' => 0, 
                
                'uom' => $incomingItem->satuan,
                'defect_count' => $validated['defect_count'] ?? 0,
                'catatan_qc' => $validated['catatan_qc'] ?? null,
                'hasil_qc' => $validated['hasil_qc'],
                'qc_date' => now(),
                'qc_by' => Auth::id(),
            ]);

            // 5. Update incoming item status (dan Qty Unit yang tersisa)
            $incomingItem->update([
                'status_qc' => $validated['hasil_qc'],
                'qty_unit' => $qtyAfterSample,
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
            
            // Log the QC activity
            $this->logActivity($qcChecklist, $validated['hasil_qc'], [
                'description' => "QC Check for {$incomingItem->material->nama_material} resulted in {$validated['hasil_qc']}. Qty Tersisa: {$qtyAfterSample}.",
                'material_id' => $incomingItem->material_id,
                'batch_lot' => $incomingItem->batch_lot,
                'qty_after' => $qtyAfterSample,
                'reference_document' => $qcChecklist->no_form_checklist,
            ]);
            
            // 6. PROSES BERDASARKAN HASIL QC (PASS/REJECT)
            if ($validated['hasil_qc'] === 'PASS') {
                $grNumber = $this->generateGRNumber();
                
                $goodReceipt = GoodReceipt::create([
                    'gr_number' => $grNumber,
                    'qc_checklist_id' => $qcChecklist->id,
                    'incoming_item_id' => $incomingItem->id,
                    'material_id' => $incomingItem->material_id,
                    'batch_lot' => $incomingItem->batch_lot,
                    'qty_received' => $qtyAfterSample, // QTY LULUS
                    'uom' => $incomingItem->satuan,
                    'status_material' => 'RELEASED', 
                    'warehouse_location' => 'QRT', 
                    'tanggal_gr' => now(),
                    'created_by' => Auth::id(),
                ]);

                // Update status stok QRT menjadi RELEASED
                $inventoryStockQRT->update([
                    'status' => 'RELEASED',
                    // !!! PERBAIKAN KRITIS !!!
                    // Menggunakan ID IncomingGood (target FK database) untuk menghindari crash
                    'gr_id' => $incomingItem->incoming_id, 
                ]);

                $releaseMovementNumber = $this->generateMovementNumber($movementSequence);
                $this->createReleaseMovement($incomingItem, $inventoryStockQRT, $goodReceipt, $qtyAfterSample, $releaseMovementNumber);
                
                $successMessage = "QC PASS berhasil! GR Number: {$grNumber}. Material siap untuk di-putaway.";
                
            } else {
                $returnNumber = $this->generateReturnNumber();
                
                ReturnSlip::create([
                    'return_number' => $returnNumber,
                    'qc_checklist_id' => $qcChecklist->id,
                    'incoming_item_id' => $incomingItem->id,
                    'material_id' => $incomingItem->material_id,
                    'supplier_id' => $incomingItem->incomingGood->supplier_id,
                    'batch_lot' => $incomingItem->batch_lot,
                    'qty_return' => $qtyAfterSample, // QTY SISA (REJECT)
                    'uom' => $incomingItem->satuan,
                    'alasan_reject' => $validated['catatan_qc'] ?? 'Material tidak memenuhi standar QC',
                    'status' => 'Pending Return',
                    'tanggal_return' => now(),
                    'created_by' => Auth::id(),
                ]);

                // Hapus stok sisa dari inventory
                $inventoryStockQRT->update([
                    'qty_on_hand' => 0,
                    'qty_available' => 0,
                    'status' => 'REJECTED', 
                ]);
                
                // Buat Stock Movement Reject
                $rejectMovementNumber = $this->generateMovementNumber($movementSequence);
                $this->createRejectMovement($incomingItem, $quarantineBin, $returnNumber, $qtyAfterSample, $rejectMovementNumber);

                $successMessage = "QC REJECT! Return Slip: {$returnNumber}. Material akan dikembalikan ke supplier.";
            }

            DB::commit();

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
            \Illuminate\Support\Facades\Log::error('Error menyimpan QC: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal menyimpan QC: ' . $e->getMessage());
        }
    }

    private function createSampleMovement($incomingItem, $inventoryStock, $qtySample, $qcChecklistId, $movementNumber)
    {
        $movementNumber = $this->generateMovementNumber();
        
        StockMovement::create([
            'movement_number' => $movementNumber,
            'movement_type' => 'QC_SAMPLING',
            'material_id' => $incomingItem->material_id,
            'batch_lot' => $incomingItem->batch_lot,
            'from_warehouse_id' => $inventoryStock->warehouse_id,
            'from_bin_id' => $inventoryStock->bin_id,
            'to_warehouse_id' => null, // Keluar dari WMS
            'to_bin_id' => null,
            'qty' => $qtySample * -1, // Kuantitas keluar (negatif)
            'uom' => $incomingItem->satuan,
            'reference_type' => 'qc_checklist',
            // --- PERBAIKI PENGAMBILAN ID ---
            'reference_id' => $qcChecklistId, 
            'movement_date' => now(),
            'executed_by' => Auth::id(),
            'notes' => "Pengambilan sampel QC sebesar {$qtySample} {$incomingItem->satuan}.",
        ]);
    }

    private function createReleaseMovement($incomingItem, $inventoryStock, $goodReceipt, $qtyAfterSample, $movementNumber)
    {
        

        StockMovement::create([
            'movement_number' => $movementNumber,
            'movement_type' => 'STATUS_CHANGE',
            'material_id' => $incomingItem->material_id,
            'batch_lot' => $incomingItem->batch_lot,
            'from_warehouse_id' => $inventoryStock->warehouse_id,
            'from_bin_id' => $inventoryStock->bin_id,
            'to_warehouse_id' => $inventoryStock->warehouse_id,
            'to_bin_id' => $inventoryStock->bin_id,
            'qty' => $qtyAfterSample,
            'uom' => $incomingItem->satuan,
            'reference_type' => 'good_receipt',
            'reference_id' => $goodReceipt->id,
            'movement_date' => now(),
            'executed_by' => Auth::id(),
            'notes' => "QC PASS - Status stok di Bin Karantina diubah menjadi RELEASED.",
        ]);
    }

    private function createRejectMovement($incomingItem, $quarantineBin, $returnNumber, $qtyAfterSample, $movementNumber)
    {
       

        StockMovement::create([
            'movement_number' => $movementNumber,
            'movement_type' => 'QC_REJECT', 
            'material_id' => $incomingItem->material_id,
            'batch_lot' => $incomingItem->batch_lot,
            'from_warehouse_id' => $quarantineBin->warehouse_id,
            'from_bin_id' => $quarantineBin->id,
            'to_warehouse_id' => null, // Keluar dari WMS
            'to_bin_id' => null,
            'qty' => $qtyAfterSample * -1, // Kuantitas keluar
            'uom' => $incomingItem->satuan,
            'reference_type' => 'return_slip',
            'reference_id' => $returnNumber, // Asumsi return slip menggunakan return_number sebagai reference
            'movement_date' => now(),
            'executed_by' => Auth::id(),
            'notes' => "QC REJECT - Stok dikurangi dari inventory (Qty: {$qtyAfterSample}) untuk dikembalikan.",
        ]);
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

    // Tambahkan helper baru untuk mendapatkan sequence terakhir dengan aman
    private function getNextMovementSequence()
    {
        // Menggunakan DB::transaction() mandiri untuk menjamin ATOMICITY dan LOCK
        // Gunakan LOCK FOR UPDATE pada query saat mencari nomor urut terakhir.
        $nextSequence = DB::transaction(function () {
            $today = now()->toDateString();
            
            // Cari pergerakan terakhir hari ini, dan kunci baris tersebut (jika ditemukan)
            $lastMovement = StockMovement::whereDate('created_at', $today)
                                        ->lockForUpdate() 
                                        ->orderBy('id', 'desc') // Pastikan ambil yang terakhir
                                        ->first();

            // Hitung sequence baru
            $sequence = $lastMovement 
                        ? (intval(substr($lastMovement->movement_number, -4)) + 1) 
                        : 1;

            return $sequence;
        });

        return $nextSequence;
    }

    private function generateMovementNumber($sequence = 1)
    {
        $date = date('Ymd');
        // Menggunakan sequence yang di-pass, bukan query database
        return "MOV/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}