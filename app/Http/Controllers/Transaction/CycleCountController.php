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
use App\Models\MaterialReqc;
use App\Models\StockMovement;

class CycleCountController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            // 1. Cari Material IDs
            $materialIds = Material::where('kode_item', 'like', "%{$search}%")
                ->orWhere('nama_material', 'like', "%{$search}%")
                ->pluck('id');

            // 2. Cari Stocks (Existing Logic)
            $stockMatches = InventoryStock::with(['material', 'bin'])
                ->where(function($q) use ($materialIds, $search) {
                    $q->whereIn('material_id', $materialIds)
                      ->orWhere('batch_lot', 'like', "%{$search}%");
                })
                ->where('qty_on_hand', '>', 0)
                ->whereNotIn('status', ['REJECTED', 'REJECT']) // Exclude REJECTED
                ->limit(20)
                ->get();

            // 3. Cari CycleCounts (Active/History) matching Search
            $ccMatches = CycleCount::with(['material', 'bin'])
                ->where(function($q) use ($materialIds, $search) {
                    $q->whereIn('material_id', $materialIds)
                      ->orWhere('cycle_number', 'like', "%{$search}%")
                      ->orWhere('scanned_serial', 'like', "%{$search}%");
                })
                ->orderBy('count_date', 'desc')
                ->limit(20)
                ->get();

            // 4. Merge Unique Keys (MaterialID_BinID)
            $keys = collect();
            foreach($stockMatches as $s) {
                $keys->push($s->material_id . '_' . $s->bin_id);
            }
            foreach($ccMatches as $c) {
                $keys->push($c->material_id . '_' . $c->warehouse_bin_id);
            }
            $uniqueKeys = $keys->unique();

            // 5. Build Data
            $data = $uniqueKeys->map(function($key) use ($stockMatches, $ccMatches) {
                [$matId, $binId] = explode('_', $key);
                
                // A. Check for Active Cycle Count (DRAFT/REVIEW_NEEDED)
                $activeCC = CycleCount::with(['material', 'bin'])
                    ->where('material_id', $matId)
                    ->where('warehouse_bin_id', $binId)
                    ->whereIn('status', ['DRAFT', 'REVIEW_NEEDED'])
                    ->latest('count_date')
                    ->first();

                if ($activeCC) {
                    return $this->mapCycleCountToRow($activeCC);
                }

                // B. Check for Stock
                // Try to find in matches first
                $stock = $stockMatches->first(function($s) use ($matId, $binId) {
                    return $s->material_id == $matId && $s->bin_id == $binId;
                });
                
                // If not in matches, fetch fresh (if we found this via CC search)
                if (!$stock) {
                    $stock = InventoryStock::with(['material', 'bin'])
                        ->where('material_id', $matId)
                        ->where('bin_id', $binId)
                        ->whereNotIn('status', ['REJECTED', 'REJECT']) // Exclude REJECTED
                        ->first();
                }

                if ($stock) {
                    return $this->mapStockToRow($stock);
                }

                // C. If no Stock (e.g. 0 qty) but we found a CC match (History)
                // Show the latest CC
                $latestCC = $ccMatches->first(function($c) use ($matId, $binId) {
                    return $c->material_id == $matId && $c->warehouse_bin_id == $binId;
                });
                
                if ($latestCC) {
                     return $this->mapCycleCountToRow($latestCC);
                }

                return null;
            })->filter()->values();

            return Inertia::render('CycleCount', [
                'initialStocks' => $data,
                'filters' => $request->only(['search'])
            ]);
        }

        // DEFAULT VIEW (Tanpa Search)
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
                ->whereNotIn('status', ['REJECTED', 'REJECT']) // Exclude REJECTED
                ->limit(10)
                ->get();

            $data = $stocks->map(function ($stock) {
                return $this->mapStockToRow($stock);
            });
        } else {
            $data = $cycleCounts->map(function ($cc) {
                return $this->mapCycleCountToRow($cc);
            });
        }

        return Inertia::render('CycleCount', [
            'initialStocks' => $data,
            'filters' => $request->only(['search'])
        ]);
    }

    private function mapStockToRow($stock)
    {
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
            'inventory_status' => $stock->status, // Add inventory status
            'history' => $history,
            'history_count' => $history->count(),
            'exp_date' => $stock->exp_date ? $stock->exp_date->format('Y-m-d') : null,
            'is_expired' => $stock->isExpired(),
            'inventory_stock_id' => $stock->id,
        ];
    }

    private function mapCycleCountToRow($cc)
    {
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

        // Try to get current stock status
        $currentStock = InventoryStock::where('material_id', $cc->material_id)
            ->where('bin_id', $cc->warehouse_bin_id)
            ->first();

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
            'inventory_status' => $currentStock ? $currentStock->status : null, // Add inventory status
            'history' => $history,
            'history_count' => $history->count(),
            'exp_date' => null, 
            'is_expired' => false,
            'inventory_stock_id' => null,
        ];
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