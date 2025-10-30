<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\Material; // Import model Material
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Traits\ActivityLogger;

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
            // PENTING: Map array items secara rekursif
            'items' => $req->items->map(fn($item) => $this->mapItemToCamelCase($item)),
        ];
    }
    
    public function index()
    {
        $reservations = ReservationRequest::with('items')
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
        $reservations = ReservationRequest::with('items')->get();
        
        // Gunakan helper untuk mapping data AJAX
        $mappedReservations = $reservations->map(fn($req) => $this->mapReservationToCamelCase($req));
        
        return response()->json($mappedReservations);
    }
    
    /**
     * Endpoint API untuk mencari material secara dinamis.
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

        $materials = Material::where('kategori', $materialCategory)
            ->where(function ($q) use ($query) {
                // Cari berdasarkan kode item atau nama material
                $q->where('kode_item', 'like', '%' . $query . '%')
                  ->orWhere('nama_material', 'like', '%' . $query . '%');
            })
            ->limit(10)
            ->get()
            ->map(function ($material) use ($type) {
                // Map hasil ke format yang dibutuhkan Vue
                $base = [
                    'id' => $material->id,
                    'kodeItem' => $material->kode_item,
                    'namaMaterial' => $material->nama_material,
                    'satuan' => $material->satuan,
                ];

                // Sesuaikan output keys berdasarkan tipe request
                if ($type === 'foh-rs') {
                    // Untuk FOH & RS, kita butuh kodeItem dan satuan
                    return [
                        ...$base,
                        'keterangan' => $material->deskripsi,
                        'uom' => $material->satuan,
                    ];
                } elseif ($type === 'packaging' || $type === 'add') {
                    // Untuk Packaging/ADD, kita butuh kodePM, namaMaterial
                    return [
                        ...$base,
                        'kodePM' => $material->kode_item, // Asumsi kode_item digunakan sebagai kodePM
                        'namaMaterial' => $material->nama_material,
                        'satuan' => $material->satuan,
                    ];
                } elseif ($type === 'raw-material') {
                    // Untuk Raw Material, kita butuh kodeBahan, namaBahan
                    return [
                        ...$base,
                        'kodeBahan' => $material->kode_item, // Asumsi kode_item digunakan sebagai kodeBahan
                        'namaBahan' => $material->nama_material,
                        'satuan' => $material->satuan,
                    ];
                }
                return $base;
            });

        return response()->json($materials);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'noReservasi' => 'required|string|unique:reservation_requests,no_reservasi',
            'tanggalPermintaan' => 'required|date',
            'departemen' => 'nullable|string',
            'alasanReservasi' => 'nullable|string',
            'namaProduk' => 'nullable|string',
            'noBetsFilling' => 'nullable|string',
            'kodeProduk' => 'nullable|string',
            'noBets' => 'nullable|string',
            'besarBets' => 'nullable|numeric',
            'items' => 'required|array',
            'request_type' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $reservationRequest = ReservationRequest::create([
                'no_reservasi' => $validated['noReservasi'],
                'request_type' => $validated['request_type'],
                'tanggal_permintaan' => $validated['tanggalPermintaan'],
                'status' => 'Submitted',
                'departemen' => $validated['departemen'],
                'alasan_reservasi' => $validated['alasanReservasi'],
                'nama_produk' => $validated['namaProduk'],
                'no_bets_filling' => $validated['noBetsFilling'],
                'kode_produk' => $validated['kodeProduk'],
                'no_bets' => $validated['noBets'],
                'besar_bets' => $validated['besarBets'],
                'requested_by' => Auth::id(),
            ]);

            // Mapping item keys kembali ke snake_case sebelum disimpan ke DB (sesuai skema Laravel)
            $mappedItems = collect($validated['items'])->map(function ($item) {
                $dbItem = [];
                foreach ($item as $key => $value) {
                    // Konversi camelCase ke snake_case (handle kasus PM, UOM, QTY, dll.)
                    $snakeCaseKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
                    
                    // Override untuk kasus khusus yang diketahui, seperti PM/UOM/QTY
                    if ($key === 'kodePM') $snakeCaseKey = 'kode_pm';
                    if ($key === 'qty') $snakeCaseKey = 'qty';
                    if ($key === 'uom') $snakeCaseKey = 'uom';
                    if ($key === 'kodeItem') $snakeCaseKey = 'kode_item';
                    if ($key === 'namaMaterial') $snakeCaseKey = 'nama_material';
                    if ($key === 'jumlahPermintaan') $snakeCaseKey = 'jumlah_permintaan';
                    if ($key === 'kodeBahan') $snakeCaseKey = 'kode_bahan';
                    if ($key === 'namaBahan') $snakeCaseKey = 'nama_bahan';
                    if ($key === 'jumlahKebutuhan') $snakeCaseKey = 'jumlah_kebutuhan';
                    if ($key === 'jumlahKirim') $snakeCaseKey = 'jumlah_kirim';
                    if ($key === 'alasanPenambahan') $snakeCaseKey = 'alasan_penambahan';


                    $dbItem[$snakeCaseKey] = $value;
                }
                // Tambahkan pengecekan jika kolom yang diperlukan tidak ada.
                return $dbItem;
            });

            $reservationRequest->items()->createMany($mappedItems->toArray());

            DB::commit();

            // FIX: Mengubah respons JSON menjadi redirect Inertia dengan flash message
            return redirect()
                ->route('transaction.reservation.index')
                ->with('flash', [
                    'type' => 'success',
                    'message' => '✅ Reservation request berhasil dibuat! No: ' . $reservationRequest->no_reservasi,
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Mengubah respons error menjadi redirect Inertia dengan flash message error
            return redirect()
                ->back()
                ->with('flash', [
                    'type' => 'error',
                    'message' => '❌ Gagal membuat reservasi. Debug: ' . $e->getMessage()
                ])
                ->withErrors(['submit' => 'Terjadi kesalahan saat menyimpan data.']); 
        }
    }

    private function generateReservationNumber()
    {
        $date = date('Ymd');
        $lastReservation = Reservation::whereDate('created_at', today())->latest()->first();
        $sequence = $lastReservation ? (intval(substr($lastReservation->reservation_number, -4)) + 1) : 1;
        return "RES/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
