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
    public function index()
    {
        // Get all cycle counts grouped by material and bin
        // We only want to show the LATEST record per material-bin combination
        $cycleCounts = CycleCount::with(['material', 'bin'])
            ->orderBy('count_date', 'desc')
            ->get()
            ->groupBy(function($item) {
                return $item->material_id . '_' . $item->warehouse_bin_id;
            })
            ->map(function($group) {
                // Return only the latest (first) record from each group
                return $group->first();
            })
            ->values();

        if ($cycleCounts->isEmpty()) {
            $stocks = InventoryStock::with(['material', 'bin'])
                ->where('qty_on_hand', '>', 0)
                ->limit(10)
                ->get();

            $data = $stocks->map(function ($stock) {
                // Get history for this material (exclude current if exists)
                $history = CycleCount::where('material_id', $stock->material_id)
                    ->where('warehouse_bin_id', $stock->bin_id)
                    ->where('status', 'APPROVED')
                    ->orderBy('count_date', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function($h) {
                        return [
                            'count_date' => $h->count_date->format('d/m/Y H:i'),
                            'system_qty' => (float) $h->system_qty,
                            'physical_qty' => (float) $h->physical_qty,
                            'status' => $h->status,
                            'spv_note' => $h->spv_note,
                            'accuracy' => $h->system_qty > 0 ? round(($h->physical_qty / $h->system_qty) * 100, 2) : 0
                        ];
                    });

                return [
                    'id' => null,
                    'material_id' => $stock->material_id,
                    'bin_id' => $stock->bin_id,
                    'serial_number' => $stock->batch_lot ?? ($stock->material->kode_item . '-' . uniqid()), 
                    'tanggal' => Carbon::now()->format('d/m/Y H:i'),
                    'code' => $stock->material->kode_item,
                    'product_name' => $stock->material->nama_material,
                    'onhand' => (float) $stock->qty_on_hand,
                    'uom' => $stock->uom,
                    'location' => $stock->bin ? $stock->bin->bin_code : '-',
                    'scan_serial' => '', 
                    'scan_bin' => '',
                    'physical_qty' => 0,
                    'status' => 'DRAFT',
                    'history' => $history,
                    'history_count' => $history->count()
                ];
            });
        } else {
            $data = $cycleCounts->map(function ($cc) {
                // Get history for this material-bin combination
                // Exclude the current record and only get APPROVED records
                $history = CycleCount::where('material_id', $cc->material_id)
                    ->where('warehouse_bin_id', $cc->warehouse_bin_id)
                    ->where('status', 'APPROVED')
                    ->where('id', '!=', $cc->id) // Exclude current record
                    ->orderBy('count_date', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function($h) {
                        return [
                            'count_date' => $h->count_date->format('d/m/Y H:i'),
                            'system_qty' => (float) $h->system_qty,
                            'physical_qty' => (float) $h->physical_qty,
                            'status' => $h->status,
                            'spv_note' => $h->spv_note,
                            'accuracy' => $h->system_qty > 0 ? round(($h->physical_qty / $h->system_qty) * 100, 2) : 0
                        ];
                    });

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
                    'scan_serial' => $cc->scanned_serial,
                    'scan_bin' => $cc->scanned_bin,
                    'physical_qty' => (float) $cc->physical_qty,
                    'status' => $cc->status,
                    'history' => $history,
                    'history_count' => $history->count()
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

    /**
     * Get cycle count history for a specific material
     */
    public function getHistory($materialId)
    {
        $history = CycleCount::with(['material', 'bin'])
            ->where('material_id', $materialId)
            ->where('status', 'APPROVED')
            ->orderBy('count_date', 'desc')
            ->limit(20)
            ->get()
            ->map(function($h) {
                return [
                    'id' => $h->id,
                    'count_date' => $h->count_date->format('d/m/Y H:i'),
                    'system_qty' => (float) $h->system_qty,
                    'physical_qty' => (float) $h->physical_qty,
                    'status' => $h->status,
                    'spv_note' => $h->spv_note,
                    'location' => $h->bin ? $h->bin->bin_code : '-',
                    'accuracy' => $h->system_qty > 0 ? round(($h->physical_qty / $h->system_qty) * 100, 2) : 0,
                    'variance' => $h->system_qty > 0 ? round((($h->physical_qty - $h->system_qty) / $h->system_qty) * 100, 2) : 0
                ];
            });

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    /**
     * Repeat/create new cycle count for a material
     */
    public function repeat(Request $request)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'bin_id' => 'required|exists:warehouse_bins,id'
        ]);

        try {
            // Get current stock data
            $stock = InventoryStock::with(['material', 'bin'])
                ->where('material_id', $request->material_id)
                ->where('bin_id', $request->bin_id)
                ->first();

            if (!$stock) {
                return redirect()->back()->with('error', 'Stock tidak ditemukan.');
            }

            // Create new cycle count record
            $cycleCount = CycleCount::create([
                'cycle_number' => $stock->batch_lot ?? ($stock->material->kode_item . '-' . uniqid()),
                'material_id' => $stock->material_id,
                'warehouse_bin_id' => $stock->bin_id,
                'system_qty' => $stock->qty_on_hand,
                'physical_qty' => null,
                'scanned_serial' => null,
                'scanned_bin' => null,
                'count_date' => Carbon::now(),
                'status' => 'DRAFT',
                'spv_note' => 'Repeat cycle count by supervisor'
            ]);

            return redirect()->back()->with('success', 'Cycle count baru berhasil dibuat untuk material: ' . $stock->material->nama_material);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat cycle count baru: ' . $e->getMessage());
        }
    }
}