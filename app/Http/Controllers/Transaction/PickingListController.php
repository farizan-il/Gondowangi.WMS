<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Traits\ActivityLogger;

class PickingListController extends Controller
{
    use ActivityLogger;

    private function mapItemToCamelCase($item) {
        // $item di sini adalah objek Model ReservationRequestItem
        
        $qtyDiminta = (float) ($item->qty ?? $item->jumlah_permintaan ?? $item->jumlah_kebutuhan ?? 0);

        return [
            'id' => $item->id, // ID dari ReservationRequestItem
            // Memilih kode item yang relevan berdasarkan tipe request
            'kodeItem' => $item->kode_item ?? $item->kode_bahan ?? $item->kode_pm ?? 'CODE MISSING',
            'namaMaterial' => $item->nama_material ?? $item->nama_bahan ?? 'Material Name Missing', 
            
            // Asumsi Lot/Bin Location akan ditambahkan saat proses Picking
            'lotSerial' => '-', // Data ini harusnya dicari dari Stock saat picking
            'sourceBin' => 'WH_LOC_UNK', // Akan diisi saat picking
            
            'qtyDiminta' => $qtyDiminta,
            'qtyPicked' => (float) ($item->qty_picked ?? 0), // Mengambil dari qty_picked di item detail
            'uom' => $item->uom ?? 'UOM Missing',
            'status' => $item->status ?? 'Pending', // Status Item (bukan status Request)
            'stockId' => null, // Akan diisi saat picking
        ];
    }

    // Helper untuk memetakan ReservationRequest (Header) sebagai Picking Task
    private function mapPickingTaskToCamelCase($request) {
        
        // $request di sini adalah objek Model ReservationRequest
        $requesterName = optional($request->requestedBy)->name ?? 'Requester Missing';
        $departemen = $request->departemen ?? '-';

        return [
            'id' => $request->id,
            // Menggunakan No Reservasi sebagai TO Number untuk kesederhanaan
            'toNumber' => 'TO-' . $request->no_reservasi, 
            'noReservasi' => $request->no_reservasi,
            'tanggalDibuat' => $request->created_at,
            'requester' => $requesterName, 
            'departemen' => $departemen,
            // Mengambil status dari Request Header
            'status' => $request->status, 
            
            // Memetakan semua item detail ke camelCase
            'items' => $request->items->map(fn($item) => $this->mapItemToCamelCase($item)), 
        ];
    }

    public function index()
    {
        return Inertia::render('PickingList');
    }

    public function getPickingList()
    {
        // FIX UTAMA: Filter hanya request yang sudah Approved/Siap Dipick.
        // Asumsi alur: Submitted -> Approved -> Ready to Pick/Picking List.
        $allowedStatuses = ['Submitted', 'In Progress', 'Completed', 'Short-Pick', 'Ready to Pick'];

        // Mengambil data dari ReservationRequest
        $pickingTasks = ReservationRequest::with([
            'items', // Detail item yang diminta
            'requestedBy', // User yang request
        ])  
            // Hanya ambil request yang statusnya Approved atau sedang diproses
            ->whereIn('status', $allowedStatuses) 
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Map data ke camelCase
        $mappedPickingTasks = $pickingTasks->map(fn($task) => $this->mapPickingTaskToCamelCase($task));
        
        return response()->json($mappedPickingTasks);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'items' => 'required|array',
            'items.*.stock_id' => 'required|exists:inventory_stocks,id',
            'items.*.picked_quantity' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            $reservation = Reservation::findOrFail($validated['reservation_id']);

            // 1. Logging sebelum perubahan (sebelumnya ada di perbaikan sebelumnya, dipertahankan)

            // 2. Update status inventori stok
            foreach ($validated['items'] as $item) {
                $stock = InventoryStock::findOrFail($item['stock_id']);
                
                // Pastikan logika validasi stok ada di sini sebelum update
                if ($stock->qty_available < $item['picked_quantity']) {
                    throw new \Exception('Insufficient stock for picking.');
                }

                $stock->decrement('qty_on_hand', $item['picked_quantity']);
                $stock->decrement('qty_available', $item['picked_quantity']);
                $stock->increment('qty_picked', $item['picked_quantity']);

                // 3. Create Stock Movement Record (sebelumnya ada di perbaikan sebelumnya, dipertahankan)
                $movementNumber = $this->generateMovementNumber();
                StockMovement::create([
                    'movement_number' => $movementNumber,
                    'movement_type' => 'PICKING',
                    'material_id' => $stock->material_id,
                    'batch_lot' => $stock->batch_lot,
                    'from_warehouse_id' => $stock->warehouse_id,
                    'from_bin_id' => $stock->bin_id,
                    'to_warehouse_id' => null, 
                    'to_bin_id' => null, 
                    'qty' => $item['picked_quantity'],
                    'uom' => $stock->uom,
                    'reference_type' => 'reservation',
                    'reference_id' => $reservation->id,
                    'movement_date' => now(),
                    'executed_by' => Auth::id(),
                    'notes' => "Picking for reservation {$reservation->reservation_number}",
                ]);

                // 4. Log the activity (sebelumnya ada di perbaikan sebelumnya, dipertahankan)
                $this->logActivity($reservation, 'Picking', [
                    'description' => "Picked {$item['picked_quantity']} {$stock->uom} of {$stock->material->nama_material} for reservation {$reservation->reservation_number}.",
                    'material_id' => $stock->material_id,
                    'batch_lot' => $stock->batch_lot,
                    'qty_after' => $item['picked_quantity'],
                    'reference_document' => $movementNumber,
                ]);
            }
            
            // 5. Update reservation status
            // NOTE: Status ini diubah dari status awal ('Submitted' atau 'Pending')
            $reservation->update(['status' => 'Completed']); 

            DB::commit();
            
            // Menggunakan Inertia redirect untuk feedback sukses
            return redirect()
                ->route('transaction.picking-list') 
                ->with('flash', ['type' => 'success', 'message' => 'Picking Task berhasil diselesaikan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Menggunakan Inertia redirect back untuk feedback error
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
