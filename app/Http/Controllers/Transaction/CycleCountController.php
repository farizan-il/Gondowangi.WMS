<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Models
use App\Models\InventoryStock;
use App\Models\CycleCount;
use App\Models\Material;
use App\Models\WarehouseBin;

class CycleCountController extends Controller
{
    /**
     * Menampilkan halaman Cycle Count dengan format tabel sesuai gambar.
     */
    public function index()
    {
        $cycleCounts = CycleCount::with(['material', 'bin'])
            ->where('status', '!=', 'APPROVED') // Hanya tampilkan yang belum selesai
            ->orderBy('count_date', 'desc')
            ->get();

        if ($cycleCounts->isEmpty()) {
            $stocks = InventoryStock::with(['material', 'bin'])
                ->where('qty_on_hand', '>', 0)
                ->limit(10) 
                ->get();

            $data = $stocks->map(function ($stock) {
                return [
                    'id' => null, // Belum tersimpan di DB cycle_counts
                    'material_id' => $stock->material_id,
                    'bin_id' => $stock->bin_id,

                    'serial_number' => $stock->batch_lot ?? ($stock->material->kode_item . '-' . uniqid()), 

                    'tanggal' => Carbon::now()->format('d/m/Y H:i'),
                    'code' => $stock->material->kode_item,
                    'product_name' => $stock->material->nama_material,
                    'onhand' => (float) $stock->qty_on_hand,
                    'uom' => $stock->uom,

                    'location' => $stock->bin ? $stock->bin->bin_code : '-', // Location sistem
                    
                    // Field Inputan User (Default Kosong)
                    'scan_serial' => '', 
                    'scan_bin' => '',
                    'physical_qty' => 0, // Default 0
                    'status' => 'DRAFT'
                ];
            });
        } else {
            // Jika sudah ada data tersimpan (User melanjutkan pekerjaan)
            $data = $cycleCounts->map(function ($cc) {
                return [
                    'id' => $cc->id,
                    'material_id' => $cc->material_id,
                    'bin_id' => $cc->warehouse_bin_id,
                    'serial_number' => $cc->cycle_number,
                    'tanggal' => $cc->count_date->format('d/m/Y H:i'),
                    'code' => $cc->material->kode_item,
                    'product_name' => $cc->material->nama_material,
                    'onhand' => (float) $cc->system_qty,
                    'uom' => $cc->material->satuan,
                    'location' => $cc->bin ? $cc->bin->bin_code : '-',
                    
                    // Field Inputan User (Isi dengan data yg sudah disimpan)
                    'scan_serial' => $cc->scanned_serial,
                    'scan_bin' => $cc->scanned_bin,
                    'physical_qty' => (float) $cc->physical_qty,
                    'status' => $cc->status
                ];
            });
        }

        return Inertia::render('CycleCount', [
            'initialStocks' => $data
        ]);
    }

    /**
     * Menyimpan hasil hitungan fisik (Opname).
     */
    public function store(Request $request)
    {
        $items = $request->input('items');
        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                // --- LOGIKA BARU ---
                // Cek apakah Pengerjaan sudah dilakukan?
                // Syarat dianggap "Dikerjakan": Ada Scan Serial DAN Ada Scan Bin DAN Qty tidak null
                $hasScannedSerial = !empty($item['scan_serial']);
                $hasScannedBin = !empty($item['scan_bin']);
                $hasInputQty = isset($item['physical_qty']) && $item['physical_qty'] !== null && $item['physical_qty'] !== '';

                $isWorkDone = $hasScannedSerial && $hasScannedBin && $hasInputQty;

                if (!$isWorkDone) {
                    // Jika belum selesai dikerjakan, status TETAP DRAFT
                    // Supaya SPV tidak bisa Approve
                    $status = 'DRAFT'; 
                } else {
                    // Jika sudah dikerjakan, baru kita cek Match/Tidak
                    // Pastikan perbandingan Bin juga Case Insensitive
                    $binMatch = strtoupper($item['scan_bin']) == strtoupper($item['location']);
                    $qtyMatch = (float)$item['physical_qty'] == (float)$item['onhand'];

                    $isMatch = $qtyMatch && $binMatch;
                    
                    $status = $isMatch ? 'APPROVED' : 'REVIEW_NEEDED';
                }

                CycleCount::updateOrCreate(
                    ['id' => $item['id'] ?? null],
                    [
                        'cycle_number' => $item['serial_number'],
                        'material_id' => $item['material_id'],
                        'warehouse_bin_id' => $item['bin_id'],
                        'system_qty' => $item['onhand'],
                        'physical_qty' => $item['physical_qty'],
                        'scanned_serial' => $item['scan_serial'],
                        'scanned_bin' => $item['scan_bin'],
                        'count_date' => Carbon::now(),
                        'status' => $status, // Status tersimpan sesuai logika baru
                        'spv_note' => $item['spv_note'] ?? null,
                    ]
                );
            }
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil disubmit. Hanya item yang sudah selesai dikerjakan yang diteruskan ke Supervisor.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Tambahkan method ini di dalam CycleCountController class
    public function approve(Request $request)
    {
        try {
            $cycleCount = CycleCount::where('id', $request->id)
                ->orWhere(function($q) use ($request) {
                    $q->where('material_id', $request->material_id)
                      ->whereDate('count_date', Carbon::today());
                })->first();

            if (!$cycleCount) {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }

            // --- VALIDASI KERAS DISINI ---
            // Jika status masih DRAFT (belum submit) atau sudah APPROVED, tolak!
            if ($cycleCount->status !== 'REVIEW_NEEDED') {
                return redirect()->back()->with('error', 'Item ini belum disubmit oleh Warehouseman atau sudah selesai.');
            }
            // -----------------------------

            $cycleCount->status = 'APPROVED';
            $cycleCount->spv_note = $request->spv_note;
            $cycleCount->save();

            return redirect()->back()->with('success', 'Data berhasil disetujui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal approve: ' . $e->getMessage());
        }
    }
}