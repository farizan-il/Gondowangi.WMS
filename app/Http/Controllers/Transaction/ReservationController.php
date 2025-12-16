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
            'qty' => $itemArray['qty'] ?? null,
            'uom' => $itemArray['uom'] ?? null,
            // Packaging / ADD
            'namaMaterial' => $itemArray['nama_material'] ?? null,
            'kodePM' => $itemArray['kode_pm'] ?? null,
            'jumlahPermintaan' => $itemArray['jumlah_permintaan'] ?? null,
            'alasanPenambahan' => $itemArray['alasan_penambahan'] ?? null,
            // Raw Material
            'kodeBahan' => $itemArray['kode_bahan'] ?? null,
            'namaBahan' => $itemArray['nama_bahan'] ?? null,
            'jumlahKebutuhan' => $itemArray['jumlah_kebutuhan'] ?? null,
            'jumlahKirim' => $itemArray['jumlah_kirim'] ?? null,
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
        // --- Langkah 1: Isolasi Bagian Bill of Material ---
        $startKeyword = "Products to Consume"; 
        $startPos = strpos($pdfText, $startKeyword);
        
        if ($startPos === false) {
            return []; // Jika header tidak ditemukan, kembalikan array kosong
        }

        // Ambil semua teks setelah keyword Bill of Material
        $BoMSection = substr($pdfText, $startPos);
        
        $allMaterialsFromPdf = [];
        
        // 🔥 PERBAIKAN REGEX UTAMA: Menggunakan preg_match_all untuk mencari pola di seluruh blok teks.
        // Pola: [KODE] [NAMA (non-greedy)] [KUANTITAS] [UOM]
        // 🔥 PERBAIKAN REGEX FIXED: 
        // 1. Code: \d{4,} (Minimal 4 digit)
        // 2. Qty: [\d.,]+ 
        // 3. UoM: [a-zA-Z]+? (Non-greedy) diikuti oleh lookahead (?=RM|Ready|Production|\s|$)
        //    Ini mencegah capture 'PcsRM' menjadi satu kata UoM.
        $globalPattern = '/(\d{4,})\s+(.+?)\s+([\d.,]+)\s*([a-zA-Z]+?)(?=RM|Ready|Production|\s|$)/is'; 

        // Mencoba mencocokkan semua pola sekaligus
        if (preg_match_all($globalPattern, $BoMSection, $matches, PREG_SET_ORDER)) {
            
            foreach ($matches as $m) {
                // $m[1] = Kode, $m[2] = Nama, $m[3] = Qty, $m[4] = UoM
                $code = trim($m[1]);
                $name = trim($m[2]);
                // Sanitasi Quantity (Format Indonesia: 1.000,00 -> 1000.00)
                // 1. Hapus titik (ribuan)
                $qtyClean = str_replace('.', '', $m[3]);
                // 2. Ganti koma dengan titik (desimal)
                $qtyClean = str_replace(',', '.', $qtyClean);
                $qty = (float) $qtyClean; 
                $qty = (float) $qtyClean; 
                
                // Logging untuk debug
                // \Log::info("Parsed Item Raw: Code={$code}, Name={$name}, Qty={$m[3]}, UoM_Raw={$m[4]}");

                // Sanitasi UoM: REGEX AGRESIF
                // Hapus apapun yang dimulai dengan RM, Ready, atau Production (case insensitive) sampai akhir string
                $uomRaw = trim($m[4]);
                $uomClean = preg_replace('/(RM|Ready|Production).*/i', '', $uomRaw);
                $uom = trim($uomClean);

                $allMaterialsFromPdf[] = [
                    'code' => $code,
                    'name' => $name, 
                    'qty' => $qty,
                    'uom' => $uom
                ];
            }
        }
        
        // --- Langkah 2: Agregasi Kuantitas (Tetap sama) ---
        // Agregasi material yang memiliki kode dan UOM yang sama (jika muncul lebih dari sekali di PO)
        $aggregatedMaterials = [];
        foreach ($allMaterialsFromPdf as $material) {
            $key = $material['code'] . '_' . $material['uom']; 
            
            if (!isset($aggregatedMaterials[$key])) {
                $aggregatedMaterials[$key] = $material;
            } else {
                $aggregatedMaterials[$key]['qty'] += $material['qty'];
            }
        }

        return array_values($aggregatedMaterials);
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

        // Ambil stok dari inventory stock
        $totalAvailableStock = InventoryStock::where('material_id', $material->id)->sum('qty_available');

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
    
    // ===================================================================
    // ENDPOINT: parseMaterials
    // ===================================================================

    public function parseMaterials(Request $request)
    {
        // 1. VALIDASI DATA FORM (Hanya PDF dan kategori harus terisi)
        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf|max:10240', 
                'request_type' => 'required|string|in:raw-material,packaging,foh-rs,add', 
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => '❌ Gagal Validasi: Periksa file atau tipe request.', 'errors' => $e->errors()], 422);
        }

        $file = $request->file('file');
        $requestType = $request->input('request_type');
        $allParsedMaterials = [];
        
        // Hapus: $pathToPdftotext (Karena tidak lagi menggunakan Spatie)

        try {
            // --- 2. FASE EKSEKUSI PURE PHP PARSER (SMALOT) ---
            if ($file->extension() === 'pdf') {
                
                // 🔥 KODE SMALOT UNTUK MENGGANTIKAN SPATIE/pdftotext
                $parser = new Parser();
                $pdf = $parser->parseFile($file->getPathname());
                $pdfText = $pdf->getText(); 
                // ---------------------------------------------------
                
                // PARSING LOGIC
                $allParsedMaterials = $this->parseProductionOrderContent($pdfText);
                
                if (empty($allParsedMaterials)) {
                     throw new \Exception("Tidak ada material yang berhasil diekstrak. Cek Regex atau format PDF."); 
                }
            } else {
                throw new \Exception("Tipe file selain PDF saat ini belum didukung untuk parsing otomatis.");
            }
            
            // --- 3. FASE FILTERING DAN DB LOOKUP ---
            $resultMaterials = [];
            $notFoundMaterials = [];
            
            // Tentukan kategori WMS berdasarkan request_type
            $materialCategoryWMS = match ($requestType) {
                'raw-material' => 'Raw Material',
                'foh-rs' => 'FOH & RS',
                'add', 'packaging' => 'Packaging Material',
                default => 'Packaging Material',
            };

            foreach ($allParsedMaterials as $parsedItem) {
                // ... (Logika filtering, DB lookup, dan penentuan $resultMaterials / $notFoundMaterials) ...
                
                $materialCode = $parsedItem['code'] ?? null;
                $materialName = $parsedItem['name'] ?? null;
                $requestedQty = (float) ($parsedItem['qty'] ?? 0);
                $uom = strtolower($parsedItem['uom'] ?? '');
                
                if (!$materialCode || $requestedQty <= 0) continue; 

                // Filter logika filtering berdasarkan UoM (kg vs non-kg)
                // REMOVED: Kita hapus filter strict UoM ini agar semua item masuk.
                /*
                $isRawMaterialUom = ($uom === 'kg');
                
                if ($requestType === 'raw-material' && !$isRawMaterialUom) {
                    continue; // Skip jika raw material tapi bukan kg (misal ada packaging nyasar)
                }
                
                if ($requestType === 'packaging' && $isRawMaterialUom) {
                    continue; 
                }
                */

                $materialData = $this->getMaterialAndStock($materialCode, $materialCategoryWMS, $uom);
                
                if ($materialData) {
                    // KODE DITEMUKAN DI MASTER

                    $item = $materialData;
                    
                    // Mapping jumlah permintaan ke field yang sesuai
                    if ($requestType === 'raw-material') {
                         $item['jumlahKebutuhan'] = $requestedQty;
                         $item['jumlahKirim'] = null;
                    } elseif ($requestType === 'packaging' || $requestType === 'add') {
                         $item['jumlahPermintaan'] = $requestedQty;
                    } elseif ($requestType === 'foh-rs') {
                         $item['qty'] = $requestedQty;
                         // foh-rs butuh 'keterangan' dan 'uom'
                         $item['keterangan'] = $materialData['namaMaterial']; // Default keterangan = nama material
                         $item['uom'] = $materialData['satuan'];
                         $item['kodeItem'] = $materialData['kodeBahan']; // map dari kodeBahan/kodePM
                    }
                    
                    // Cek ketersediaan stok
                    if ($materialData['stokAvailable'] < $requestedQty) {
                        $notFoundMaterials[] = [
                            'kode' => $materialCode,
                            'nama' => $materialData['namaBahan'] ?? $materialData['namaMaterial'],
                            'satuan' => $item['satuan'] ?? $uom,
                            'message' => "Stok tersedia hanya " . $materialData['stokAvailable'] . " " . ($item['satuan'] ?? $uom) . ". (Diperlukan: {$requestedQty})",
                        ];
                        // Karena stok kurang, kita tidak akan memasukkannya ke $resultMaterials.
                        continue;
                    }
                    
                    $resultMaterials[] = $item; 
                    
                } else {
                    $notFoundMaterials[] = [
                        'kode' => $materialCode,
                        'nama' => $materialName,
                        'satuan' => $uom,
                        'message' => 'Tidak ditemukan di master inventory WMS (Kategori: ' . $materialCategoryWMS . ').'
                    ];
                    
                    $item = ['satuan' => $uom, 'stokAvailable' => 0];
                    
                    if ($requestType === 'raw-material') {
                        $item['kodeBahan'] = $materialCode;
                        $item['namaBahan'] = $materialName; 
                        $item['jumlahKebutuhan'] = $requestedQty;
                    } elseif ($requestType === 'packaging' || $requestType === 'add') {
                        $item['kodePM'] = $materialCode;
                        $item['namaMaterial'] = $materialName;
                        $item['jumlahPermintaan'] = $requestedQty;
                    } elseif ($requestType === 'foh-rs') {
                        $item['kodeItem'] = $materialCode;
                        $item['keterangan'] = $materialName;
                        $item['qty'] = $requestedQty;
                        $item['uom'] = $uom;
                    }
                    $resultMaterials[] = $item;
                }
            }

            // 4. RESPON SUKSES
            return response()->json([
                'materials' => $resultMaterials,
                'notFoundMaterials' => $notFoundMaterials,
                'message' => '✅ File berhasil diproses.'
            ]);

        } 
        catch (\Exception $e) {
            // TANGKAP SEMUA ERROR RUNTIME LAINNYA
            Log::error('RESERVATION PARSE FATAL ERROR:', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'request_type' => $requestType,
            ]);
            
            // Kirim pesan error yang tertangkap di log ke frontend
            return response()->json([
                'message' => '❌ Gagal Eksekusi/Parsing: ' . $e->getMessage() 
            ], 422); 
        }
    }
    
    public function index()
    {
        // Memuat 'reservations' (detail batch) beserta materialnya
        $reservations = ReservationRequest::with(['items', 'reservations.material'])
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
        $reservations = ReservationRequest::with(['items', 'reservations.material'])->get();
        
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
            $categoryFilter = 'Packaging Material';
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
            // Apply Status Filter: Hanya ambil yang RELEASED
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
            
            // 2. Dapatkan stok yang tersedia
            $availableStocksQuery = InventoryStock::where('material_id', $material->id)
                ->where('qty_available', '>', 0);
            
            // ** START: LOGIKA PENGURUTAN FEFO/FIFO BARU (DI DATABASE) **
            
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

            // 2c. Ambil data yang SUDAH TERURUT SEMPURNA dari DB
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
            'besarBets' => 'nullable|required_if:request_type,raw-material|numeric|min:0.01',

            // Item validation
            'items' => 'required|array|min:1',
            // Item codes must be present based on type
            'items.*.kodeItem' => 'nullable|required_if:request_type,foh-rs|string',
            'items.*.kodePM' => 'nullable|required_if:request_type,packaging,add|string',
            'items.*.kodeBahan' => 'nullable|required_if:request_type,raw-material|string',
            // Item quantities must be present and > 0 based on type
            'items.*.qty' => 'nullable|required_if:request_type,foh-rs|numeric|min:0.01',
            'items.*.jumlahPermintaan' => 'nullable|required_if:request_type,packaging,add|numeric|min:0.01',
            'items.*.jumlahKebutuhan' => 'nullable|required_if:request_type,raw-material|numeric|min:0.01',
            // Field lainnya yang sifatnya opsional/spesifik item
            'items.*.keterangan' => 'nullable|string',
            'items.*.uom' => 'nullable|string',
            'items.*.namaMaterial' => 'nullable|string',
            'items.*.namaBahan' => 'nullable|string',
            'items.*.jumlahKirim' => 'nullable|numeric',
            'items.*.alasanPenambahan' => 'nullable|string',
        ]);
        // END PERBAIKAN UTAMA

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
                
                // 2. Hitung total stok tersedia dari InventoryStock
                $totalAvailableStock = InventoryStock::where('material_id', $material->id)
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
