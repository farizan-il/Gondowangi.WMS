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
            ->with('defaultSupplier')
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
        // ... (Validasi File tetap sama)
        $pdfFile = $request->file('erp_pdf');

        // --- INI ADALAH LOGIKA PARSING PDF SESUNGGUHNYA ---
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfFile->getPathname());
            $text = $pdf->getText();
            $text = preg_replace('/\s+/', ' ', $text); 

            $extractedData = [
                'incoming_number' => '',
                'no_surat_jalan' => '',
                'no_po' => '',
                'date' => '',
                'no_truck' => '', 
                'driver_name' => '', 
                'supplier_name' => '', 
                'supplier_code' => '',
                'items' => [],
            ];
            
            // =======================================================
            // 2. EKSTRAKSI DATA HEADER MENGGUNAKAN REGEX
            // =======================================================

            // A. Incoming Shipment Number (IN/27576)
            if (preg_match('/Incoming Shipment : ([A-Z]{2}\/[0-9]+)/', $text, $matches)) {
                $extractedData['incoming_number'] = trim($matches[1]);
            }
            
            // B. No SJ dan No PO (Mendukung pola yang diunggah)
            // Pola yang DICARI: No SJ (SJ2511000171), Order(Origin) (P064943), Date/Time
            // Kita mencari teks di sekitar baris "No SJ" dan "Order(Origin)"
            // Pola 1: Mencari di sekitar teks 'No SJ'
            if (preg_match('/No SJ\s+Order\(Origin\)\s+Date\s+Input by\s+No Truck\s+Driver Name\s+([A-Z0-9]+)\s+([A-Z0-9]+)\s+([0-9]{2}\/[0-9]{2}\/[0-9]{4})\s+([0-9]{2}:[0-9]{2}:[0-9]{2})/', $text, $matches)) {
                $extractedData['no_surat_jalan'] = trim($matches[1]); // SJ
                $extractedData['no_po'] = trim($matches[2]);          // PO
                $extractedData['date'] = trim("{$matches[3]} {$matches[4]}");
            } 
            // Pola 2 (Fallback untuk PDF lama jika ada): Mencari di baris setelah header tabel
            else if (preg_match('/(SJ[0-9]+|DO-[0-9]{2}-[0-9]+)\s+(P[0-9]+)\s+([0-9]{2}\/[0-9]{2}\/[0-9]{4})\s+([0-9]{2}:[0-9]{2}:[0-9]{2})/', $text, $matches)) {
                $extractedData['no_surat_jalan'] = trim($matches[1]);
                $extractedData['no_po'] = trim($matches[2]);
                $extractedData['date'] = trim("{$matches[3]} {$matches[4]}");
            }
            
            // C. Supplier Name
            if (preg_match('/Supplier Address : ([A-Za-z\s.,]+?)\s+Komp Industri/', $text, $matches)) {
                // Mencoba mengambil nama supplier yang paling dekat dengan 'Supplier Address :'
                // Pola untuk: Universal Lestari Grafika, PT
                $extractedData['supplier_name'] = trim($matches[1]);
            } else if (preg_match('/Supplier Address : ([A-Z]+\s*[A-Za-z\s]+\s*),/', $text, $matches)) {
                // Coba pola alternatif (seperti di PDF lama)
                $extractedData['supplier_name'] = trim($matches[1]);
            }


            // =======================================================
            // 3. EKSTRAKSI DETAIL ITEM (Pola: [CODE] Description UoM Quantity)
            // =======================================================

            // Pola yang diperbarui: [CODE] Deskripsi... UoM Quantity.
            // Deskripsi sekarang bisa berisi koma dan spasi panjang (misal: Almond & Ginseng Oil 60 ml - R23)
            // Pattern: \[([0-9]+)\]\s+(.*?)\s+([A-Za-z]{2,5})\s+([0-9\.,]+)
            $itemPattern = '/\[([0-9]+)\]\s+([A-Za-z0-9\s,&._-]+)\s+([A-Za-z]{2,5})\s+([0-9\.,]+)/';


            if (preg_match_all($itemPattern, $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    // Konversi '5.000,0000' ke '5000.0000'
                    $quantity = str_replace(['.', ','], ['', '.'], $match[4]); 
                    
                    $extractedData['items'][] = [
                        'kode_material' => trim($match[1]), 
                        'description' => trim($match[2]),
                        'uom' => trim($match[3]),
                        'quantity' => floatval($quantity),
                    ];
                }
            }


            // =======================================================
            // 4. PEMROSESAN AKHIR (Pencocokan Supplier dan Konversi Tanggal)
            // =======================================================
            
            // A. Konversi Tanggal ke format ISO untuk Vue
            // ... (Logika konversi tanggal tetap sama)
            if (!empty($extractedData['date'])) {
                try {
                    $date = \DateTime::createFromFormat('d/m/Y H:i:s', $extractedData['date']);
            
                    if ($date) {
                        $extractedData['tanggal_terima'] = $date->format('Y-m-d\TH:i'); 
                    } else {
                        $extractedData['tanggal_terima'] = now()->toDateTimeLocalString('minute');
                    }
            
                } catch (\Exception $e) {
                    $extractedData['tanggal_terima'] = now()->toDateTimeLocalString('minute');
                }
            } else {
                $extractedData['tanggal_terima'] = now()->toDateTimeLocalString('minute');
            }


            // B. Cari Supplier di database
            if (!empty($extractedData['supplier_name'])) {
                $supplier = Supplier::where('nama_supplier', 'LIKE', '%' . $extractedData['supplier_name'] . '%')
                                    ->orWhere('nama_supplier', trim($extractedData['supplier_name']))
                                    ->first();
                if ($supplier) {
                    $extractedData['supplier_name'] = $supplier->nama_supplier;
                    $extractedData['supplier_code'] = $supplier->kode_supplier;
                }
            }

            return response()->json($extractedData);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal memproses PDF. Pastikan file dalam format ERP yang didukung.',
                'details' => $e->getMessage()
            ], 500); 
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

            // Create incoming good record
            $incoming = IncomingGood::create([
                'incoming_number' => $incomingNumber,
                'no_surat_jalan' => $validated['noSuratJalan'],
                'po_id' => $validated['noPo'],
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
                    'qty_wadah' => $jumlahWadah,
                    'qty_unit' => $qtyPerWadah, 

                    'satuan' => $material->satuan,
                    
                    // Semua field checklist kondisi/coa/label
                    'kondisi_baik' => $itemData['kondisiBaik'] ?? false,
                    'kondisi_tidak_baik' => $itemData['kondisiTidakBaik'] ?? false,
                    'coa_ada' => $itemData['coaAda'] ?? false,
                    'coa_tidak_ada' => $itemData['coaTidakAda'] ?? false,
                    'label_mfg_ada' => $itemData['labelMfgAda'] ?? false,
                    'label_mfg_tidak_ada' => $itemData['labelMfgTidakAda'] ?? false,
                    'label_coa_sesuai' => $itemData['labelCoaSesuai'] ?? false,
                    'label_coa_tidak_sesuai' => $itemData['labelCoaTidakSesuai'] ?? false,

                    'bin_target' => $itemData['binTarget'],
                    'is_halal' => $itemData['isHalal'] ?? false,
                    'is_non_halal' => $itemData['isNonHalal'] ?? false,
                    
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