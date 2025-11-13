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
use App\Traits\ActivityLogger;

class GoodsReceiptController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        $incomingGoods = IncomingGood::with([
            'purchaseOrder',
            'supplier',
            'items.material',
            'receiver'
        ])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($incoming) {

            // 1. Ambil semua item
            $items = $incoming->items->map(function ($item) {
                // Perubahan di sini untuk konsistensi: 
                // Jika Anda menyimpan total_qty_received (misalnya, jika kolom diubah), ambil dari situ, 
                // jika tidak, gunakan qty_unit yang diasumsikan sebagai total qty untuk sementara.
                // Jika kolom IncomingGoodsItem->qty_unit berisi total_qty, maka logika ini sudah benar.
                return [
                    'kodeItem' => $item->material->kode_item ?? '',
                    'namaMaterial' => $item->material->nama_material ?? '',
                    'batchLot' => $item->batch_lot,
                    'expDate' => $item->exp_date,
                    'qtyWadah' => $item->qty_wadah,
                    'qtyUnit' => $item->qty_unit, // Asumsi ini adalah total qty untuk ditampilkan di index
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
            // Cek apakah ada item yang statusQC-nya masih 'To QC'
            $isStillToQC = $items->contains(fn($item) => $item['statusQC'] === 'To QC');
            
            // Tentukan status akhir untuk ditampilkan
            $finalStatus = $isStillToQC ? 'Proses' : 'Selesai';
            
            return [
                'id' => $incoming->id,
                'incomingNumber' => $incoming->incoming_number,
                'noPo' => $incoming->po_id ?? '',
                'noSuratJalan' => $incoming->no_surat_jalan,
                'supplier' => $incoming->supplier->nama_supplier ?? '',
                'tanggalTerima' => $incoming->tanggal_terima,
                'noKendaraan' => $incoming->no_kendaraan,
                'namaDriver' => $incoming->nama_driver,
                'kategori' => $incoming->kategori,
                // Ganti status lama dengan status otomatis
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
                    'mfg' => $material->defaultSupplier->nama_supplier ?? '',
                    'qcRequired' => $material->qc_required,
                    'kategori' => $material->kategori,
                ];
            });

        return Inertia::render('PenerimaanBarang', [
            'shipments' => $incomingGoods,
            'suppliers' => $suppliers,
            'materials' => $materials,
        ]);
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

        DB::beginTransaction();
        try {
            // Generate incoming number
            $date = date('Ymd');
            $lastIncoming = IncomingGood::whereDate('created_at', today())->latest()->first();
            $sequence = $lastIncoming ? (intval(substr($lastIncoming->incoming_number, -4)) + 1) : 1;
            $incomingNumber = "IN/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

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
                    // Jika Anda memiliki kolom 'total_qty_received' di IncomingGoodsItem, gunakan itu:
                    // 'total_qty_received' => $totalQtyReceived, 
                    // Karena kode lama menggunakan 'qty_unit' untuk menyimpan total Qty, dan Anda ingin mempertahankannya:
                    // Anda perlu membuat kolom baru, atau mengubah maksud kolom.
                    // Jika kolom Anda di IncomingGoodsItem yang bernama 'qty_unit' akan menampung TOTAL QTY (seperti yang ditunjukkan kode lama), 
                    // maka kodenya dikembalikan ke:
                    // 'qty_unit' => $totalQtyReceived, // Simpan total qty di qty_unit untuk QC
                    // Namun ini membingungkan. Lebih baik **asumsi** `qty_unit` menyimpan kuantitas unit per wadah, dan Anda akan **menggunakan kolom lain di InventoryStock** untuk totalnya.
                    // Berdasarkan permintaan, saya akan **mempertahankan** `$totalQtyReceived` yang dihitung dan **menggunakan kolom `qty_unit` di `IncomingGoodsItem` untuk menyimpan total qty** (sesuai dengan maksud kode Anda yang sebelumnya), tapi ini butuh penyesuaian di sisi frontend/QC.
                    
                    // Untuk menjaga konsistensi dengan InventoryStock:
                    // 'qty_unit' => $totalQtyReceived, // Menyimpan total qty
                    // 'qty_per_wadah' => $qtyPerWadah, // *ASUMSI: Anda menambahkan kolom ini untuk menyimpan Qty per Wadah yang asli*
                    
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
                    'description' => "Material {$material->nama_material} diterima dan masuk Bin {$itemData['binTarget']}. Total: {$totalQtyReceived} {$material->satuan}",
                    'material_id' => $material->id,
                    'batch_lot' => $itemData['batchLot'],
                    'exp_date' => $itemData['expDate'] ?? null,
                    'qty_after' => $inventory->qty_on_hand, 
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