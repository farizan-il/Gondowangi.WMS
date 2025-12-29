<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\ReservationRequestItem; 
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Traits\ActivityLogger;
use Illuminate\Validation\ValidationException;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\Log;
use Spatie\PdfToText\Exceptions\PdfNotFound; 
use Spatie\PdfToText\Exceptions\CouldNotExtractText;
use Smalot\PdfParser\Parser;


class ReservationController extends Controller
{
    use ActivityLogger;

    private function mapItemToCamelCase($item)
    {
        $itemArray = is_object($item) ? $item->toArray() : $item;
        return [
            // FOH & RS
            'kodeItem' => $itemArray['kode_item'] ?? null,
            'keterangan' => $itemArray['keterangan'] ?? null,
            'qty' => $itemArray['qty'] !== null ? (float) $itemArray['qty'] : null,
            'uom' => $itemArray['uom'] ?? null,
            // Packaging / ADD
            'namaMaterial' => $itemArray['nama_material'] ?? null,
            'kodePM' => $itemArray['kode_pm'] ?? null,
            'jumlahPermintaan' => $itemArray['jumlah_permintaan'] !== null ? (float) $itemArray['jumlah_permintaan'] : null,
            'alasanPenambahan' => $itemArray['alasan_penambahan'] ?? null,
            // Raw Material
            'kodeBahan' => $itemArray['kode_bahan'] ?? null,
            'namaBahan' => $itemArray['nama_bahan'] ?? null,
            'jumlahKebutuhan' => $itemArray['jumlah_kebutuhan'] !== null ? (float) $itemArray['jumlah_kebutuhan'] : null,
            'jumlahKirim' => $itemArray['jumlah_kirim'] !== null ? (float) $itemArray['jumlah_kirim'] : null,
        ];
    }
    
    private function mapBatchDetailToCamelCase($res)
    {
        $resArray = is_object($res) ? $res->toArray() : $res;
        $materialCode = $res->material->kode_item ?? 'N/A';
        $materialName = $res->material->nama_material ?? 'N/A';

        return [
            'materialId' => $resArray['material_id'] ?? null,
            'materialCode' => $materialCode,
            'materialName' => $materialName,
            'qtyReserved' => (float) ($resArray['qty_reserved'] ?? 0),
            'batchLot' => $resArray['batch_lot'] ?? null,
            'warehouseId' => $resArray['warehouse_id'] ?? null,
            'binId' => $resArray['bin_id'] ?? null,
            'uom' => $resArray['uom'] ?? null,
            'expiryDate' => $resArray['expiry_date'] ? (new \DateTime($resArray['expiry_date']))->format('Y-m-d') : null,
            'reservationId' => $resArray['id'] ?? null,
        ];
    }
    
    private function mapReservationToCamelCase($req)
    {
        return [
            'id' => $req->id,
            'noReservasi' => $req->no_reservasi,
            'type' => strtolower($req->request_type), 
            'tanggalPermintaan' => $req->tanggal_permintaan,
            'status' => $req->status,
            'departemen' => $req->departemen,
            'alasanReservasi' => $req->alasan_reservasi,
            'namaProduk' => $req->nama_produk,
            'kodeProduk' => $req->kode_produk,
            'noBetsFilling' => $req->no_bets_filling,
            'noBets' => $req->no_bets,
            'besarBets' => (float) $req->besar_bets,
            'requestedBy' => $req->requested_by,
            'approvedBy' => $req->approved_by,
            'approvedAt' => $req->approved_at,
            'rejectionReason' => $req->rejection_reason,
            'items' => $req->items->map(fn($item) => $this->mapItemToCamelCase($item)),
            'batchDetails' => $req->reservations->map(fn($res) => $this->mapBatchDetailToCamelCase($res)),
        ];
    }

    // ===================================================================
    // PARSING AND STOCK LOGIC
    // ===================================================================

    private function parseProductionOrderContent(string $pdfText)
{
    $headerData = [
        'productionOrderNo' => null,
        'productName' => null,
        'totalQuantity' => null
    ];

    // 1. Ambil Production Order N° (support angka + huruf, contoh: 025178FM)
    if (preg_match('/Production Order N°\s*:\s*([\w]+)/i', $pdfText, $matches)) {
        $headerData['productionOrderNo'] = $matches[1];
    }

    // 2. PERBAIKAN FINAL: Ambil Nama Produk dan Quantity
    // Normalisasi: Ubah semua line break dan multiple spaces jadi single space
    $normalizedText = preg_replace('/\s+/', ' ', $pdfText);
    
    // Log untuk debugging
    \Log::info('PDF Parsing - Normalized Text Sample:', [
        'production_order' => $headerData['productionOrderNo'],
        'text_sample' => substr($normalizedText, 0, 500)
    ]);
    
    // Ekstrak bagian tabel header (Source Document | Product | Quantity)
    // Lalu ambil data yang ada SETELAH header tersebut
    // Pattern 1: Format standar dengan "Source Document"
    if (preg_match('/Source Document\s+Product\s+Quantity\s+(.+?)\s+([\d.,]+)\s+(Pcs|PCS|pcs|Kg|kg|KG)/is', $normalizedText, $matches)) {
        // Group 1: Nama Produk (bisa mengandung kata "Quantity" yang tidak diinginkan)
        $rawProductName = trim($matches[1]);
        
        // SOLUSI MASALAH 1: Hapus kata "Quantity" yang terbawa di nama produk
        $cleanProductName = preg_replace('/Quantity/i', '', $rawProductName);
        $cleanProductName = trim($cleanProductName);
        $headerData['productName'] = $cleanProductName;
        
        // Group 2: Angka Quantity
        // SOLUSI MASALAH 2: Format dengan titik sebagai pemisah ribuan (1241 → 1.241)
        $qtyString = $matches[2];
        
        // Konversi ke float dulu (hapus titik ribuan, ubah koma ke titik desimal)
        $qtyClean = str_replace('.', '', $qtyString);  // 1.241,0000 → 1241,0000
        $qtyClean = str_replace(',', '.', $qtyClean);  // 1241,0000 → 1241.0000
        $qtyFloat = (float) $qtyClean;                 // → 1241.0
        
        // Kirim sebagai float agar bisa diparse oleh frontend
        $headerData['totalQuantity'] = $qtyFloat;
        
        \Log::info('PDF Parsing - Product & Quantity Extracted (Pattern 1):', [
            'product_name' => $headerData['productName'],
            'quantity' => $headerData['totalQuantity']
        ]);
    } 
    // Pattern 2: Fallback - Coba pattern yang lebih sederhana
    elseif (preg_match('/Product\s+Quantity[^\d]*([\d.,]+)\s*(Pcs|PCS|pcs|Kg|kg|KG)/is', $normalizedText, $matches)) {
        // Jika pattern 1 gagal, coba ambil quantity langsung setelah kata "Quantity"
        $qtyString = $matches[1];
        $qtyClean = str_replace('.', '', $qtyString);
        $qtyClean = str_replace(',', '.', $qtyClean);
        $qtyFloat = (float) $qtyClean;
        $headerData['totalQuantity'] = $qtyFloat;
        
        \Log::info('PDF Parsing - Quantity Extracted (Pattern 2 - Fallback):', [
            'quantity' => $headerData['totalQuantity']
        ]);
    } else {
        \Log::warning('PDF Parsing - Failed to extract Product Name and Quantity', [
            'production_order' => $headerData['productionOrderNo'],
            'text_around_product' => substr($normalizedText, strpos($normalizedText, 'Product') ?: 0, 200)
        ]);
    }

    // --- Bagian Bill Of Material (Daftar Item) ---
    $startKeyword = "Products to Consume";
    $startPos = strpos($pdfText, $startKeyword);
    
    if ($startPos !== false) {
        $BoMSection = substr($pdfText, $startPos);
        $allMaterialsFromPdf = [];
        
        // Regex Global untuk menangkap SEMUA item dari Bill of Material
        // Pattern: [Kode 5 digit] [Nama Material] [Qty dengan titik/koma] [UoM] [Status]
        $globalPattern = '/(\d{5})\s+(.+?)\s+([\d.,]+)\s+(Kg|Pcs|L|Unit)\s+RM/is';

        if (preg_match_all($globalPattern, $BoMSection, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                // Clean up material name: remove newlines and extra whitespace
                $materialName = trim(preg_replace('/\s+/', ' ', $m[2]));
                
                // Konversi quantity dengan cara yang sama
                $qtyItem = str_replace('.', '', $m[3]);
                $qtyItem = str_replace(',', '.', $qtyItem);
                
                $allMaterialsFromPdf[] = [
                    'code' => trim($m[1]),           // Kode Material (5 digit)
                    'name' => $materialName,         // Nama Material (cleaned)
                    'qty' => (float) $qtyItem,       // Quantity (float)
                    'uom' => trim($m[4])             // Unit of Measure
                ];
            }
        }
        $headerData['items'] = array_values($allMaterialsFromPdf);
    }

    return $headerData;
}

    /**
     * Mencari material di master data dan menghitung stok tersedia.
     */
    private function getMaterialAndStock($materialCode, $materialCategory, $uom)
    {
        // USER REQUEST: "Jangan dibatasin kategorinya".
        // Strategi Baru: Cari berdasarkan KODE ITEM sebagai prioritas utama.
        // Abaikan kategori dan satuan untuk pencarian awal.
        
        $material = Material::where('kode_item', $materialCode)->first();

        // Jika tidak ditemukan by Code, baru return null
        if (!$material) {
             return null;
        }

        // Ambil stok dari inventory stock - HANYA status RELEASED (sudah lolos QC)
        // Material yang masih KARANTINA tidak boleh direservasi
        $totalAvailableStock = InventoryStock::where('material_id', $material->id)
            ->where('status', 'RELEASED')
            ->sum('qty_available');

        return [
            'kodeBahan' => $material->kode_item,
             // Gunakan nama dari material master data, bukan dari PDF (untuk konsistensi)
            'namaBahan' => $material->nama_material,
            'kodePM' => $material->kode_item,
            'namaMaterial' => $material->nama_material,
            'satuan' => $material->satuan, 
            'stokAvailable' => (float) $totalAvailableStock,
            'kategori' => $material->kategori 
        ];
    }

    public function parseMaterials(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
            'request_type' => 'required|string',
        ]);

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($request->file('file')->getPathname());
            $pdfText = $pdf->getText(); 

            $parsedData = $this->parseProductionOrderContent($pdfText);
            // parseProductionOrderContent returns headerData directly with 'items' nested inside
            $allParsedItems = $parsedData['items'] ?? [];
            
            // Create header info without the 'items' key for response
            $headerInfo = [
                'productionOrderNo' => $parsedData['productionOrderNo'] ?? null,
                'productName' => $parsedData['productName'] ?? null,
                'totalQuantity' => $parsedData['totalQuantity'] ?? null,
            ];

            // ** VALIDASI DUPLICATE NO BETS SAAT IMPORT PDF **
            $requestType = $request->input('request_type');
            
            // Untuk Raw Material, cek apakah productionOrderNo (yang akan jadi noBets) sudah ada
            if ($requestType === 'raw-material' && !empty($parsedData['productionOrderNo'])) {
                $existingBets = ReservationRequest::where('no_bets', $parsedData['productionOrderNo'])->first();
                
                if ($existingBets) {
                    return response()->json([
                        'success' => false,
                        'message' => "❌ No Bets '{$parsedData['productionOrderNo']}' sudah digunakan pada reservasi {$existingBets->no_reservasi}. Import dibatalkan. Silakan gunakan file dengan No Bets yang berbeda."
                    ], 422);
                }
            }
            
            // Untuk Packaging/ADD, cek apakah productionOrderNo (yang akan jadi noBetsFilling) sudah ada
            if (($requestType === 'packaging' || $requestType === 'add') && !empty($parsedData['productionOrderNo'])) {
                $existingBetsFilling = ReservationRequest::where('no_bets_filling', $parsedData['productionOrderNo'])->first();
                
                if ($existingBetsFilling) {
                    return response()->json([
                        'success' => false,
                        'message' => "❌ No Bets Filling '{$parsedData['productionOrderNo']}' sudah digunakan pada reservasi {$existingBetsFilling->no_reservasi}. Import dibatalkan. Silakan gunakan file dengan No Bets yang berbeda."
                    ], 422);
                }
            }
            // ** END VALIDASI DUPLICATE NO BETS **

            // ** NEW: SMART CATEGORY FILTERING **
            // Map request type to expected material category
            $expectedCategory = null;
            if ($requestType === 'raw-material') {
                $expectedCategory = 'Raw Material';
            } elseif ($requestType === 'packaging' || $requestType === 'add') {
                $expectedCategory = 'Packaging';
            }
            // foh-rs tidak memiliki kategori specific, jadi $expectedCategory tetap null

            $resultMaterials = [];
            $notFoundMaterials = [];
            $skippedCategoryMismatch = []; // NEW: Items skipped due to category mismatch

            foreach ($allParsedItems as $parsedItem) {
                // Lookup ke database menggunakan fungsi getMaterialAndStock yang sudah ada di kode Anda
                $materialData = $this->getMaterialAndStock($parsedItem['code'], 'All', $parsedItem['uom']);
                
                if ($materialData) {
                    // NEW: Check category match
                    if ($expectedCategory && $materialData['kategori'] !== $expectedCategory) {
                        // Material found but category doesn't match
                        $skippedCategoryMismatch[] = [
                            'kode' => $parsedItem['code'],
                            'nama' => $materialData['namaBahan'] ?? $materialData['namaMaterial'],
                            'kategori' => $materialData['kategori'],
                            'expectedKategori' => $expectedCategory,
                            'message' => "Material '{$parsedItem['code']}' adalah '{$materialData['kategori']}', tidak sesuai dengan kategori yang dipilih '{$expectedCategory}'."
                        ];
                        continue; // Skip this item
                    }

                    $item = $materialData;
                    // Masukkan Qty dari PDF ke field yang sesuai kategori
                    if ($requestType === 'raw-material') {
                        $item['jumlahKebutuhan'] = $parsedItem['qty'];
                    } elseif ($requestType === 'packaging' || $requestType === 'add') {
                        // Round UP untuk packaging karena UoM adalah PCS, ROL (tidak bisa ada desimal)
                        $item['jumlahPermintaan'] = ceil($parsedItem['qty']);
                    } else {
                        $item['qty'] = $parsedItem['qty'];
                    }
                    $resultMaterials[] = $item;
                } else {
                    $notFoundMaterials[] = [
                        'kode' => $parsedItem['code'],
                        'message' => 'Tidak ditemukan di master data.'
                    ];
                }
            }

            return response()->json([
                'header' => $headerInfo,
                'materials' => $resultMaterials,
                'notFoundMaterials' => $notFoundMaterials,
                'skippedCategoryMismatch' => $skippedCategoryMismatch, // NEW
                'hasSkippedItems' => count($skippedCategoryMismatch) > 0, // NEW
                'message' => '✅ Sukses'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    public function index()
    {
        // Memuat 'reservations' (detail batch) beserta materialnya
        $reservations = ReservationRequest::with(['items', 'reservations.material'])
            ->where('requested_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Gunakan helper untuk mapping data awal ke view
        $mappedReservations = $reservations->map(fn($req) => $this->mapReservationToCamelCase($req));

        // Kirim data awal ke view
        return Inertia::render('Reservation', [
            'initialRequests' => $mappedReservations,
        ]);
    }

    public function getReservationsData() 
    {
        // Memuat 'reservations' (detail batch) beserta materialnya
        $reservations = ReservationRequest::with(['items', 'reservations.material'])
            ->where('requested_by', Auth::id())
            ->get();
        
        // Gunakan helper untuk mapping data AJAX
        $mappedReservations = $reservations->map(fn($req) => $this->mapReservationToCamelCase($req));
        
        return response()->json($mappedReservations);
    }
    
    /**
     * Endpoint API untuk mencari material secara dinamis, BERDASARKAN STOK yang tersedia.
     */
    public function searchMaterials(Request $request)
    {
        $query = $request->input('query');
        $type = $request->input('type');

        if (!$query) {
            return response()->json([]);
        }

        // Filter Setup: Gunakan field 'kategori' untuk semua tipe 
        $categoryFilter = null;

        if ($type === 'foh-rs') {
             // Opsional: Filter kategori untuk FOH jika diketahui (misal 'Office Supply' / 'Spare Part')
             // Saat ini dikosongkan agar tidak membatasi hasil pencarian FOH
             $categoryFilter = null; 
        } elseif ($type === 'packaging' || $type === 'add') {
            // FIXED: Gunakan 'Packaging' sesuai dengan format yang digunakan di parseMaterials (line 295)
            $categoryFilter = 'Packaging';
        } elseif ($type === 'raw-material') {
            $categoryFilter = 'Raw Material';
        } else {
            return response()->json([]);
        }

        // ** REVISI UTAMA: Mengambil data langsung dari InventoryStock **
        $materialsInStock = InventoryStock::join('materials', 'inventory_stock.material_id', '=', 'materials.id')
            ->select(
                'materials.id as materialId', 
                'materials.kode_item', 
                'materials.nama_material', 
                'materials.satuan'
            )
            // Menjumlahkan qty_available dari semua baris stok material yang sama
            ->selectRaw('SUM(inventory_stock.qty_available) as total_available_stock')
            // Apply Status Filter: HANYA ambil yang RELEASED (material yang sudah lolos QC)
            ->where('inventory_stock.status', 'RELEASED')
            
            // Apply Filters (Category)
            ->when($categoryFilter, function($q) use ($categoryFilter) {
                 $q->where('materials.kategori', $categoryFilter); 
            })
            ->where(function ($q) use ($query) {
                // Mencari berdasarkan kode item atau nama material
                $q->where('materials.kode_item', 'like', '%' . $query . '%')
                  ->orWhere('materials.nama_material', 'like', '%' . $query . '%');
            })
            // Mengelompokkan berdasarkan material untuk mendapatkan total stok
            ->groupBy('materials.id', 'materials.kode_item', 'materials.nama_material', 'materials.satuan')
            // Hanya menampilkan material yang memiliki stok tersedia > 0
            ->having('total_available_stock', '>', 0) 
            ->limit(100)
            ->get();


        $materials = $materialsInStock->map(function ($material) use ($type) {
            $base = [
                'id' => $material->materialId, // ID material
                'kodeItem' => $material->kode_item,
                'namaMaterial' => $material->nama_material,
                'satuan' => $material->satuan,
                'stokAvailable' => (float) $material->total_available_stock, // Stok Agregat
            ];

            // Sesuaikan output keys berdasarkan tipe request
            if ($type === 'foh-rs') {
                return [
                    ...$base,
                    // FIX: Gunakan nama_material jika deskripsi tidak ada/bermasalah
                    'keterangan' => $material->nama_material, 
                    'uom' => $material->satuan, // Menggunakan satuan sebagai uom
                ];
            } elseif ($type === 'packaging' || $type === 'add') {
                return [
                    ...$base,
                    'kodePM' => $material->kode_item,
                    'namaMaterial' => $material->nama_material,
                ];
            } elseif ($type === 'raw-material') {
                return [
                    ...$base,
                    'kodeBahan' => $material->kode_item,
                    'namaBahan' => $material->nama_material,
                ];
            }
            return $base;
        });

        return response()->json($materials);
    }
    
    private function allocateStock(ReservationRequest $reservationRequest, array $validatedItems, string $requestType): void
    {
        foreach ($validatedItems as $index => $item) {
            $materialCodeKey = '';
            $qtyKey = '';

            // Tentukan key kode material dan kuantitas permintaan
            if ($requestType === 'foh-rs') {
                $materialCodeKey = 'kodeItem';
                $qtyKey = 'qty';
            } elseif ($requestType === 'packaging' || $requestType === 'add') {
                $materialCodeKey = 'kodePM';
                $qtyKey = 'jumlahPermintaan';
            } elseif ($requestType === 'raw-material') {
                $materialCodeKey = 'kodeBahan';
                $qtyKey = 'jumlahKebutuhan';
            }

            $materialCode = $item[$materialCodeKey] ?? null;
            $requestedQty = (float) ($item[$qtyKey] ?? 0);

            if (!$materialCode || $requestedQty <= 0) {
                continue; // Skip jika tidak ada kode material atau kuantitas 0
            }
            
            // 1. Dapatkan Material ID
            $material = Material::where('kode_item', $materialCode)->firstOrFail();
            
            // 2. Dapatkan stok yang tersedia - HANYA yang RELEASED (sudah lolos QC)
        // Material KARANTINA tidak boleh dialokasikan untuk reservation
        $availableStocksQuery = InventoryStock::where('material_id', $material->id)
            ->where('status', 'RELEASED')
            ->where('qty_available', '>', 0)
            // ** CRITICAL FIX: Filter out EXPIRED materials **
            // FEFO hanya berlaku untuk material yang BELUM kadaluarsa!
            ->where(function($q) {
                $q->whereNull('exp_date')  // Material tanpa exp_date = OK
                  ->orWhere('exp_date', '>', now());  // Material belum kadaluarsa = OK
            });
            
            // ** START: LOGIKA PENGURUTAN FEFO/FIFO/QTY TERKECIL BARU (DI DATABASE) **
            
            // 2a. Prioritas 1: FEFO (First Expired First Out)
            //    - Urutkan berdasarkan exp_date ASC (tercepat kedaluwarsa).
            //    - Stok yang tidak punya exp_date (NULL) ditaruh di paling akhir.
            $availableStocksQuery->orderByRaw('ISNULL(exp_date) ASC, exp_date ASC');

            // 2b. Prioritas 2: FIFO (First In First Out)
            //    - Jika exp_date SAMA, urutkan berdasarkan tanggal kedatangan di batch_lot.
            //    - Asumsi format batch_lot: 14095 (5 char) + 131125 (6 char, dmy) + NP (sisa)
            //    - (Ini adalah sintaks MySQL. Jika Anda pakai DB lain, sintaksnya mungkin beda)
            try {
                // Menggunakan SUBSTRING(batch_lot, 6, 6) untuk mengambil '131125' dari '14095131125NP'
                // dan STR_TO_DATE untuk mengubahnya menjadi tanggal ('2025-11-13')
                $availableStocksQuery->orderByRaw("STR_TO_DATE(SUBSTRING(batch_lot, 6, 6), '%d%m%y') ASC");
            } catch (\Exception $e) {
                // Fallback jika DB (cth: SQLite saat testing) tidak support STR_TO_DATE
                // Ini tidak akan mengurutkan FIFO, tapi setidaknya tidak error.
                Log::warning("Gagal mengurutkan FIFO di database (DB mungkin tidak support STR_TO_DATE): " . $e->getMessage());
            }

            // 2c. Prioritas 3: Qty Terkecil (Smallest Quantity First)
            //    - Jika exp_date DAN tanggal kedatangan SAMA, urutkan berdasarkan qty_available ASC (terkecil dulu).
            //    - Ini memastikan stok dengan jumlah lebih kecil dialokasikan terlebih dahulu.
            $availableStocksQuery->orderBy('qty_available', 'ASC');

            // 2d. Ambil data yang SUDAH TERURUT SEMPURNA dari DB
            $availableStocks = $availableStocksQuery->get();
            
            // ** END: LOGIKA PENGURUTAN BARU **
            
            $remainingQtyToReserve = $requestedQty;

            foreach ($availableStocks as $stock) {
                if ($remainingQtyToReserve <= 0) break;

                $qtyToDeduct = min($remainingQtyToReserve, (float) $stock->qty_available);

                if ($qtyToDeduct > 0) {
                    // ** Lakukan perhitungan stok di PHP, bukan menggunakan DB::raw() **
                    $newAvailable = (float) $stock->qty_available - $qtyToDeduct;
                    $newReserved = (float) $stock->qty_reserved + $qtyToDeduct;

                    // 3. Kurangi qty_available di InventoryStock (Assign nilai numerik)
                    $stock->qty_available = $newAvailable;
                    $stock->qty_reserved = $newReserved; // Tambahkan ke reserved
                    $stock->save();
                    
                    // 4. Catat Reservasi di tabel 'reservations'
                    // Ini mencatat alokasi stok per batch/lot ke request
                    Reservation::create([
                        'reservation_no' => $reservationRequest->no_reservasi,
                        'reservation_request_id' => $reservationRequest->id,
                        'reservation_type' => $reservationRequest->request_type,
                        'material_id' => $material->id,
                        'warehouse_id' => $stock->warehouse_id,
                        'bin_id' => $stock->bin_id,
                        'batch_lot' => $stock->batch_lot,
                        'qty_reserved' => $qtyToDeduct,
                        'uom' => $stock->uom,
                        'status' => 'Reserved',
                        'reservation_date' => now(),
                        'expiry_date' => $stock->exp_date, 
                        'created_by' => Auth::id(),
                    ]);

                    $remainingQtyToReserve -= $qtyToDeduct;
                }
            }
            
            if ($remainingQtyToReserve > 0) {
                // Seharusnya tidak terjadi jika validasi stok server-side bekerja
                throw new \Exception("Gagal mengalokasikan stok penuh untuk material {$materialCode}. Sisa: {$remainingQtyToReserve}");
            }
        }
    }

    // ===================================================================
    // STORE METHOD (Updated)
    // ===================================================================

    public function store(Request $request)
    {
        // ** PERBAIKAN UTAMA: Menerapkan required_if untuk semua field header yang spesifik per kategori **
        $validated = $request->validate([
            'noReservasi' => 'required|string|unique:reservation_requests,no_reservasi',
            'tanggalPermintaan' => 'required|date',
            'request_type' => 'required|string',

            // FOH & RS fields - REQUIRED IF foh-rs
            'departemen' => 'nullable|required_if:request_type,foh-rs|string',
            'alasanReservasi' => 'nullable|required_if:request_type,foh-rs|string',

            // Packaging & ADD fields - REQUIRED IF packaging atau add
            'namaProduk' => 'nullable|required_if:request_type,packaging,add|string',
            'noBetsFilling' => 'nullable|required_if:request_type,packaging,add|string',
            
            // Raw Material fields - REQUIRED IF raw-material
            'kodeProduk' => 'nullable|required_if:request_type,raw-material|string',
            'noBets' => 'nullable|required_if:request_type,raw-material|string',
            'besarBets' => 'nullable|required_if:request_type,raw-material|numeric',

            // Item validation
            'items' => 'required|array|min:1',
            // Item codes must be present based on type
            'items.*.kodeItem' => 'nullable|required_if:request_type,foh-rs|string',
            'items.*.kodePM' => 'nullable|required_if:request_type,packaging,add|string',
            'items.*.kodeBahan' => 'nullable|required_if:request_type,raw-material|string',
            // Item quantities must be present and > 0 based on type
            'items.*.qty' => 'nullable|required_if:request_type,foh-rs|numeric',
            'items.*.jumlahPermintaan' => 'nullable|required_if:request_type,packaging,add|numeric',
            'items.*.jumlahKebutuhan' => 'nullable|required_if:request_type,raw-material|numeric',
            // Field lainnya yang sifatnya opsional/spesifik item
            'items.*.keterangan' => 'nullable|string',
            'items.*.uom' => 'nullable|string',
            'items.*.namaMaterial' => 'nullable|string',
            'items.*.namaBahan' => 'nullable|string',
            'items.*.jumlahKirim' => 'nullable|numeric',
            'items.*.alasanPenambahan' => 'nullable|string',
        ]);
        // END PERBAIKAN UTAMA

        // ** START: VALIDASI DUPLICATE NO BETS **
        // Cek apakah No Bets sudah ada di database untuk mencegah duplikasi
        $requestType = $validated['request_type'];
        
        if ($requestType === 'raw-material' && !empty($validated['noBets'])) {
            // Cek duplicate untuk Raw Material (no_bets)
            $existingBets = ReservationRequest::where('no_bets', $validated['noBets'])->first();
            
            if ($existingBets) {
                throw ValidationException::withMessages([
                    'noBets' => "❌ No Bets '{$validated['noBets']}' sudah digunakan pada reservasi {$existingBets->no_reservasi}. Silakan gunakan No Bets yang berbeda."
                ]);
            }
        }
        
        if (($requestType === 'packaging' || $requestType === 'add') && !empty($validated['noBetsFilling'])) {
            // Cek duplicate untuk Packaging/ADD (no_bets_filling)
            $existingBetsFilling = ReservationRequest::where('no_bets_filling', $validated['noBetsFilling'])->first();
            
            if ($existingBetsFilling) {
                throw ValidationException::withMessages([
                    'noBetsFilling' => "❌ No Bets Filling '{$validated['noBetsFilling']}' sudah digunakan pada reservasi {$existingBetsFilling->no_reservasi}. Silakan gunakan No Bets Filling yang berbeda."
                ]);
            }
        }
        // ** END: VALIDASI DUPLICATE NO BETS **

        // ** START: VALIDASI STOK SERVER-SIDE (Final Guard) **
        // Logic ini penting untuk mencegah double submission atau race condition
        $stockErrors = [];
        foreach ($validated['items'] as $index => $item) {
            $materialCodeKey = '';
            $qtyKey = '';
            $requestType = $validated['request_type'];

            if ($requestType === 'foh-rs') {
                $materialCodeKey = 'kodeItem';
                $qtyKey = 'qty';
            } elseif ($requestType === 'packaging' || $requestType === 'add') {
                $materialCodeKey = 'kodePM';
                $qtyKey = 'jumlahPermintaan';
            } elseif ($requestType === 'raw-material') {
                $materialCodeKey = 'kodeBahan';
                $qtyKey = 'jumlahKebutuhan';
            }

            $materialCode = $item[$materialCodeKey] ?? null;
            $requestedQty = (float) ($item[$qtyKey] ?? 0);

            if ($materialCode && $requestedQty > 0) {
                // 1. Cari Material (masih perlu untuk mendapatkan ID material)
                $material = Material::where('kode_item', $materialCode)->first();

                if (!$material) {
                    if($materialCode) {
                        $stockErrors["items.{$index}"] = "Material dengan kode {$materialCode} (item ke-".($index + 1).") tidak ditemukan.";
                    }
                    continue;
                }
                
                // 2. Hitung total stok tersedia dari InventoryStock (RELEASED/KARANTINA only)
                $totalAvailableStock = InventoryStock::where('material_id', $material->id)
                    ->whereIn('status', ['RELEASED', 'KARANTINA'])
                    ->where('status', '!=', 'REJECTED')
                    ->sum('qty_available');

                // 3. Bandingkan
                if ($requestedQty > $totalAvailableStock) {
                    $stockErrors["items.{$index}"] = "Permintaan untuk **{$materialCode}** ({$requestedQty}) melebihi stok yang tersedia ({$totalAvailableStock}).";
                }
            }
        }

        if (!empty($stockErrors)) {
            // Jika ada error stok, lempar ValidationException
            throw ValidationException::withMessages($stockErrors);
        }
        // ** END: VALIDASI STOK SERVER-SIDE **


        DB::beginTransaction();
        try {
            $reservationRequest = ReservationRequest::create([
                'no_reservasi' => $validated['noReservasi'],
                'request_type' => $validated['request_type'],
                'tanggal_permintaan' => $validated['tanggalPermintaan'],
                'status' => 'In Progress', 
                'departemen' => $validated['departemen'] ?? null,
                'alasan_reservasi' => $validated['alasanReservasi'] ?? null,
                'nama_produk' => $validated['namaProduk'] ?? null,
                'no_bets_filling' => $validated['noBetsFilling'] ?? null,
                'kode_produk' => $validated['kodeProduk'] ?? null,
                'no_bets' => $validated['noBets'] ?? null,
                'besar_bets' => $validated['besarBets'] ?? null,
                'requested_by' => Auth::id(),
            ]);

            // Mapping item keys kembali ke snake_case sebelum disimpan ke DB (sesuai skema Laravel)
            $mappedItems = collect($validated['items'])->map(function ($item) {
                $dbItem = [];
                foreach ($item as $key => $value) {
                    // Abaikan field tambahan dari frontend
                    if ($key === 'stokAvailable' || $key === 'satuan') continue;
                    
                    // Konversi camelCase ke snake_case secara manual untuk memastikan keakuratan
                    $snakeCaseKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
                    $dbItem[$snakeCaseKey] = $value;
                }
                return $dbItem;
            });

            // Simpan Item Request
            $reservationRequest->items()->createMany($mappedItems->toArray());
            
            // ** STOCK DEDUCTION / RESERVATION LOGIC **
            // PENTING: Panggil fungsi untuk mengurangi stok dan mencatat di tabel 'reservations'
            $this->allocateStock($reservationRequest, $validated['items'], $validated['request_type']);

            DB::commit();

            // FIX: Mengubah respons JSON menjadi redirect Inertia dengan flash message
            return redirect()
                ->route('transaction.reservation.index')
                ->with('flash', [
                    'type' => 'success',
                    'message' => '✅ Reservation request berhasil dibuat! Stok telah dialokasikan. No: ' . $reservationRequest->no_reservasi,
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log kesalahan
            report($e);
            
            // ** PERBAIKAN UTAMA: Tampilkan pesan exception yang sebenarnya **
            $errorMessage = '❌ Gagal membuat reservasi. Kesalahan: ' . $e->getMessage();

            return redirect()
                ->back()
                ->with('flash', [
                    'type' => 'error',
                    'message' => $errorMessage, 
                ])
                // ** PERBAIKAN PENTING: Masukkan pesan detail ke withErrors agar terbaca oleh Inertia/Vue error handling **
                ->withErrors(['submit' => $errorMessage]); 
        }
    }
}


