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
use App\Models\QcReqcHistory;
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

        if ($quarantineBin) {
            // Search by bin location only, regardless of status (for Re-QC)
            $inventoryStock = InventoryStock::where([
                'material_id' => $item->material_id,
                'bin_id' => $quarantineBin->id,
            ])->first();
            
            if ($inventoryStock) {
                return $inventoryStock;
            }
        }

        // Fallback for Re-QC: Search in all QRT bins by material_id + batch_lot
        \Log::info('⚠️ Stock not found in bin_target, searching all QRT bins', [
            'material_id' => $item->material_id,
            'batch_lot' => $item->batch_lot,
            'bin_target' => $item->bin_target
        ]);
        
        $qrtBins = WarehouseBin::whereIn('bin_code', ['QRT-HALAL', 'QRT-NON HALAL', 'QRT-HALAL-AC'])->get();
        
        foreach ($qrtBins as $bin) {
            // Search by bin + material + batch, no status filter
            $inventoryStock = InventoryStock::where([
                'material_id' => $item->material_id,
                'bin_id' => $bin->id,
            ])->where('batch_lot', $item->batch_lot)
              ->first();
              
            if ($inventoryStock) {
                \Log::info('✅ Found stock in QRT bin', [
                    'bin_code' => $bin->bin_code,
                    'stock_id' => $inventoryStock->id,
                    'status' => $inventoryStock->status
                ]);
                return $inventoryStock;
            }
        }

        return null;
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
            if (in_array($item['statusQC'], ['PASS', 'REJECTED'])) {
                $qcDetail = QCChecklistDetail::whereHas('qcChecklist', function($query) use ($item) {
                    $query->where('incoming_item_id', $item['id']);
                })->first();

                $qtyDiambil = $qcDetail ? (float)$qcDetail->qty_sample : 0;
            }

            if (!isset($groupedItems[$key])) {
                // Inisialisasi item baru (mengambil data dari item pertama)
                $groupedItems[$key] = $item;
                $groupedItems[$key]['original_ids'] = [$item['id']];
                $groupedItems[$key]['qtyDatangTotal'] = (float)$item['qtyDatangTotal'];
                $groupedItems[$key]['qtyDiambilTotal'] = $qtyDiambil;
                $groupedItems[$key]['qtyReceived'] = (float)$item['qtyReceived'];
                $groupedItems[$key]['displayStatusQC'] = $item['statusQC'];
                $groupedItems[$key]['qtyDatangAwal'] = (float)$item['qtyDatangTotal'];
                
                // ⭐ Tambahkan Properti Wadah
                $groupedItems[$key]['qtyWadah'] = (int)$item['qtyWadah']; 
                $groupedItems[$key]['qtyUnitPerWadah'] = (float)$item['qtyUnitPerWadah'];

            } else {
                // Agregasi
                $groupedItems[$key]['qtyDatangAwal'] += (float)$item['qtyDatangTotal'];
                $groupedItems[$key]['qtyDiambilTotal'] += $qtyDiambil;
                $groupedItems[$key]['qtyReceived'] += (float)$item['qtyReceived'];
                $groupedItems[$key]['original_ids'][] = $item['id'];
                
                // ⭐ Tambahkan Agregasi Qty Wadah (Jika menggunakan IncomingGoodsItem, ini harusnya sama atau diagregasi)
                $groupedItems[$key]['qtyWadah'] += (int)$item['qtyWadah'];

                if ($item['statusQC'] === 'To QC') {
                    $groupedItems[$key]['displayStatusQC'] = 'To QC';
                }
            }
        }
        
        // Finalisasi data
        $finalItems = array_values($groupedItems);
        
        // Tentukan nilai Qty Diambil dan Status Display akhir
        foreach ($finalItems as &$item) {
            if ($item['displayStatusQC'] === 'To QC') {
                $item['qtyDiambilTotal'] = 0;
                $item['qtyReceived'] = $item['qtyDatangAwal']; // Sebelum ada pengambilan sampel
            } else {
                // Setelah QC, total QTY Received adalah QTY yang tersisa (stok QRT)
                // QtyUnitPerWadah tidak perlu dihitung ulang karena seharusnya sama di satu batch/kode item
            }
        }
        
        return $finalItems;
    }

    public function index(Request $request)
    {
        // Get items that need QC or have completed QC
        $query = IncomingGoodsItem::with([
            'incomingGood',
            'incomingGood.purchaseOrder',
            'incomingGood.supplier',
            'material',
            'qcChecklist',
            'qcChecklist.qcChecklistDetail'
        ]);

        // --- FILTERING ---
        // 1. Status Filter (Modified Default)
        if ($request->has('status') && $request->status != '') {
            $status = $request->status;
            if ($status === 'To QC') {
                $query->where('status_qc', 'To QC');
            } elseif ($status === 'PASS') {
                $query->where('status_qc', 'PASS');
            } elseif ($status === 'REJECTED') {
                $query->where('status_qc', 'REJECTED');
            } elseif ($status === 'Completed') {
                $query->whereIn('status_qc', ['PASS', 'REJECTED']);
            }
        } else {
             // Default: Show All relevant statuses
             $query->whereIn('status_qc', ['To QC', 'PASS', 'REJECTED']);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('batch_lot', 'LIKE', "%{$search}%")
                  ->orWhereHas('material', function($q2) use ($search) {
                      $q2->where('kode_item', 'LIKE', "%{$search}%")
                         ->orWhere('nama_material', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('incomingGood', function($q3) use ($search) {
                      $q3->where('incoming_number', 'LIKE', "%{$search}%")
                         ->orWhere('no_surat_jalan', 'LIKE', "%{$search}%")
                         ->orWhereHas('purchaseOrder', function($q4) use ($search) {
                             $q4->where('no_po', 'LIKE', "%{$search}%");
                         });
                  });
            });
        }

        if ($request->has('date_start') && $request->date_start != '') {
             $query->whereHas('incomingGood', fn($q) => $q->whereDate('tanggal_terima', '>=', $request->date_start));
        }
        
        if ($request->has('date_end') && $request->date_end != '') {
             $query->whereHas('incomingGood', fn($q) => $q->whereDate('tanggal_terima', '<=', $request->date_end));
        }
        // ----------------

        $itemsToQCCollection = $query->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($item) {

            // ⭐ LOGIKA 1: Ambil QTY dari Inventory Stock QRT (Qty yang belum di-QC/Qty yang tersisa)
            $inventoryStock = $this->getQuarantineStock($item);
            
            // Qty yang saat ini ada di Bin Karantina (setelah dikurangi sampel)
            $qtyCurrentStock = $inventoryStock ? $inventoryStock->qty_on_hand : (
                $item->status_qc === 'To QC' ? (float)($item->qty_wadah * $item->getOriginal('qty_unit')) : 0.0
            );

            // Ambil Qty Sampel (Qty Diambil) dari Detail QC, jika ada.
            $qcDetail = $item->qcChecklist->qcChecklistDetail ?? null;
            $qtySampleTaken = $qcDetail ? (float)$qcDetail->qty_sample : 0.0;
            
            // Ambil catatan QC, jika ada
            $catatanQc = $qcDetail ? $qcDetail->catatan_qc : null;
            
            // Ambil Qty Unit Per Wadah (Qty per Box)
            $qtyUnitPerWadah = (float)$item->getOriginal('qty_unit'); // Nilai asli yang diisi saat GR

            // ⭐ PERUBAHAN UTAMA UNTUK MENGHITUNG QTY DATANG TOTAL (TOTAL AWAL)
            // Ini adalah QTY TOTAL (unit) saat material datang.
            $qtyDatangTotal = (float)$item->qty_wadah * $qtyUnitPerWadah;

            // ⭐ QTY WADAH FISIK
            // Jumlah wadah yang datang.
            $qtyWadah = (int)$item->qty_wadah;

            // ⭐ CHECK RE-QC STATUS
            // Get Re-QC history for this item
            $reqcHistory = [];
            $isReqc = false;
            $reqcCount = 0;
            
            if ($inventoryStock) {
                $reqcHistory = QcReqcHistory::where('inventory_stock_id', $inventoryStock->id)
                    ->with('initiator')
                    ->orderBy('initiated_at', 'desc')
                    ->get()
                    ->map(function($h) {
                        return [
                            'reqc_number' => $h->reqc_number,
                            'old_status' => $h->old_status,
                            'new_status' => $h->new_status,
                            'old_exp_date' => $h->old_exp_date ? $h->old_exp_date->format('Y-m-d') : null,
                            'new_exp_date' => $h->new_exp_date ? $h->new_exp_date->format('Y-m-d') : null,
                            'reason' => $h->reason,
                            'initiated_by' => $h->initiator ? $h->initiator->name : null,
                            'initiated_at' => $h->initiated_at->format('d/m/Y H:i'),
                            'status' => $h->status,
                        ];
                    })
                    ->toArray();
                    
                $isReqc = count($reqcHistory) > 0;
                $reqcCount = count($reqcHistory);
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
                // Qty Datang Total: QTY Unit total sebelum ada QC/Sample
                'qtyDatangTotal' => (float)$qtyDatangTotal, 
                'qcSampleQty' => (float)$qtySampleTaken,

                // ⭐ Tambahkan Qty Wadah dan Qty Unit Per Wadah
                'qtyWadah' => $qtyWadah, 
                'qtyUnitPerWadah' => $qtyUnitPerWadah,

                // ⭐ RE-QC DATA
                'is_reqc' => $isReqc,
                'reqc_count' => $reqcCount,
                'reqc_history' => $reqcHistory,

                'noKendaraan' => $item->incomingGood->no_kendaraan,
                'namaDriver' => $item->incomingGood->nama_driver,
                'kategori' => $item->incomingGood->kategori,
                'qrCode' => $item->qr_code,
                'tanggalTerima' => $item->incomingGood->tanggal_terima ?? now()->toDateTimeString(),
                'catatanQC' => $catatanQc,
            ];
        })
        ->toArray();
        
        // Panggil fungsi pengelompokan
        $itemsToQC = $this->groupItemsToQC($itemsToQCCollection);

        // Sortir kembali agar yang 'To QC' muncul di atas
        usort($itemsToQC, function($a, $b) {
            $statusOrder = ['To QC' => 0, 'PASS' => 1, 'REJECTED' => 2];
            return $statusOrder[$a['displayStatusQC']] - $statusOrder[$b['displayStatusQC']];
        });
        
        // --- MANUAL PAGINATION ---
        $page = $request->input('page', 1);
        $perPage = $request->input('limit', 10);
        if ($perPage === 'all') $perPage = 1000;
        
        $offset = ($page * $perPage) - $perPage;
        $itemsToSlice = array_values($itemsToQC); 
        $itemsForCurrentPage = array_slice($itemsToSlice, $offset, $perPage, true);
        
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage, 
            count($itemsToQC), 
            $perPage, 
            $page, 
            ['path' => $request->url(), 'query' => $request->query()]
        );
        // -------------------------

        return Inertia::render('QualityControl', [
            'itemsToQC' => $paginatedItems, 
            'filters' => $request->only(['search', 'date_start', 'date_end', 'limit', 'status']),
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

                'defect_count' => 'nullable',
                'catatan_qc' => 'nullable|string',
                'hasil_qc' => 'required|in:PASS,REJECTED',
                'new_exp_date' => 'nullable|date|after:today', // For Re-QC PASS - new expired date
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
            
            // ⭐ CHECK IF THIS IS RE-QC
            $inventoryStock = $this->getQuarantineStock($incomingItem);
            $pendingReqc = null;
            $previousSampleTotal = 0;
            $isReqc = false;
            
            if ($inventoryStock) {
                $pendingReqc = QcReqcHistory::where('inventory_stock_id', $inventoryStock->id)
                    ->where('status', 'PENDING')
                    ->latest()
                    ->first();
                    
                if ($pendingReqc) {
                    $isReqc = true;
                    // Get all previous QC details for cumulative count
                    $allPreviousQc = QCChecklistDetail::whereHas('qcChecklist', function($q) use ($incomingItem) {
                        $q->where('incoming_item_id', $incomingItem->id);
                    })->sum('qty_sample');
                    
                    $previousSampleTotal = (float) $allPreviousQc;
                }
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
                // 'status' => 'KARANTINA', // REMOVED: Re-QC items might be RELEASED but in QRT bin
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
            // ⭐ Add cumulative sampling note if this is Re-QC
            $catatanQc = $validated['catatan_qc'] ?? '';
            if ($isReqc && $previousSampleTotal > 0) {
                $cumulativeTotal = $previousSampleTotal + $qtySample;
                $catatanQc = "[RE-QC] Sample sebelumnya: {$previousSampleTotal}, Sample baru: +{$qtySample}, Total kumulatif: {$cumulativeTotal}. " . $catatanQc;
            }
            
            $qcDetail = QCChecklistDetail::create([
                'qc_checklist_id' => $qcChecklist->id,
                'qty_sample' => $qtySample, // Current sample only, not cumulative
                'total_incoming' => $qtyIncomingOriginal,
                
                'jumlah_box_utuh' => 0, 
                'qty_box_utuh' => 0, 
                'jumlah_box_tidak_utuh' => 0, 
                'qty_box_tidak_utuh' => 0, 
                
                'uom' => $incomingItem->satuan,
                'defect_count' => $validated['defect_count'] ?? 0,
                'catatan_qc' => $catatanQc,
                'hasil_qc' => $validated['hasil_qc'],
                'qc_date' => now(),
                'qc_by' => Auth::id(),
            ]);

            // 5. Update incoming item status (dan Qty Unit yang tersisa)
            $incomingItem->update([
                'status_qc' => $validated['hasil_qc'],
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
            
            // ⭐ UPDATE RE-QC HISTORY IF THIS IS RE-QC
            if ($isReqc && $pendingReqc) {
                $newExpDate = isset($validated['new_exp_date']) ? $validated['new_exp_date'] : null;
                
                $pendingReqc->update([
                    'qc_checklist_id' => $qcChecklist->id,
                    'status' => 'COMPLETED',
                    'new_status' => $validated['hasil_qc'],
                    'new_exp_date' => $newExpDate,
                    'qty_sample_previous' => $previousSampleTotal,
                    'qty_sample_new' => $qtySample,
                    'completed_at' => now(),
                ]);
                
                // Update inventory stock exp_date if PASS with new exp date
                if ($validated['hasil_qc'] === 'PASS' && $newExpDate && $inventoryStock) {
                    $inventoryStock->exp_date = $newExpDate;
                    $inventoryStock->save();
                    
                    // Also update incoming item exp_date
                    $incomingItem->exp_date = $newExpDate;
                    $incomingItem->save();
                }
            }
            
            // Log the QC activity
            $activityDesc = $isReqc ? "[RE-QC] Pemeriksaan QC untuk {$incomingItem->material->nama_material}" : "Pemeriksaan QC untuk {$incomingItem->material->nama_material}";
            $this->logActivity($qcChecklist, $validated['hasil_qc'], [
                'description' => "{$activityDesc} dengan hasil {$validated['hasil_qc']}. Qty Tersisa: {$qtyAfterSample}.",
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

                // Update status stok QRT menjadi REJECTED (TETAP ADA DI BIN QRT)
                Log::info("QC REJECT: Updating inventory stock ID {$inventoryStockQRT->id} to REJECTED status");
                
                $inventoryStockQRT->status = 'REJECTED';
                $inventoryStockQRT->last_movement_date = now();
                $inventoryStockQRT->save();
                
                // Verify the update was successful
                $inventoryStockQRT->refresh();
                if ($inventoryStockQRT->status !== 'REJECTED') {
                    throw new \Exception("Failed to update inventory status to REJECTED. Current status: {$inventoryStockQRT->status}");
                }
                
                Log::info("QC REJECT: Successfully updated inventory stock ID {$inventoryStockQRT->id} to REJECTED");
                
                // Buat Stock Movement Status Change to REJECTED
                $rejectMovementNumber = $this->generateMovementNumber($movementSequence);
                $this->createRejectStatusMovement($incomingItem, $inventoryStockQRT, $returnNumber, $qtyAfterSample, $rejectMovementNumber);

                $successMessage = "QC REJECT! Return Slip: {$returnNumber}. Material wajib dilakukan pemindahan ke Bin Reject.";
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
        // $movementNumber passed from argument, do not regenerate!
        
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

    private function createRejectStatusMovement($incomingItem, $inventoryStock, $returnNumber, $qtyAfterSample, $movementNumber)
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
            'reference_type' => 'return_slip',
            'reference_id' => $returnNumber, 
            'movement_date' => now(),
            'executed_by' => Auth::id(),
            'notes' => "QC REJECT - Status stok di Bin Karantina diubah menjadi REJECTED. Menunggu pemindahan ke Bin Reject.",
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

    public function getQCDetail($id)
    {
        try {
            $incomingItem = IncomingGoodsItem::with([
                'incomingGood.purchaseOrder',
                'incomingGood.supplier',
                'material',
                'qcChecklist.qcChecklistDetail',
                'qcChecklist.qcBy'
            ])->findOrFail($id);

            $qcChecklist = $incomingItem->qcChecklist;
            $qcDetail = $qcChecklist->qcChecklistDetail ?? null;

            if (!$qcChecklist) {
                return response()->json([
                    'error' => 'QC data not found'
                ], 404);
            }

            // Get inventory stock for current quantity
            $inventoryStock = $this->getQuarantineStock($incomingItem);
            $qtyCurrentStock = $inventoryStock ? $inventoryStock->qty_on_hand : 0;

            // Get Re-QC info
            $isReqc = false;
            $reqcCount = 0;
            if ($inventoryStock) {
                $reqcCount = QcReqcHistory::where('inventory_stock_id', $inventoryStock->id)->count();
                $isReqc = $reqcCount > 0;
            }

            // Get Stock Movements for this material (batch_lot)
            $movements = [];
            if ($inventoryStock) {
                $stockMovements = StockMovement::where('material_id', $incomingItem->material_id)
                    ->where('batch_lot', $incomingItem->batch_lot)
                    ->with(['fromBin', 'toBin'])
                    ->orderBy('movement_date', 'desc')
                    ->limit(20) // Limit to last 20 movements
                    ->get();

                $movements = $stockMovements->map(function($movement) {
                    return [
                        'movement_type' => $movement->movement_type,
                        'movement_date' => $movement->movement_date->format('d/m/Y H:i'),
                        'qty' => (float)$movement->qty,
                        'uom' => $movement->uom,
                        'from_bin' => $movement->fromBin ? $movement->fromBin->bin_code : null,
                        'to_bin' => $movement->toBin ? $movement->toBin->bin_code : null,
                        'notes' => $movement->notes,
                    ];
                })->toArray();
            }

            $data = [
                'id' => $incomingItem->id,
                'kodeItem' => $incomingItem->material->kode_item ?? '',
                'namaMaterial' => $incomingItem->material->nama_material ?? '',
                'batchLot' => $incomingItem->batch_lot,
                'supplier' => $incomingItem->incomingGood->supplier->nama_supplier ?? '',
                'noPo' => $incomingItem->incomingGood->purchaseOrder->no_po ?? '',
                'noSuratJalan' => $incomingItem->incomingGood->no_surat_jalan,
                'statusQC' => $incomingItem->status_qc,
                'uom' => $incomingItem->satuan,
                
                // QC Quantities
                'qcSampleQty' => $qcDetail ? (float)$qcDetail->qty_sample : 0,
                'qtyDatangTotal' => (float)($incomingItem->qty_wadah * $incomingItem->getOriginal('qty_unit')),
                'qtyReceived' => (float)$qtyCurrentStock,
                
                // QC Details
                'catatanQC' => $qcDetail ? $qcDetail->catatan_qc : null,
                'no_form_checklist' => $qcChecklist->no_form_checklist,
                'kategori' => $qcChecklist->kategori,
                
                // User & Timestamp
                'qc_by_name' => $qcChecklist->qcBy ? $qcChecklist->qcBy->name : 'N/A',
                'qc_date' => $qcChecklist->tanggal_qc ? $qcChecklist->tanggal_qc->format('d/m/Y H:i') : 'N/A',
                
                // Re-QC Info
                'is_reqc' => $isReqc,
                'reqc_count' => $reqcCount,

                // Stock Movements History
                'movements' => $movements,
            ];

            return response()->json($data);

        } catch (\Exception $e) {
            \Log::error('Error fetching QC detail: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch QC details',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function generateMovementNumber($sequence = 1)
    {
        $date = date('Ymd');
        // Menggunakan sequence yang di-pass, bukan query database
        return "MOV/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}