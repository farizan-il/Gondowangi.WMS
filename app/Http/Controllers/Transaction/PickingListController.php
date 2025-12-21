<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\StockMovement;
use App\Models\WarehouseBin;
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
            'expDate' => $reservationDetail->expiry_date,
            
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

        // TAMBAHAN: Tentukan Batch Record (MO) berdasarkan request_type/kategori
        $batchRecord = '-';
        if ($request->request_type) {
            if ($request->request_type === 'raw-material') {
                $batchRecord = $request->no_bets ?? '-';
            } elseif (in_array($request->request_type, ['packaging', 'add'])) {
                $batchRecord = $request->no_bets_filling ?? '-';
            }
        }

        return [
            'id' => $request->id,
            'toNumber' => 'TO-' . $request->no_reservasi, 
            'noReservasi' => $request->no_reservasi,
            'batchRecord' => $batchRecord, // << FIELD BARU: Batch Record (MO)
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
 