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
use App\Traits\ActivityLogger;

class CycleCountController extends Controller
{
    use ActivityLogger;
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $frequency = $request->input('frequency');
        $category = $request->input('category');

        // --- 1. QUERY STOCK ---
        $query = InventoryStock::with(['material', 'bin'])
            ->where('qty_on_hand', '>', 0)
            ->whereNotIn('status', ['REJECTED', 'REJECT']);

        // Filter Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('material', function($m) use ($search) {
                    $m->where('kode_item', 'like', "%{$search}%")
                      ->orWhere('nama_material', 'like', "%{$search}%");
                })->orWhere('batch_lot', 'like', "%{$search}%");
            });
        }

        // Filter Frequency
        if ($frequency) {
            if ($frequency === 'never') $query->doesntHave('cycleCounts');
            elseif ($frequency === 'rare') $query->has('cycleCounts', '<', 2);
            elseif ($frequency === 'often') $query->has('cycleCounts', '>', 5);
        }

        // Filter Category
        if ($category) {
            $query->whereHas('material', function($q) use ($category) {
                $q->where('kategori', $category);
            });
        }

        // --- 2. EKSEKUSI PAGINATION (GANTI LIMIT DENGAN PAGINATE) ---
        // Kita gunakan paginate(100). Ini akan otomatis mendeteksi ?page=1, ?page=2 dst.
        $stocks = $query->withCount(['cycleCounts' => function($q) {
             $q->where('status', 'APPROVED'); 
        }])
        ->orderBy('material_id') // Penting! Tambahkan order agar urutan konsisten antar halaman
        ->paginate(100); 

        // --- 3. SIAPKAN CYCLE COUNT AKTIF (Hanya untuk item di halaman ini agar ringan) ---
        // Kita ambil list Material ID & Bin ID dari stocks halaman ini saja
        $stockKeys = $stocks->map(function($s) {
            return $s->material_id . '-' . $s->bin_id;
        });

        // Ambil Active Cycle Count yang RELEVAN saja
        $activeCycleCounts = CycleCount::with(['material', 'bin'])
            ->where(function($q) {
                 $q->whereIn('status', ['DRAFT', 'REVIEW_NEEDED', 'APPROVED'])
                   ->orWhere(function($sub) {
                       $sub->where('status', 'APPROVED')
                           ->whereDate('updated_at', Carbon::today());
                   });
            })
            // Filter tambahan: Hanya ambil yang material & bin-nya ada di stock halaman ini
            // (Opsional, tapi bagus untuk performa jika data ribuan)
            ->get()
            ->keyBy(function($item) {
                return $item->material_id . '_' . $item->warehouse_bin_id;
            });

        // --- 4. MAPPING DATA ---
        // Kita mapping items yang ada di dalam paginator ($stocks->items())
        $mappedItems = collect($stocks->items())->map(function ($stock) use ($activeCycleCounts, $status) {
            $key = $stock->material_id . '_' . $stock->bin_id;
            
            $mappedItem = null;
            if ($activeCycleCounts->has($key)) {
                $mappedItem = $this->mapCycleCountToRow($activeCycleCounts[$key]);
            } else {
                $mappedItem = $this->mapStockToRow($stock);
            }
            $mappedItem['total_counted_times'] = $stock->cycle_counts_count ?? 0;
            return $mappedItem;
        });

        // Filter Status Post-Mapping
        if ($status) {
            $mappedItems = $mappedItems->filter(function($item) use ($status) {
                return $item['status'] === $status;
            })->values();
        }

        // --- 5. RETURN RESPONSE ---
        
        // JIKA REQUEST ADALAH "LOAD MORE" (Ajax/JSON)
        if ($request->wantsJson()) {
            return response()->json([
                'data' => $mappedItems,
                'next_page_url' => $stocks->nextPageUrl()
            ]);
        }

        // JIKA HALAMAN AWAL (Inertia)
        $stats = $this->getStatistics($category);

        return Inertia::render('CycleCount', [
            'initialStocks' => $mappedItems, // Kirim data yang sudah dimapping
            'nextPageUrl' => $stocks->nextPageUrl(), // Kirim URL halaman selanjutnya
            'filters' => $request->only(['search', 'status', 'frequency', 'category']),
            'statistics' => $stats
        ]);
    }

    /**
     * Private Helper: Menghitung Statistik Dashboard
     */
    private function getStatistics($category = null) 
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. Total SKU Aktif (Stock > 0)
        $totalSku = InventoryStock::where('qty_on_hand', '>', 0)
            ->whereNotIn('status', ['REJECTED', 'REJECT'])
            ->when($category, function($q) use ($category) {
                $q->whereHas('material', function($m) use ($category) {
                    $m->where('kategori', $category);
                });
            })
            ->count();

        // 2. Item Sudah Opname Bulan Ini (Unique Material)
        $countedThisMonth = CycleCount::where('status', 'APPROVED')
            ->whereMonth('count_date', $currentMonth)
            ->whereYear('count_date', $currentYear)
            ->when($category, function($q) use ($category) {
                $q->whereHas('material', function($m) use ($category) {
                    $m->where('kategori', $category);
                });
            })
            ->distinct('material_id')
            ->count('material_id');

        // 3. Item Belum Pernah Opname Sama Sekali
        $neverCounted = InventoryStock::where('qty_on_hand', '>', 0)
            ->whereNotIn('status', ['REJECTED', 'REJECT'])
            ->when($category, function($q) use ($category) {
                $q->whereHas('material', function($m) use ($category) {
                    $m->where('kategori', $category);
                });
            })
            ->doesntHave('cycleCounts')
            ->count();

        // 4. Rata-rata Akurasi (Bulan Ini)
        $avgAccuracy = CycleCount::where('status', 'APPROVED')
            ->whereMonth('count_date', $currentMonth)
            ->where('system_qty', '>', 0)
            ->when($category, function($q) use ($category) {
                $q->whereHas('material', function($m) use ($category) {
                    $m->where('kategori', $category);
                });
            })
            ->selectRaw('AVG((physical_qty / system_qty) * 100) as avg_acc')
            ->value('avg_acc');

        return [
            'total_sku' => $totalSku,
            'counted_items' => $countedThisMonth,
            'progress_percentage' => $totalSku > 0 ? round(($countedThisMonth / $totalSku) * 100, 1) : 0,
            'never_counted' => $neverCounted,
            'avg_accuracy' => round($avgAccuracy ?? 0, 2)
        ];
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
            'category' => $stock->material->kategori,
            'onhand' => max(0, (float) ($stock->qty_on_hand - $stock->qty_reserved)),
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

        // Try to get current stock status - IMPORTANT: Use live data from InventoryStock
        $currentStock = InventoryStock::where('material_id', $cc->material_id)
            ->where('bin_id', $cc->warehouse_bin_id)
            ->first();

        // Use live inventory data if available, otherwise fallback to cycle count snapshot
        $onhandQty = $currentStock 
            ? max(0, (float) ($currentStock->qty_on_hand - $currentStock->qty_reserved))
            : (float) $cc->system_qty;

        return [
            'id' => $cc->id,
            'material_id' => $cc->material_id,
            'bin_id' => $cc->warehouse_bin_id,
            'serial_number' => $cc->cycle_number,
            'tanggal' => $cc->count_date->format('d/m/Y H:i'),
            'code' => $cc->material->kode_item,
            'product_name' => $cc->material->nama_material,
            'category' => $cc->material->kategori,
            'onhand' => $onhandQty, // Now uses live data from InventoryStock
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
            'inventory_stock_id' => $currentStock ? $currentStock->id : null,
        ];
    }

    public function bulkRepeat(Request $request)
    {
        $request->validate([
            'targets' => 'required|array',
            'targets.*.material_id' => 'required|exists:materials,id',
            'targets.*.bin_id' => 'required|exists:warehouse_bins,id'
        ]);

        DB::beginTransaction();
        try {
            $countCreated = 0;

            foreach ($request->targets as $target) {
                // 1. Ambil info Stock saat ini untuk material & bin tersebut
                $stock = InventoryStock::with(['material'])
                    ->where('material_id', $target['material_id'])
                    ->where('bin_id', $target['bin_id'])
                    ->first();

                // Skip jika stock entah kenapa tidak ada (edge case)
                if (!$stock) continue;

                // 2. Logika Pembuatan
                // Kita akan membuat CycleCount baru berstatus DRAFT.
                // Jika sudah ada yang DRAFT (aktif), kita akan RESET saja datanya.
                
                $activeCC = CycleCount::where('material_id', $target['material_id'])
                    ->where('warehouse_bin_id', $target['bin_id'])
                    ->whereIn('status', ['DRAFT', 'REVIEW_NEEDED'])
                    ->first();

                if ($activeCC) {
                    // RESET yang aktif
                    $activeCC->update([
                        'system_qty' => max(0, $stock->qty_on_hand - $stock->qty_reserved),
                        'physical_qty' => null,
                        'scanned_serial' => null,
                        'scanned_bin' => null,
                        'status' => 'DRAFT',
                        'count_date' => Carbon::now(),
                        'spv_note' => 'Bulk Repeat by SPV (Reset)',
                    ]);
                } else {
                    // BUAT BARU (Karena yang lama sudah Approved/Done)
                    CycleCount::create([
                        'cycle_number' => $stock->batch_lot ?? ($stock->material->kode_item . '-' . uniqid()),
                        'material_id' => $stock->material_id,
                        'warehouse_bin_id' => $stock->bin_id,
                        'system_qty' => max(0, $stock->qty_on_hand - $stock->qty_reserved),
                        'physical_qty' => null,
                        'scanned_serial' => null,
                        'scanned_bin' => null,
                        'count_date' => Carbon::now(),
                        'status' => 'DRAFT',
                        'spv_note' => 'Bulk Repeat by SPV'
                    ]);
                }
                
                $countCreated++;
            }

            DB::commit();
            return redirect()->back()->with('success', "Berhasil membuat/reset {$countCreated} cycle count.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses bulk repeat: ' . $e->getMessage());
        }
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
                    // Jika sudah dikerjakan (scan serial, bin, dan input qty),
                    // Status selalu REVIEW_NEEDED agar SPV harus approve
                    // (Tidak ada auto-approve lagi)
                    $status = 'REVIEW_NEEDED';
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
        DB::beginTransaction();
        try {
            $cycleCount = CycleCount::with(['material', 'bin'])
                ->where('id', $request->id)
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

            // 1. Update Cycle Count Status
            $cycleCount->status = 'APPROVED';
            $cycleCount->spv_note = $request->spv_note;
            $cycleCount->save();

            // 2. UPDATE INVENTORY STOCK (Logic Baru!)
            $inventoryStock = InventoryStock::where('material_id', $cycleCount->material_id)
                ->where('bin_id', $cycleCount->warehouse_bin_id)
                ->where('batch_lot', $cycleCount->cycle_number)
                ->first();

            if ($inventoryStock) {
                $oldQty = $inventoryStock->qty_on_hand;
                $newQty = $cycleCount->physical_qty;
                $difference = $newQty - $oldQty;

                // Update inventory quantities
                $inventoryStock->qty_on_hand = $newQty;
                
                // Update qty_available: add the difference
                // If difference is positive (added), increase available
                // If negative (removed), decrease available
                $inventoryStock->qty_available = max(0, $inventoryStock->qty_available + $difference);
                $inventoryStock->last_movement_date = now();
                $inventoryStock->save();

                // 3. CREATE STOCK MOVEMENT RECORD (Adjustment)
                if ($difference != 0) {
                    StockMovement::create([
                        'movement_number' => 'ADJ-CC-' . date('YmdHis') . '-' . $cycleCount->id,
                        'movement_type' => 'Adjustment',
                        'material_id' => $cycleCount->material_id,
                        'batch_lot' => $cycleCount->cycle_number,
                        'qty' => abs($difference), // Field qty yang required
                        'qty_before' => $oldQty,
                        'qty_movement' => abs($difference),
                        'qty_after' => $newQty,
                        'movement_direction' => $difference >= 0 ? 'IN' : 'OUT',
                        'uom' => $inventoryStock->uom,
                        'bin_from' => $difference < 0 ? $cycleCount->warehouse_bin_id : null,
                        'bin_to' => $difference >= 0 ? $cycleCount->warehouse_bin_id : null,
                        'reference_document' => $cycleCount->cycle_number,
                        'notes' => "Cycle Count Adjustment. System: {$oldQty}, Physical: {$newQty}, Diff: {$difference}. SPV Note: " . ($request->spv_note ?? '-'),
                        'movement_date' => now(),
                    ]);
                }

                \Log::info('Cycle Count Approved and Inventory Adjusted', [
                    'cycle_count_id' => $cycleCount->id,
                    'material_id' => $cycleCount->material_id,
                    'bin_id' => $cycleCount->warehouse_bin_id,
                    'old_qty' => $oldQty,
                    'new_qty' => $newQty,
                    'difference' => $difference
                ]);
            } else {
                \Log::warning('Inventory Stock not found for approved Cycle Count', [
                    'cycle_count_id' => $cycleCount->id,
                    'material_id' => $cycleCount->material_id,
                    'bin_id' => $cycleCount->warehouse_bin_id,
                    'batch_lot' => $cycleCount->cycle_number
                ]);
            }

            // 4. Log Activity (Enhanced)
            $diff = $cycleCount->physical_qty - $cycleCount->system_qty;
            $this->logActivity($cycleCount, 'Approve and Adjust Stock', [
                'description' => "SPV Approved Cycle Count and adjusted inventory for {$cycleCount->material->nama_material} di {$cycleCount->bin->bin_code}. System: {$cycleCount->system_qty}, Fisik: {$cycleCount->physical_qty}. Selisih: {$diff}. Inventory updated.",
                'material_id' => $cycleCount->material_id,
                'batch_lot' => $cycleCount->cycle_number, 
                'qty_before' => $cycleCount->system_qty,
                'qty_after' => $cycleCount->physical_qty,
                'bin_from' => $cycleCount->warehouse_bin_id,
                'reference_document' => $cycleCount->cycle_number,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Cycle Count disetujui dan inventory telah disesuaikan.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving Cycle Count', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
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
                'system_qty' => max(0, $stock->qty_on_hand - $stock->qty_reserved),
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