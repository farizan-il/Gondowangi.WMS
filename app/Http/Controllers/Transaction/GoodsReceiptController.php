<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\IncomingGood;
use App\Models\IncomingGoodsItem;
use App\Models\InventoryStock;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Material;
use App\Models\WarehouseBin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Smalot\PdfParser\Parser;
use App\Traits\ActivityLogger;

class GoodsReceiptController extends Controller
{
    use ActivityLogger;
    public function index(Request $request)
    {
        $query = IncomingGood::with([
            'purchaseOrder',
            'supplier',
            'items.material',
            'receiver'
        ])
        ->orderBy('created_at', 'desc');

        // Filter: Search (No PO, No SJ, No Kendaraan, Nama Driver)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('incoming_number', 'LIKE', "%{$search}%")
                  ->orWhere('no_surat_jalan', 'LIKE', "%{$search}%")
                  ->orWhere('po_id', 'LIKE', "%{$search}%")
                  ->orWhere('no_kendaraan', 'LIKE', "%{$search}%")
                  ->orWhere('nama_driver', 'LIKE', "%{$search}%");
            });
        }

        // // Filter: Supplier (REMOVED as per request)
        // if ($request->has('supplier_id') && $request->supplier_id != '') {
        //     $query->where('supplier_id', $request->supplier_id);
        // }

        // Filter: Date Range
        if ($request->has('date_start') && $request->date_start != '') {
            $query->whereDate('tanggal_terima', '>=', $request->date_start);
        }
        if ($request->has('date_end') && $request->date_end != '') {
            $query->whereDate('tanggal_terima', '<=', $request->date_end);
        }

        // Pagination with Dynamic Limit (Show Entries)
        $limit = $request->input('limit', 10); // Default 10
        // Jika limit 'all', kita bisa set ke angka besar atau handle khusus. 
        // Namun paginate() butuh integer. Kita set 1000 jika 'all' atau user kirim angka besar.
        if ($limit === 'all') {
            $limit = 1000;
        }

        $incomingGoods = $query->paginate($limit)
            ->withQueryString()
            ->through(function ($incoming) {
                // 1. Ambil semua item
                $items = $incoming->items->map(function ($item) {
                    return [
                        'kodeItem' => $item->material->kode_item ?? '',
                        'namaMaterial' => $item->material->nama_material ?? '',
                        'satuanMaterial' => $item->material->satuan ?? '',
                        'batchLot' => $item->batch_lot,
                        'expDate' => $item->exp_date,
                        'qtyWadah' => $item->qty_wadah,
                        'qtyUnit' => $item->qty_unit,
                        'kondisiBaik' => $item->kondisi_baik,
                        'kondisiTidakBaik' => $item->kondisi_tidak_baik,
                        'coaAda' => $item->coa_ada,
                        'coaTidakAda' => $item->coa_tidak_ada,
                        'labelMfgAda' => $item->label_mfg_ada,
                        'labelMfgTidakAda' => $item->label_mfg_tidak_ada,
                        'labelCoaSesuai' => $item->label_coa_sesuai,
                        'labelCoaTidakSesuai' => $item->label_coa_tidak_sesuai,
                        'pabrikPembuat' => $item->pabrik_pembuat,
                        'statusQC' => $item->status_qc,
                        'binTarget' => $item->bin_target,
                        'isHalal' => $item->is_halal,
                        'isNonHalal' => $item->is_non_halal,
                        'qrCode' => $item->qr_code,
                    ];
                });

                // 2. LOGIKA PENENTUAN STATUS GR BARU
                $isStillToQC = $items->contains(fn($item) => $item['statusQC'] === 'To QC');
                $finalStatus = $isStillToQC ? 'Proses' : 'Selesai';
                
                return [
                    'id' => $incoming->id,
                    'incomingNumber' => $incoming->incoming_number,
                    'noPo' => $incoming->purchaseOrder->no_po ?? '',
                    'noSuratJalan' => $incoming->no_surat_jalan,
                    'supplier' => $incoming->supplier->nama_supplier ?? '',
                    'tanggalTerima' => $incoming->tanggal_terima,
                    'noKendaraan' => $incoming->no_kendaraan,
                    'namaDriver' => $incoming->nama_driver,
                    'kategori' => $incoming->kategori,
                    'status' => $finalStatus, 
                    'items' => $items,
                ];
            });

        $suppliers = Supplier::where('status', 'active')
            ->orderBy('nama_supplier')
            ->get(['id', 'kode_supplier', 'nama_supplier']);

        $materials = Material::where('status', 'active')
            ->orderBy('kode_item')
            ->get()
            ->map(function ($material) {
                return [
                    'id' => $material->id,
                    'code' => $material->kode_item,
                    'name' => $material->nama_material,
                    'unit' => $material->satuan,
                    'mfg' => $material->defaultSupplier->nama_supplier ?? '',
                    'qcRequired' => $material->qc_required,
                    'kategori' => $material->kategori,
                    'subCategory' => $material->subkategori,
                    'halalStatus' => $material->halal_status,
                ];
            });

        return Inertia::render('PenerimaanBarang', [
            'shipments' => $incomingGoods,
            'suppliers' => $suppliers,
            'materials' => $materials,
            'filters' => $request->only(['search', 'date_start', 'date_end', 'limit']), // Send filters back for UI state
        ]);
    }

    public function parseErpPdf(Request $request)
    {
        $request->validate([
            'erp_pdf' => 'required|mimes:pdf|max:5120',
        ]);
        
        $pdfFile = $request->file('erp_pdf');

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfFile->getPathname());
            $text = $pdf->getText();
            
            // 1. NORMALISASI TEKS
            // Ubah semua newline, tab, dan spasi ganda menjadi SATU spasi tunggal.
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);

            $extractedData = [
                'incoming_number' => '',
                'no_surat_jalan' => 'N/A', // <--- DEFAULT VALUE (Permintaan Anda)
                'no_po' => '',
                'date' => '',
                'items' => [],
                'supplier_name' => '',
                'supplier_code' => '',
            ];
            
            // 2. HEADER EXTRACTION
            
            // Incoming Number
            if (preg_match('/Incoming Shipment\s*[:]\s*([A-Z]{2}\/[0-9]+)/i', $text, $matches)) {
                $extractedData['incoming_number'] = trim($matches[1]);
                if (IncomingGood::where('incoming_number', $extractedData['incoming_number'])->exists()) {
                     return response()->json(['error' => "Nomor {$extractedData['incoming_number']} sudah ada."], 422);
                }
            }
            
            // No PO (Anchor utama kita)
            if (preg_match('/(PO[0-9]+)/', $text, $poMatch)) {
                $extractedData['no_po'] = $poMatch[1];
                
                // --- LOGIKA BARU UNTUK NO SURAT JALAN ---
                // Idenya: No SJ pasti berada tepat SEBELUM No PO.
                // Regex: Ambil string ([A-Z0-9\/\.\-]+) sebelum spasi dan kata PO...
                // Kita gunakan preg_match dengan logika lookahead posisi PO
                
                // Cari kata apa saja (huruf/angka/garis miring/strip) tepat sebelum PO yang ditemukan
                // Pattern: KataSpasiPO -> ambil Katanya
                $escapedPO = preg_quote($poMatch[1], '/');
                if (preg_match('/([A-Z0-9\/\.\-]+)\s+' . $escapedPO . '/', $text, $sjMatch)) {
                    $candidateSJ = trim($sjMatch[1]);
                    
                    // Filter: Karena teks di-flatten, bisa jadi kata sebelumnya adalah Header Kolom
                    // Daftar kata yang MUNGKIN muncul jika No SJ kosong:
                    $blacklistWords = ['Order(Origin)', 'Origin)', 'Origin', 'Date', 'SJ', 'No', 'Input', 'Name', 'Truck'];
                    
                    // Jika kata yang ditemukan TIDAK ada di blacklist, berarti itu No SJ beneran
                    if (!in_array($candidateSJ, $blacklistWords) && strlen($candidateSJ) > 2) {
                        $extractedData['no_surat_jalan'] = $candidateSJ;
                    }
                }
                // -----------------------------------------
            }

            // Tanggal
            if (preg_match('/([0-9]{2}\/[0-9]{2}\/[0-9]{4})\s+([0-9]{2}:[0-9]{2}:[0-9]{2})/', $text, $dateMatch)) {
                $extractedData['date'] = trim("{$dateMatch[1]} {$dateMatch[2]}");
            }
            
            // Supplier
            if (preg_match('/Supplier Address\s*:\s*(.*?)(?=\s+(?:Contact|Address|Kp\.|Jl\.|Komp|Jalan|Desa|Kecamatan|, PT))/i', $text, $matches)) {
                 $extractedData['supplier_name'] = trim($matches[1]);
            }

            // 3. ITEM EXTRACTION (Global Scan)
            $globalPattern = '/\[(\d+)\]\s+(.*?)\s+([A-Za-z]{1,10})\s+([\d\.,]+)/';

            if (preg_match_all($globalPattern, $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $qtyString = str_replace('.', '', $match[4]); 
                    $qtyString = str_replace(',', '.', $qtyString); 

                    $extractedData['items'][] = [
                        'kode_material' => trim($match[1]), 
                        'description' => trim($match[2]),
                        'uom' => trim($match[3]),
                        'quantity' => floatval($qtyString),
                    ];
                }
            }

            // 4. FINALISASI
            // Format Tanggal
            if (!empty($extractedData['date'])) {
                try {
                    $dateObj = \DateTime::createFromFormat('d/m/Y H:i:s', $extractedData['date']);
                    if (!$dateObj) $dateObj = \DateTime::createFromFormat('d/m/Y', $extractedData['date']);
                    $extractedData['tanggal_terima'] = $dateObj ? $dateObj->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
                } catch (\Exception $e) { $extractedData['tanggal_terima'] = now()->format('Y-m-d\TH:i'); }
            } else {
                $extractedData['tanggal_terima'] = now()->format('Y-m-d\TH:i');
            }

            // Lookup Supplier
             if (!empty($extractedData['supplier_name'])) {
                $searchName = explode(',', $extractedData['supplier_name'])[0]; 
                $supplier = Supplier::where('nama_supplier', 'LIKE', '%' . $searchName . '%')->first();
                if ($supplier) {
                    $extractedData['supplier_name'] = $supplier->nama_supplier;
                    $extractedData['supplier_code'] = $supplier->kode_supplier;
                }
            }

            // 5. DETECT MULTIPLE ITEM CODES
            $uniqueItemCodes = array_unique(array_column($extractedData['items'], 'kode_material'));
            $hasMultipleItemCodes = count($uniqueItemCodes) > 1;
            
            $extractedData['has_multiple_item_codes'] = $hasMultipleItemCodes;
            $extractedData['unique_item_codes'] = array_values($uniqueItemCodes);
            
            // 6. GROUP ITEMS BY KODE_MATERIAL (untuk wizard mode)
            if ($hasMultipleItemCodes) {
                $extractedData['items_grouped_by_code'] = $this->groupItemsByCode($extractedData['items']);
            }

            return response()->json($extractedData);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memproses PDF.', 'details' => $e->getMessage()], 500); 
        }
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'noPo' => 'required|string|max:255',
            'noSuratJalan' => 'required|string|max:255',
            'supplier' => 'required|exists:suppliers,id',
            'noKendaraan' => 'required|string|max:50',
            'namaDriver' => 'required|string|max:255',
            'tanggalTerima' => 'required|date',
            'kategori' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.kodeItem' => 'required|exists:materials,id',
            'items.*.batchLot' => 'required|string|max:255',
            'items.*.expDate' => 'nullable|date',
            'items.*.qtyWadah' => 'required|numeric|min:1',
            'items.*.qtyUnit' => 'required|numeric|min:1',
            'items.*.binTarget' => 'required|string|max:255', // Memastikan binTarget divvalidasi
            'items.*.isHalal' => 'nullable|boolean',
            'items.*.isNonHalal' => 'nullable|boolean',
            'items.*.pabrikPembuat' => 'nullable|string|max:255',
            'items.*.kondisiBaik' => 'nullable|boolean',
            'items.*.kondisiTidakBaik' => 'nullable|boolean',
            'items.*.coaAda' => 'nullable|boolean',
            'items.*.coaTidakAda' => 'nullable|boolean',
            'items.*.labelMfgAda' => 'nullable|boolean',
            'items.*.labelMfgTidakAda' => 'nullable|boolean',
            'items.*.labelCoaSesuai' => 'nullable|boolean',
            'items.*.labelCoaTidakSesuai' => 'nullable|boolean',
        ]);

        $incomingNumberFromRequest = $request->input('incomingNumber');
        DB::beginTransaction();
        try {
            // Generate incoming number
            $date = date('Ymd');
            $lastIncoming = IncomingGood::whereDate('created_at', today())->latest()->first();
            $sequence = $lastIncoming ? (intval(substr($lastIncoming->incoming_number, -4)) + 1) : 1;
            
            $incomingNumber = $incomingNumberFromRequest;

            if (empty($incomingNumber)){
                $date = date('Ymd');
                $lastIncoming = IncomingGood::whereDate('created_at', today())->latest()->first();
                $sequence = $lastIncoming ? (intval(substr($lastIncoming->incoming_number, -4)) + 1) : 1;
                $incomingNumber = "IN/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }else {
                if (IncomingGood::where('incoming_number', $incomingNumber)->exists()) {
                    throw new \Exception("Nomor Incoming ERP ({$incomingNumber}) sudah ada dalam sistem.");
                }
            }

            $purchaseOrder = PurchaseOrder::firstOrCreate(
                ['no_po' => $validated['noPo']], // Kriteria pencarian
                [
                    // Data default jika PO baru dibuat
                    'supplier_id' => $validated['supplier'], 
                    'tanggal_po' => now(), 
                    'status' => 'Open', 
                    'created_by' => Auth::id() ?? 1
                ]
            );

            // 2. Gunakan ID ($purchaseOrder->id), BUKAN string ($validated['noPo'])
            $incoming = IncomingGood::create([
                'incoming_number' => $incomingNumber,
                'no_surat_jalan' => $validated['noSuratJalan'],
                
                'po_id' => $purchaseOrder->id, // <--- UBAH INI (Pakai ID dari database)
                
                'supplier_id' => $validated['supplier'],
                'no_kendaraan' => $validated['noKendaraan'],
                'nama_driver' => $validated['namaDriver'],
                'tanggal_terima' => $validated['tanggalTerima'],
                'kategori' => $validated['kategori'] ?? 'Raw Material',
                'status' => 'Karantina',
                'received_by' => Auth::id(),
            ]);

            // Dapatkan Warehouse ID (Asumsi Bin Target semua berada di Warehouse yang sama)
            $sampleBin = WarehouseBin::where('bin_code', $validated['items'][0]['binTarget'])->first();
            $warehouseId = $sampleBin ? $sampleBin->warehouse_id : 1; // Fallback ke ID 1 jika tidak ditemukan.

            // Create incoming items AND Inventory Stock
            foreach ($validated['items'] as $itemData) {
                $material = Material::find($itemData['kodeItem']);
                
                // Cari WarehouseBin berdasarkan kode yang diinput
                $binTarget = WarehouseBin::where('bin_code', $itemData['binTarget'])->first();

                if (!$binTarget) {
                    throw new \Exception("Warehouse Bin dengan kode {$itemData['binTarget']} tidak ditemukan.");
                }

                // HITUNG TOTAL QTY DARI QTY_WADAH * QTY_UNIT
                $jumlahWadah = (float) $itemData['qtyWadah'];
                $qtyPerWadah = (float) $itemData['qtyUnit'];
                $totalQtyReceived = $jumlahWadah * $qtyPerWadah;
                
                // Generate QR code
                $qrCode = $this->generateQRCode(
                    $incomingNumber,
                    $material->kode_item,
                    $itemData['batchLot'],
                    $totalQtyReceived,
                    $itemData['expDate'] ?? ''
                );

                // 1. CREATE INCOMING GOODS ITEM
                IncomingGoodsItem::create([
                    'incoming_id' => $incoming->id,
                    'material_id' => $material->id,
                    'batch_lot' => $itemData['batchLot'],
                    'exp_date' => $itemData['expDate'] ?? null,
                    'qty_wadah' => $qtyPerWadah,
                    'qty_unit' => $jumlahWadah, 

                    'satuan' => $material->satuan,
                    
                    // Semua field checklist kondisi/coa/label
                    'kondisi_baik' => $itemData['kondisiBaik'] ?? true,
                    'kondisi_tidak_baik' => $itemData['kondisiTidakBaik'] ?? true,
                    'coa_ada' => $itemData['coaAda'] ?? true,
                    'coa_tidak_ada' => $itemData['coaTidakAda'] ?? true,
                    'label_mfg_ada' => $itemData['labelMfgAda'] ?? true,
                    'label_mfg_tidak_ada' => $itemData['labelMfgTidakAda'] ?? true,
                    'label_coa_sesuai' => $itemData['labelCoaSesuai'] ?? true,
                    'label_coa_tidak_sesuai' => $itemData['labelCoaTidakSesuai'] ?? true,

                    'bin_target' => $itemData['binTarget'],
                    'is_halal' => $itemData['isHalal'] ?? true,
                    'is_non_halal' => $itemData['isNonHalal'] ?? true,
                    
                    'pabrik_pembuat' => $itemData['pabrikPembuat'] ?? '',
                    'status_qc' => 'To QC',
                    'qr_code' => $qrCode,
                ]);

                // 2. TAMBAHKAN STOK KE INVENTORY_STOCK (Ke Bin Karantina)
                // Cek apakah stok dengan Batch/Lot yang sama sudah ada di Bin Target QRT
                $inventory = InventoryStock::firstOrNew([
                    'material_id' => $material->id,
                    'warehouse_id' => $warehouseId, // Gunakan Warehouse ID Karantina
                    'bin_id' => $binTarget->id,
                    'batch_lot' => $itemData['batchLot'],
                    'status' => 'KARANTINA', // Status Karantina
                ]);
                
                // Jika stok baru, inisialisasi nilainya
                if (!$inventory->exists) {
                    $inventory->fill([
                        'exp_date' => $itemData['expDate'] ?? null,
                        'qty_on_hand' => 0, 
                        'qty_reserved' => 0,
                        'uom' => $material->satuan, 
                        'status' => 'KARANTINA', // Status Invetory: Karantina
                        'gr_id' => $incoming->id,
                        'last_movement_date' => now(),
                    ]);
                }
                
                // *** PERBAHARUAN KALKULASI SUDAH BENAR ***
                // Tambahkan Qty baru ke stok yang sudah ada/baru. $totalQtyReceived sudah hasil kali QTY_WADAH * QTY_UNIT.
                $inventory->qty_on_hand += $totalQtyReceived;
                $inventory->qty_available = $inventory->qty_on_hand; // Stok QRT dianggap available untuk QC
                $inventory->save();

                // Log activity for each item
                $this->logActivity($incoming, 'Create', [
                    'description' => "Penerimaan Material {$material->name} ({$material->code}) Batch {$itemData['batchLot']} ke Bin {$binTarget->location_code}. Qty: {$totalQtyReceived} {$material->uom}",
                    'material_id' => $material->id,
                    'batch_lot' => $itemData['batchLot'],
                    'exp_date' => $itemData['expDate'] ?? null,
                    'qty_before' => $inventory->qty_on_hand - $totalQtyReceived,
                    'qty_after' => $inventory->qty_on_hand, 
                    'bin_to' => $binTarget->id,
                    'reference_document' => $incoming->no_surat_jalan,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', "Penerimaan berhasil disimpan dengan nomor: {$incomingNumber}");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan penerimaan: ' . $e->getMessage());
        }
    }

    /**
     * Group items by kode_material for multi-item-code handling
     */
    private function groupItemsByCode(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $code = $item['kode_material'];
            if (!isset($grouped[$code])) {
                $grouped[$code] = [
                    'code' => $code,
                    'items' => []
                ];
            }
            $grouped[$code]['items'][] = $item;
        }
        return array_values($grouped);
    }

    /**
     * Store multiple shipments (untuk handle PDF dengan multiple item codes)
     */
    public function storeMultiple(Request $request)
    {
        $validated = $request->validate([
            'shipments' => 'required|array|min:1',
            'shipments.*.noPo' => 'required|string|max:255',
            'shipments.*.noSuratJalan' => 'required|string|max:255',
            'shipments.*.supplier' => 'required|exists:suppliers,id',
            'shipments.*.noKendaraan' => 'required|string|max:50',
            'shipments.*.namaDriver' => 'required|string|max:255',
            'shipments.*.tanggalTerima' => 'required|date',
            'shipments.*.kategori' => 'nullable|string|max:100',
            'shipments.*.incomingNumber' => 'required|string|max:255',
            'shipments.*.items' => 'required|array|min:1',
            'shipments.*.items.*.kodeItem' => 'required|exists:materials,id',
            'shipments.*.items.*.batchLot' => 'required|string|max:255',
            'shipments.*.items.*.expDate' => 'nullable|date',
            'shipments.*.items.*.qtyWadah' => 'required|numeric|min:1',
            'shipments.*.items.*.qtyUnit' => 'required|numeric|min:1',
            'shipments.*.items.*.binTarget' => 'required|string|max:255',
            'shipments.*.items.*.isHalal' => 'nullable|boolean',
            'shipments.*.items.*.isNonHalal' => 'nullable|boolean',
            'shipments.*.items.*.pabrikPembuat' => 'nullable|string|max:255',
            'shipments.*.items.*.kondisiBaik' => 'nullable|boolean',
            'shipments.*.items.*.kondisiTidakBaik' => 'nullable|boolean',
            'shipments.*.items.*.coaAda' => 'nullable|boolean',
            'shipments.*.items.*.coaTidakAda' => 'nullable|boolean',
            'shipments.*.items.*.labelMfgAda' => 'nullable|boolean',
            'shipments.*.items.*.labelMfgTidakAda' => 'nullable|boolean',
            'shipments.*.items.*.labelCoaSesuai' => 'nullable|boolean',
            'shipments.*.items.*.labelCoaTidakSesuai' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $createdShipments = [];

            foreach ($validated['shipments'] as $shipmentData) {
                // Gunakan incoming number dari PDF (sama untuk semua split)
                $incomingNumber = $shipmentData['incomingNumber'];
                
                // Check jika incoming number sudah ada (hanya cek sekali untuk yang pertama)
                if (count($createdShipments) === 0 && IncomingGood::where('incoming_number', $incomingNumber)->exists()) {
                    throw new \Exception("Nomor Incoming ({$incomingNumber}) sudah ada dalam sistem.");
                }

                // Create atau get PO
                $purchaseOrder = PurchaseOrder::firstOrCreate(
                    ['no_po' => $shipmentData['noPo']],
                    [
                        'supplier_id' => $shipmentData['supplier'],
                        'tanggal_po' => now(),
                        'status' => 'Open',
                        'created_by' => Auth::id() ?? 1
                    ]
                );

                // Create incoming good
                $incoming = IncomingGood::create([
                    'incoming_number' => $incomingNumber,
                    'no_surat_jalan' => $shipmentData['noSuratJalan'],
                    'po_id' => $purchaseOrder->id,
                    'supplier_id' => $shipmentData['supplier'],
                    'no_kendaraan' => $shipmentData['noKendaraan'],
                    'nama_driver' => $shipmentData['namaDriver'],
                    'tanggal_terima' => $shipmentData['tanggalTerima'],
                    'kategori' => $shipmentData['kategori'] ?? 'Raw Material',
                    'status' => 'Karantina',
                    'received_by' => Auth::id(),
                ]);

                // Get warehouse ID from first bin
                $sampleBin = WarehouseBin::where('bin_code', $shipmentData['items'][0]['binTarget'])->first();
                $warehouseId = $sampleBin ? $sampleBin->warehouse_id : 1;

                // Create items and inventory stock
                foreach ($shipmentData['items'] as $itemData) {
                    $material = Material::find($itemData['kodeItem']);
                    
                    $binTarget = WarehouseBin::where('bin_code', $itemData['binTarget'])->first();
                    if (!$binTarget) {
                        throw new \Exception("Warehouse Bin dengan kode {$itemData['binTarget']} tidak ditemukan.");
                    }

                    $jumlahWadah = (float) $itemData['qtyWadah'];
                    $qtyPerWadah = (float) $itemData['qtyUnit'];
                    $totalQtyReceived = $jumlahWadah * $qtyPerWadah;
                    
                    $qrCode = $this->generateQRCode(
                        $incomingNumber,
                        $material->kode_item,
                        $itemData['batchLot'],
                        $totalQtyReceived,
                        $itemData['expDate'] ?? ''
                    );

                    IncomingGoodsItem::create([
                        'incoming_id' => $incoming->id,
                        'material_id' => $material->id,
                        'batch_lot' => $itemData['batchLot'],
                        'exp_date' => $itemData['expDate'] ?? null,
                        'qty_wadah' => $qtyPerWadah,
                        'qty_unit' => $jumlahWadah,
                        'satuan' => $material->satuan,
                        'kondisi_baik' => $itemData['kondisiBaik'] ?? true,
                        'kondisi_tidak_baik' => $itemData['kondisiTidakBaik'] ?? false,
                        'coa_ada' => $itemData['coaAda'] ?? true,
                        'coa_tidak_ada' => $itemData['coaTidakAda'] ?? false,
                        'label_mfg_ada' => $itemData['labelMfgAda'] ?? true,
                        'label_mfg_tidak_ada' => $itemData['labelMfgTidakAda'] ?? false,
                        'label_coa_sesuai' => $itemData['labelCoaSesuai'] ?? true,
                        'label_coa_tidak_sesuai' => $itemData['labelCoaTidakSesuai'] ?? false,
                        'bin_target' => $itemData['binTarget'],
                        'is_halal' => $itemData['isHalal'] ?? false,
                        'is_non_halal' => $itemData['isNonHalal'] ?? false,
                        'pabrik_pembuat' => $itemData['pabrikPembuat'] ?? '',
                        'status_qc' => 'To QC',
                        'qr_code' => $qrCode,
                    ]);

                    // Update inventory stock
                    $inventory = InventoryStock::firstOrNew([
                        'material_id' => $material->id,
                        'warehouse_id' => $warehouseId,
                        'bin_id' => $binTarget->id,
                        'batch_lot' => $itemData['batchLot'],
                        'status' => 'KARANTINA',
                    ]);
                    
                    if (!$inventory->exists) {
                        $inventory->fill([
                            'exp_date' => $itemData['expDate'] ?? null,
                            'qty_on_hand' => 0,
                            'qty_reserved' => 0,
                            'uom' => $material->satuan,
                            'status' => 'KARANTINA',
                            'gr_id' => $incoming->id,
                            'last_movement_date' => now(),
                        ]);
                    }
                    
                    $inventory->qty_on_hand += $totalQtyReceived;
                    $inventory->qty_available = $inventory->qty_on_hand;
                    $inventory->save();

                    // Log activity
                    $this->logActivity($incoming, 'Create', [
                        'description' => "Penerimaan Material {$material->nama_material} ({$material->kode_item}) Batch {$itemData['batchLot']} ke Bin {$binTarget->bin_code}. Qty: {$totalQtyReceived} {$material->satuan}",
                        'material_id' => $material->id,
                        'batch_lot' => $itemData['batchLot'],
                        'exp_date' => $itemData['expDate'] ?? null,
                        'qty_before' => $inventory->qty_on_hand - $totalQtyReceived,
                        'qty_after' => $inventory->qty_on_hand,
                        'bin_to' => $binTarget->id,
                        'reference_document' => $incoming->no_surat_jalan,
                    ]);
                }

                $createdShipments[] = [
                    'id' => $incoming->id,
                    'incoming_number' => $incoming->incoming_number,
                    'no_surat_jalan' => $incoming->no_surat_jalan,
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'created_count' => count($createdShipments),
                'shipments' => $createdShipments,
                'message' => "Berhasil membuat " . count($createdShipments) . " penerimaan barang."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function generateQRCode($incomingNumber, $itemCode, $batchLot, $qty, $expDate)
    {
        return implode('|', [
            $incomingNumber,
            $itemCode,
            $batchLot,
            $qty,
            $expDate
        ]);
    }
}