<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\Reservation;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Traits\ActivityLogger;

class PickingListController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        return Inertia::render('PickingList');
    }

    public function getPickingList()
    {
        $pickingList = Reservation::with('request.items')->where('status', 'Picked')->get();
        return response()->json($pickingList);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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

            foreach ($validated['items'] as $item) {
                $stock = InventoryStock::findOrFail($item['stock_id']);

                // 1. Check if enough stock is available
                if ($stock->qty_available < $item['picked_quantity']) {
                    throw new \Exception('Insufficient stock for picking.');
                }

                // 2. Update stock quantities
                $stock->decrement('qty_on_hand', $item['picked_quantity']);
                $stock->decrement('qty_available', $item['picked_quantity']);
                $stock->increment('qty_picked', $item['picked_quantity']);

                // 3. Create Stock Movement Record
                $movementNumber = $this->generateMovementNumber();
                $movement = StockMovement::create([
                    'movement_number' => $movementNumber,
                    'movement_type' => 'PICKING',
                    'material_id' => $stock->material_id,
                    'batch_lot' => $stock->batch_lot,
                    'from_warehouse_id' => $stock->warehouse_id,
                    'from_bin_id' => $stock->bin_id,
                    'to_warehouse_id' => null, // Or a staging area warehouse
                    'to_bin_id' => null, // Or a staging area bin
                    'qty' => $item['picked_quantity'],
                    'uom' => $stock->uom,
                    'reference_type' => 'reservation',
                    'reference_id' => $reservation->id,
                    'movement_date' => now(),
                    'executed_by' => Auth::id(),
                    'notes' => "Picking for reservation {$reservation->reservation_number}",
                ]);

                // 4. Log the activity
                $this->logActivity($reservation, 'Picking', [
                    'description' => "Picked {$item['picked_quantity']} {$stock->uom} of {$stock->material->nama_material} for reservation {$reservation->reservation_number}.",
                    'material_id' => $stock->material_id,
                    'batch_lot' => $stock->batch_lot,
                    'qty_after' => $item['picked_quantity'],
                    'reference_document' => $movementNumber,
                ]);
            }

            // 5. Update reservation status
            $reservation->update(['status' => 'Picked']);

            DB::commit();

            return redirect()->back()->with('success', 'Picking successful.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Picking failed: ' . $e->getMessage());
        }
    }

    private function generateMovementNumber()
    {
        $date = date('Ymd');
        $lastMovement = StockMovement::whereDate('created_at', today())->latest()->first();
        $sequence = $lastMovement ? (intval(substr($lastMovement->movement_number, -4)) + 1) : 1;
        return "MOV/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
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
