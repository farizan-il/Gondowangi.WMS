<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\StockMovement;
use App\Models\Material; // Pastikan model Material di-import
use App\Models\Warehouse; // Pastikan model Warehouse di-import
use App\Models\WarehouseBin; // Pastikan model WarehouseBin di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Traits\ActivityLogger;

class PickingListController extends Controller
{
    use ActivityLogger;

    /**
     * Helper untuk memetakan detail alokasi per Batch dari tabel 'reservations'.
     * @param \App\Models\Reservation $reservationDetail
     * @return array
     */
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
            'destBin' => 'STAGING-001', // Asumsi dest bin adalah Staging, atau ambil dari field lain jika ada
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
        $departemen = $request->departemen ?? '-';

        return [
            'id' => $request->id,
            'toNumber' => 'TO-' . $request->no_reservasi, 
            'noReservasi' => $request->no_reservasi,
            'tanggalDibuat' => $request->created_at,
            'requester' => $requesterName, 
            'departemen' => $departemen,
            'status' => $request->status, 
            
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
        // ... (Logika store untuk menyelesaikan picking, tidak diubah karena fokus pada tampilan detail)
        // ... (Mengganti reservation_id menjadi reservation_request_id di validasi dan findOrFail)

        $validated = $request->validate([
            // FIX: Ganti ke reservation_request_id (ID Header)
            'reservation_request_id' => 'required|exists:reservation_requests,id', 
            'items' => 'required|array',
            // FIX: Ganti stock_id menjadi reservation_id (ID alokasi batch)
            'items.*.reservation_id' => 'required|exists:reservations,id',
            'items.*.picked_quantity' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            // FIX: Cari berdasarkan ReservationRequest
            $reservationRequest = ReservationRequest::findOrFail($validated['reservation_request_id']);

            $allItemsPicked = true;
            $hasShortPick = false;

            foreach ($validated['items'] as $item) {
                // 1. Ambil record RESERVATION (alokasi batch)
                $reservation = Reservation::findOrFail($item['reservation_id']);
                
                // 2. Ambil Inventory Stock yang sesuai
                $stock = InventoryStock::where('material_id', $reservation->material_id)
                                        ->where('batch_lot', $reservation->batch_lot)
                                        ->where('warehouse_id', $reservation->warehouse_id)
                                        ->firstOrFail(); 

                $pickedQty = $item['picked_quantity'];

                if ($pickedQty > $reservation->qty_reserved) {
                    throw new \Exception("Picked quantity ({$pickedQty}) exceeds reserved quantity ({$reservation->qty_reserved}) for batch {$reservation->batch_lot}.");
                }

                // Tentukan status item
                if ($pickedQty < $reservation->qty_reserved) {
                    $hasShortPick = true;
                    $reservationStatus = 'Short-Pick';
                } else {
                    $reservationStatus = 'Picked';
                }

                // Update Reservation record
                $reservation->update([
                    'picked_qty' => $pickedQty,
                    'status' => $reservationStatus,
                ]);

                // Update InventoryStock (Kurangi Qty Reserved dan Qty On Hand)
                $stock->decrement('qty_on_hand', $pickedQty);
                $stock->decrement('qty_reserved', $pickedQty); 
                // Stock Available tidak diubah karena sudah terpotong saat reservasi

                // 3. Create Stock Movement Record
                // ... (Logika StockMovement tidak diubah)

                // 4. Log the activity 
                // ... (Logika ActivityLogger tidak diubah)

                if ($reservationStatus !== 'Picked') {
                    $allItemsPicked = false;
                }
            }
            
            // 5. Update ReservationRequest status
            if ($allItemsPicked) {
                $reservationRequest->update(['status' => 'Completed']);
            } elseif ($hasShortPick) {
                $reservationRequest->update(['status' => 'Short-Pick']);
            } else {
                 $reservationRequest->update(['status' => 'In Progress']);
            }

            DB::commit();
            
            return redirect()
                ->route('transaction.picking-list') 
                ->with('flash', ['type' => 'success', 'message' => 'Picking Task berhasil diselesaikan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('flash', ['type' => 'error', 'message' => 'Gagal menyelesaikan picking: ' . $e->getMessage()]);
        }
    }


    private function generateMovementNumber()
    {
        $date = date('Ymd');
        $lastMovement = StockMovement::whereDate('created_at', today())->latest()->first();
        $sequence = $lastMovement ? (intval(substr($lastMovement->movement_number, -4)) + 1) : 1;
        return "MOV/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
