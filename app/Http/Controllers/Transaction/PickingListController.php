<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\StockMovement;
use App\Models\WarehouseBin;
use App\Models\MaterialRemovalLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Traits\ActivityLogger;
use Illuminate\Support\Facades\Log;

class PickingListController extends Controller
{
    use ActivityLogger;
    private function mapBatchDetailToCamelCase($reservationDetail) {
        $material = optional($reservationDetail->material);
        // Coba ambil material code dari relasi reservation request item jika ada
        $requestItem = $reservationDetail->reservationRequest->items
            ->firstWhere(function ($item) use ($material) {
                // Cek kode item yang relevan (kode_item, kode_bahan, kode_pm)
                $code = $item->kode_item ?? $item->kode_bahan ?? $item->kode_pm;
                return $code === $material->kode_item;
            });

        // Tentukan QTY yang diminta untuk item agregat (diambil dari ReservationRequestItem)
        $qtyDimintaAgregat = (float) ($requestItem->qty ?? $requestItem->jumlah_permintaan ?? $requestItem->jumlah_kebutuhan ?? 0);

        return [
            'id' => $reservationDetail->id, // ID dari record Reservation (alokasi batch)
            'materialId' => $reservationDetail->material_id,
            'kodeItem' => $material->kode_item ?? 'N/A',
            'namaMaterial' => $material->nama_material ?? 'N/A',

            'lotSerial' => $reservationDetail->batch_lot,
            'sourceBin' => optional($reservationDetail->bin)->bin_code ?? 'BIN MISSING', // Ambil Bin Code dari relasi
            'sourceWarehouse' => optional($reservationDetail->warehouse)->name ?? 'WH MISSING', // Ambil Warehouse Name
            // 'destBin' => 'STAGING-001', // Asumsi dest bin adalah Staging, atau ambil dari field lain jika ada
            'qtyDiminta' => (float) $reservationDetail->qty_reserved, // Qty yang DIALOKASIKAN
            'qtyPicked' => (float) $reservationDetail->picked_qty,
            'uom' => $reservationDetail->uom,
            'status' => $reservationDetail->status, // Status alokasi (Reserved, Picked, etc.)
            'expDate' => $reservationDetail->exp_date,
            
            // Data untuk mempermudah grouping di frontend:
            'reservationRequestItemId' => $requestItem->id ?? null,
            'qtyDimintaAgregat' => $qtyDimintaAgregat,
        ];
    }

    // Helper untuk memetakan ReservationRequest (Header) sebagai Picking Task
    private function mapPickingTaskToCamelCase($request) {
        
        // $request di sini adalah objek Model ReservationRequest
        $requesterName = optional($request->requestedBy)->name ?? 'Requester Missing';
        // Fallback ke departemen user jika di request kosong
        $departemen = $request->departemen ?? optional($request->requestedBy)->departement ?? '-';

        // CORRECTED: Tentukan Batch Record (MO) berdasarkan request_type/kategori
        $batchRecord = '-';
        if ($request->request_type) {
            if ($request->request_type === 'raw-material') {
                // Raw Material → ambil dari no_bets
                $batchRecord = $request->no_bets ?? '-';
            } elseif (in_array($request->request_type, ['Packaging', 'add'])) {
                // Packaging & ADD → ambil dari no_bets_filling
                $batchRecord = $request->no_bets_filling ?? '-';
            }
        }

        return [
            'id' => $request->id,
            'toNumber' => $request->to_number ?? 'Belum Digenerate',
            'toGenerated' => !is_null($request->to_number),
            'noReservasi' => $request->no_reservasi,
            'batchRecord' => $batchRecord, 
            'tanggalDibuat' => $request->created_at,
            'requester' => $requesterName, 
            'departemen' => $departemen,
            'status' => $request->status, 
            'pickingStartedAt' => $request->picking_started_at,
            'pickingCompletedAt' => $request->picking_completed_at, 
            
            // PERUBAHAN UTAMA: Items sekarang adalah DETAIL ALOKASI BATCH (Reservations)
            'items' => $request->reservations
                ->map(fn($item) => $this->mapBatchDetailToCamelCase($item))
                ->sortBy('kodeItem') // Sort untuk grouping visual di FE
                ->values()
                ->all(),
        ];
    }

    public function index()
    {
        return Inertia::render('PickingList');
    }

    public function getPickingList()
    {
        // FIX UTAMA: Filter hanya request yang sudah memiliki alokasi stok.
        $allowedStatuses = ['Submitted', 'In Progress', 'Completed', 'Short-Pick', 'Ready to Pick', 'Reserved'];

        // Mengambil data dari ReservationRequest
        // Memuat relasi Reservation (detail alokasi batch)
        $pickingTasks = ReservationRequest::with([
            'reservations.material', // Detail Batch + Material
            'reservations.warehouse', // Detail Gudang
            'reservations.bin', // Detail Bin
            'requestedBy', // User yang request
            'items' // Item permintaan (untuk data QTY agregat jika diperlukan)
        ])  
            ->whereIn('status', $allowedStatuses)
            ->whereHas('reservations', function ($query) {
                // Pastikan hanya request yang memiliki alokasi stok yang valid
                $query->where('qty_reserved', '>', 0);
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Map data ke camelCase
        $mappedPickingTasks = $pickingTasks->map(fn($task) => $this->mapPickingTaskToCamelCase($task));
        
        return response()->json($mappedPickingTasks);
    }

    // Analyze expiry for materials before generating TO
    public function analyzeExpiry(Request $request, $reservationRequestId)
    {
        try {
            $reservationRequest = ReservationRequest::with('reservations.material')->findOrFail($reservationRequestId);
            
            $materials = [];
            $hasIssues = false;
            $today = Carbon::today();
            $warningThreshold = $today->copy()->addDays(30); // 30 days warning
            
            foreach ($reservationRequest->reservations as $reservation) {
                $expiryDate = $reservation->exp_date ? Carbon::parse($reservation->exp_date) : null;
                $status = 'ok';
                $daysUntilExpiry = null;
                
                if ($expiryDate) {
                    $daysUntilExpiry = $today->diffInDays($expiryDate, false);
                    
                    if ($expiryDate->lt($today)) {
                        $status = 'expired';
                        $hasIssues = true;
                    } elseif ($expiryDate->lte($warningThreshold)) {
                        $status = 'near-expiry';
                        $hasIssues = true;
                    }
                }
                
                $materials[] = [
                    'reservationId' => $reservation->id,
                    'materialCode' => $reservation->material->kode_item ?? 'N/A',
                    'materialName' => $reservation->material->nama_material ?? 'N/A',
                    'batchLot' => $reservation->batch_lot,
                    'expiryDate' => $expiryDate ? $expiryDate->format('Y-m-d') : null,
                    'qtyAllocated' => (float) $reservation->qty_reserved,
                    'uom' => $reservation->uom,
                    'status' => $status,
                    'daysUntilExpiry' => $daysUntilExpiry,
                ];
            }
            
            // Sort: expired first, then near-expiry, then ok
            usort($materials, function($a, $b) {
                $order = ['expired' => 0, 'near-expiry' => 1, 'ok' => 2];
                return $order[$a['status']] <=> $order[$b['status']];
            });
            
            return response()->json([
                'success' => true,
                'hasIssues' => $hasIssues,
                'materials' => $materials,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal analyzing materials: ' . $e->getMessage()
            ], 500);
        }
    }

    // Generate TO Number after expiry analysis
    public function generateToNumber(Request $request, $reservationRequestId)
    {
        DB::beginTransaction();
        try {
            $reservationRequest = ReservationRequest::with('reservations.material', 'reservations.bin')
                ->findOrFail($reservationRequestId);
            
            // Check if TO already generated
            if ($reservationRequest->to_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'TO Number sudah digenerate: ' . $reservationRequest->to_number
                ], 400);
            }
            
            $removedReservationIds = $request->input('removedReservationIds', []);
            
            // Remove selected materials and log them
            if (!empty($removedReservationIds)) {
                foreach ($removedReservationIds as $reservationId) {
                    $reservation = Reservation::with('material')->find($reservationId);
                    
                    if ($reservation && $reservation->reservation_request_id == $reservationRequestId) {
                        // Calculate days until expiry
                        $daysUntilExpiry = null;
                        $removalReason = 'manual';
                        
                        if ($reservation->exp_date) {
                            $expiryDate = Carbon::parse($reservation->exp_date);
                            $daysUntilExpiry = Carbon::today()->diffInDays($expiryDate, false);
                            
                            if ($daysUntilExpiry < 0) {
                                $removalReason = 'expired';
                            } elseif ($daysUntilExpiry <= 30) {
                                $removalReason = 'near-expiry';
                            }
                        }
                        
                        // Log the removal
                        MaterialRemovalLog::create([
                            'reservation_request_id' => $reservationRequestId,
                            'reservation_id' => $reservation->id,
                            'material_code' => $reservation->material->kode_item ?? 'N/A',
                            'material_name' => $reservation->material->nama_material ?? 'N/A',
                            'batch_lot' => $reservation->batch_lot,
                            'expiry_date' => $reservation->exp_date,
                            'qty_removed' => $reservation->qty_reserved,
                            'uom' => $reservation->uom,
                            'removal_reason' => $removalReason,
                            'days_until_expiry' => $daysUntilExpiry,
                            'removed_by' => Auth::id(),
                            'notes' => 'Removed during TO generation',
                        ]);
                        
                        // Return qty to available stock
                        $stock = InventoryStock::where('material_id', $reservation->material_id)
                            ->where('batch_lot', $reservation->batch_lot)
                            ->where('bin_id', $reservation->bin_id)
                            ->first();
                        
                        if ($stock) {
                            $stock->decrement('qty_reserved', $reservation->qty_reserved);
                            $stock->updateAvailableQty();
                        }
                        
                        // Delete the reservation
                        $reservation->delete();
                    }
                }
            }
            
            // Check if still have materials after removal
            $remainingReservations = $reservationRequest->reservations()->count();
            if ($remainingReservations === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat generate TO: Semua material telah dihapus. Tidak ada material tersisa untuk picking.'
                ], 400);
            }
            
            // Generate TO Number
            $toNumber = 'TO-' . $reservationRequest->no_reservasi;
            
            // Update reservation request
            $reservationRequest->update([
                'to_number' => $toNumber,
                'to_generated_at' => now(),
                'to_generated_by' => Auth::id(),
                'status' =>'Ready to Pick',
            ]);
            
            // Log activity
            $this->logActivity($reservationRequest, 'Generate TO Number', [
                'description' => "TO Number {$toNumber} generated. Materials removed: " . count($removedReservationIds),
                'to_number' => $toNumber,
                'removed_count' => count($removedReservationIds),
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => '✅ TO Number berhasil digenerate!',
                'toNumber' => $toNumber,
                'removedCount' => count($removedReservationIds),
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate TO Number: ' . $e->getMessage()
            ], 500);
        }
    }

    // Find replacement materials for expired items
    public function findReplacement(Request $request)
    {
        try {
            $materialCode = $request->materialCode;
            $qtyNeeded = $request->qtyNeeded;
            $uom = $request->uom;
            $reservationId = $request->reservationId;
            
            // Get the original reservation to know material_id
            $originalReservation = Reservation::with('material')->findOrFail($reservationId);
            
            // Find available stocks with same material, not expired, has available qty
            $today = Carbon::today();
            
            $availableStocks = InventoryStock::where('material_id', $originalReservation->material_id)
                ->where('qty_available', '>', 0)
                ->where(function($q) use ($today) {
                    $q->whereNull('exp_date')
                      ->orWhere('exp_date', '>', $today);
                })
                ->with(['bin', 'material'])
                ->get();
            
            if ($availableStocks->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'hasReplacement' => false,
                    'message' => 'Tidak ada stock pengganti yang tersedia'
                ]);
            }
            
            // FEFO Logic: Sort by expiry date (closest first)
            // No minimum threshold - as long as not expired, include it
            $sortedStocks = $availableStocks->sortBy(function($stock) {
                return $stock->exp_date ?? '9999-12-31';
            });
            
            // Allocate qty from sorted stocks (FEFO)
            $replacements = [];
            $remainingQty = $qtyNeeded;
            
            foreach ($sortedStocks as $stock) {
                if ($remainingQty <= 0) break;
                
                $qtyToAllocate = min($stock->qty_available, $remainingQty);
                
                $replacements[] = [
                    'stockId' => $stock->id,
                    'materialId' => $stock->material_id,
                    'batchLot' => $stock->batch_lot,
                    'expiryDate' => $stock->exp_date ? $stock->exp_date->format('Y-m-d') : null,
                    'qtyAvailable' => (float) $stock->qty_available,
                    'qtyToAllocate' => $qtyToAllocate,
                    'daysUntilExpiry' => $stock->exp_date ? 
                        Carbon::today()->diffInDays(Carbon::parse($stock->exp_date), false) : null,
                    'binId' => $stock->bin_id,
                    'binCode' => $stock->bin->code ?? 'Unknown',
                    'warehouseId' => $stock->warehouse_id,
                ];
                
                $remainingQty -= $qtyToAllocate;
            }
            
            $totalAllocated = $qtyNeeded - $remainingQty;
            
            return response()->json([
                'success' => true,
                'hasReplacement' => !empty($replacements),
                'replacements' => $replacements,
                'totalAllocated' => $totalAllocated,
                'fullyAllocated' => $remainingQty <= 0,
                'shortfall' => max(0, $remainingQty)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari pengganti: ' . $e->getMessage()
            ], 500);
        }
    }

    // Replace expired material with fresh stock
    public function replaceMaterial(Request $request)
    {
        DB::beginTransaction();
        try {
            $oldReservation = Reservation::with('material')->findOrFail($request->oldReservationId);
            $reservationRequestId = $oldReservation->reservation_request_id;
            
            // 1. Log the removal of old reservation
            MaterialRemovalLog::create([
                'reservation_request_id' => $reservationRequestId,
                'reservation_id' => $oldReservation->id,
                'material_code' => $oldReservation->material->kode_item ?? 'N/A',
                'material_name' => $oldReservation->material->nama_material ?? 'N/A',
                'batch_lot' => $oldReservation->batch_lot,
                'expiry_date' => $oldReservation->exp_date,
                'qty_removed' => $oldReservation->qty_reserved,
                'uom' => $oldReservation->uom,
                'removal_reason' => 'expired',
                'days_until_expiry' => $oldReservation->exp_date ? 
                    Carbon::today()->diffInDays(Carbon::parse($oldReservation->exp_date), false) : null,
                'removed_by' => Auth::id(),
                'notes' => 'Replaced with fresh material via auto-replacement'
            ]);
            
            // 2. Return old reserved qty to available
            $oldStock = InventoryStock::where('material_id', $oldReservation->material_id)
                ->where('batch_lot', $oldReservation->batch_lot)
                ->where('bin_id', $oldReservation->bin_id)
                ->first();
            
            if ($oldStock) {
                $oldStock->decrement('qty_reserved', $oldReservation->qty_reserved);
                $oldStock->updateAvailableQty();
            }
            
            // 3. Delete old reservation
            $oldReservation->delete();
            
            // 4. Create new reservations for replacements
            foreach ($request->replacements as $replacement) {
                $stock = InventoryStock::findOrFail($replacement['stockId']);
                
                // Create new reservation
                Reservation::create([
                    'reservation_request_id' => $reservationRequestId,
                    'material_id' => $replacement['materialId'],
                    'batch_lot' => $stock->batch_lot,
                    'expiry_date' => $stock->exp_date,
                    'qty_reserved' => $replacement['qtyToAllocate'],
                    'uom' => $stock->uom,
                    'bin_id' => $replacement['binId'],
                    'warehouse_id' => $replacement['warehouseId'],
                    'status' => 'Reserved'
                ]);
                
                // Update stock
                $stock->increment('qty_reserved', $replacement['qtyToAllocate']);
                $stock->updateAvailableQty();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Material berhasil diganti dengan stock yang lebih fresh!',
                'replacementCount' => count($request->replacements)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengganti material: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_request_id' => 'required|exists:reservation_requests,id', 
            'items' => 'required|array',
            'items.*.reservation_id' => 'required|exists:reservations,id',
            'items.*.picked_quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $reservationRequest = ReservationRequest::findOrFail($validated['reservation_request_id']);

            $allItemsPicked = true;
            $hasShortPick = false;
            $movementNumber = $this->generateMovementNumber();

            foreach ($validated['items'] as $item) {
                $reservation = Reservation::findOrFail($item['reservation_id']);
                $pickedQty = (float) $item['picked_quantity'];
                
                if ($pickedQty <= 0) {
                    if ($reservation->status === 'Reserved' || $reservation->status === 'In Progress') {
                        $allItemsPicked = false;
                    }
                    continue; 
                }

                // 1. Ambil Inventory Stock yang sesuai (Stok Asal)
                $stock = InventoryStock::where('material_id', $reservation->material_id)
                    ->where('batch_lot', $reservation->batch_lot)
                    ->where('bin_id', $reservation->bin_id) 
                    ->firstOrFail(); 
                
                // 2. Validasi Qty Picked
                if ($pickedQty > $reservation->qty_reserved) {
                    throw new \Exception("Picked quantity ({$pickedQty}) melebihi reserved quantity ({$reservation->qty_reserved}) untuk material {$stock->material->kode_item} batch {$reservation->batch_lot}.");
                }
                if ($pickedQty > $stock->qty_on_hand) {
                    throw new \Exception("Stok On Hand ({$stock->qty_on_hand}) tidak cukup untuk mempick {$pickedQty} dari material {$stock->material->kode_item} batch {$reservation->batch_lot}.");
                }

                // 3. Tentukan Status & Update Reservation
                $reservationStatus = ($pickedQty < $reservation->qty_reserved) ? 'Short-Pick' : 'Picked';
                $reservation->update([
                    'picked_qty' => $pickedQty, 
                    'status' => $reservationStatus,
                    'picked_at' => now(),
                    'picked_by' => Auth::id(),
                ]);

                // 4. Update InventoryStock (Kurangi Qty Reserved, Qty On Hand)
                // Ini adalah langkah KUNCI untuk mengurangi stok dari sistem.
                $stock->decrement('qty_on_hand', $pickedQty);
                $stock->decrement('qty_reserved', $pickedQty); 
                
                $stock->updateAvailableQty(); 
                
                // Cek jika stok habis di bin asal
                if ($stock->qty_on_hand <= 0) {
                    $stock->delete();
                    $reservation->bin->decrement('current_items');
                }
                $movementNumber = $this->generateMovementNumber();

                // 5. Create Stock Movement Record (Keluar dari sistem)
                // [PERUBAHAN] to_warehouse_id dan to_bin_id di set NULL karena barang LANGSUNG KELUAR
                StockMovement::create([
                    'movement_number' => $movementNumber,
                    'movement_type' => 'OUT', // Keluar dari Inventory
                    'material_id' => $reservation->material_id,
                    'batch_lot' => $reservation->batch_lot,
                    'from_warehouse_id' => $reservation->warehouse_id,
                    'from_bin_id' => $reservation->bin_id,
                    'to_warehouse_id' => null, // Dihapus/NULL
                    'to_bin_id' => null, // Dihapus/NULL
                    'qty' => $pickedQty,
                    'uom' => $reservation->uom,
                    'reference_type' => ReservationRequest::class,
                    'reference_id' => $reservationRequest->id,
                    'movement_date' => now(),
                    'executed_by' => Auth::id(),
                    'notes' => "Picking OUT dari {$reservation->bin->bin_code} untuk RR #{$reservationRequest->no_reservasi}",
                ]);
                
                // 6. Log activity 
                $this->logActivity($reservationRequest, 'Complete Picking Item', [
                    'description' => "Picked {$pickedQty} {$reservation->uom} of {$stock->material->nama_material} (Batch: {$reservation->batch_lot}). Barang dikeluarkan dari inventori.",
                    'material_id' => $reservation->material_id,
                    'batch_lot' => $reservation->batch_lot,
                    'qty_after' => $stock->qty_on_hand,
                    'bin_from' => $reservation->bin->bin_code,
                    'bin_to' => 'OUT', // Mengganti STAGING-001 dengan OUT
                    'reference_document' => $reservationRequest->no_reservasi,
                ]);

                if ($reservationStatus !== 'Picked') {
                    $allItemsPicked = false;
                    $hasShortPick = true;
                }
            }
            
            // 7. Update ReservationRequest status (Header)
            $totalAllocations = $reservationRequest->reservations()->count();
            $completedCount = $reservationRequest->reservations()->whereIn('status', ['Picked', 'Short-Pick'])->count();

            $finalStatus = 'In Progress'; 
            if ($completedCount === $totalAllocations) {
                $finalStatus = $hasShortPick ? 'Short-Pick' : 'Completed';
            }
            
            $updateData = ['status' => $finalStatus];
            
            Log::info("DEBUG STORE PICKING: Final Status: {$finalStatus}, Current End: {$reservationRequest->picking_completed_at}");

            if (($finalStatus === 'Completed' || $finalStatus === 'Short-Pick') && is_null($reservationRequest->picking_completed_at)) {
                $updateData['picking_completed_at'] = now();
                Log::info("DEBUG STORE PICKING: Setting picking_completed_at to " . now());
            }
            
            $reservationRequest->update($updateData);

            DB::commit();
            
            $message = "Picking Task #{$reservationRequest->no_reservasi} berhasil diselesaikan dengan status: {$finalStatus}";
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'flash' => ['type' => 'success', 'message' => $message]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Kembalikan Error Response JSON (Status 500)
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyelesaikan picking: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString()
            ], 500); 
        }
    }

    public function updateStatus(Request $request, $id) 
    {
        $request->validate(['status' => 'required|string']);
        
        try {
            $reservationRequest = ReservationRequest::findOrFail($id);
            
            Log::info("DEBUG UPDATE STATUS: Current Status: {$reservationRequest->status}, New Status: {$request->status}, Current Start: {$reservationRequest->picking_started_at}");

            $updateData = ['status' => $request->status];
            if ($request->status === 'In Progress' && is_null($reservationRequest->picking_started_at)) {
                $updateData['picking_started_at'] = now();
                Log::info("DEBUG UPDATE STATUS: Setting picking_started_at to " . now());
            }
            
            $reservationRequest->update($updateData);

            $this->logActivity($reservationRequest, 'Update Picking Status', [
                'description' => "Picking Task status diubah menjadi {$request->status} oleh " . Auth::user()->name,
                'reference_document' => $reservationRequest->no_reservasi,
            ]);

            return response()->json(['success' => true, 'message' => 'Status berhasil diubah.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengubah status: ' . $e->getMessage()], 500);
        }
    }

    // Tambahkan (atau pastikan) helper ini ada di Controller Anda:
    private function generateMovementNumber()
    {
        $date = date('Ymd');
        
        $lastMovement = StockMovement::whereDate('movement_date', today())
            ->lockForUpdate() // KUNCI: Lock baris yang ditemukan
            ->latest('movement_number')
            ->first();
            
        $sequence = $lastMovement ? (intval(substr($lastMovement->movement_number, -4)) + 1) : 1;
        
        // Pastikan Movement Number yang digenerate adalah yang tertinggi dari yang sudah ada di database saat ini
        return "MOV/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
 