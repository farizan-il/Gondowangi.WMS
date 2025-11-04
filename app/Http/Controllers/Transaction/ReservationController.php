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

class ReservationController extends Controller
{
    use ActivityLogger;

    // Helper function untuk mapping data item (detail) ke camelCase
    private function mapItemToCamelCase($item)
    {
        $itemArray = is_object($item) ? $item->toArray() : $item;
        
        // Pemetaan eksplisit untuk memastikan semua kunci item detail sesuai dengan front-end (camelCase)
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

    /**
     * Helper function untuk mapping detail batch reservasi.
     */
    private function mapBatchDetailToCamelCase($res)
    {
        $resArray = is_object($res) ? $res->toArray() : $res;
        
        // Asumsi relasi material dimuat pada model Reservation
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

    // Helper function untuk mapping data request (header) ke camelCase
    private function mapReservationToCamelCase($req)
    {
        return [
            'id' => $req->id,
            'noReservasi' => $req->no_reservasi,
            'type' => strtolower($req->request_type), // FIX UTAMA: paksa type menjadi huruf kecil
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
            // PENTING: Map array items (permintaan agregat)
            'items' => $req->items->map(fn($item) => $this->mapItemToCamelCase($item)),
            // BARU: Map detail batch reservasi
            'batchDetails' => $req->reservations->map(fn($res) => $this->mapBatchDetailToCamelCase($res)),
        ];
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

        // Tentukan kategori material yang dicari berdasarkan tipe request
        $materialCategory = '';
        if ($type === 'foh-rs') {
            $materialCategory = 'FOH & RS';
        } elseif ($type === 'packaging' || $type === 'add') {
            $materialCategory = 'Packaging Material';
        } elseif ($type === 'raw-material') {
            $materialCategory = 'Raw Material';
        } else {
            return response()->json([]);
        }

        // ** REVISI UTAMA: Mengambil data langsung dari InventoryStock **
        $materialsInStock = InventoryStock::join('materials', 'inventory_stock.material_id', '=', 'materials.id')
            ->select(
                'materials.id as materialId', 
                'materials.kode_item', 
                'materials.nama_material', 
                'materials.satuan', 
                'materials.deskripsi'
            )
            // Menjumlahkan qty_available dari semua baris stok material yang sama
            ->selectRaw('SUM(inventory_stock.qty_available) as total_available_stock')
            ->where('materials.kategori', $materialCategory)
            ->where(function ($q) use ($query) {
                // Mencari berdasarkan kode item atau nama material
                $q->where('materials.kode_item', 'like', '%' . $query . '%')
                  ->orWhere('materials.nama_material', 'like', '%' . $query . '%');
            })
            // Mengelompokkan berdasarkan material untuk mendapatkan total stok
            ->groupBy('materials.id', 'materials.kode_item', 'materials.nama_material', 'materials.satuan', 'materials.deskripsi')
            // Hanya menampilkan material yang memiliki stok tersedia > 0
            ->having('total_available_stock', '>', 0) 
            ->limit(10)
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
                    'keterangan' => $material->deskripsi,
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
    
    // ===================================================================
    // NEW FUNCTION: Stock Allocation/Reservation Logic
    // ===================================================================

    /**
     * Mengalokasikan stok dari InventoryStock dan membuat entri Reservation.
     * Logika ini menggunakan strategi LIFO-Stock (Least Inventory First Out).
     *
     * @param ReservationRequest $request
     * @param array $validatedItems
     * @param string $requestType
     * @throws \Exception
     */
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
            
            // 2. Dapatkan stok yang tersedia (LIFO-Stock: diurutkan berdasarkan kuantitas tersedia terkecil)
            $availableStocks = InventoryStock::where('material_id', $material->id)
                ->where('qty_available', '>', 0)
                ->orderBy('qty_available', 'asc') // Stok paling sedikit diambil dulu
                ->get();
            
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

    public function create()
    {
        //
    }
    public function show(string $id)
    {
        //
    }
    public function edit(string $id)
    {
        //
    }
    public function update(Request $request, string $id)
    {
        //
    }
    public function destroy(string $id)
    {
        //
    }
}
