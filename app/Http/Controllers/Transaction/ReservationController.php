<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\Reservation;
use App\Models\ReservationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Traits\ActivityLogger;

class ReservationController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        return Inertia::render('Reservation');
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
            'request_id' => 'required|exists:reservation_requests,id',
            'items' => 'required|array',
            'items.*.stock_id' => 'required|exists:inventory_stocks,id',
            'items.*.reserved_quantity' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            $reservationRequest = ReservationRequest::findOrFail($validated['request_id']);

            // Generate reservation number
            $reservationNumber = $this->generateReservationNumber();
            $reservation = Reservation::create([
                'reservation_number' => $reservationNumber,
                'request_id' => $reservationRequest->id,
                'status' => 'Reserved',
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                $stock = InventoryStock::findOrFail($item['stock_id']);

                // 1. Check if enough stock is available
                if ($stock->qty_available < $item['reserved_quantity']) {
                    throw new \Exception('Insufficient stock for reservation.');
                }

                // 2. Update stock quantities
                $stock->decrement('qty_available', $item['reserved_quantity']);
                $stock->increment('qty_reserved', $item['reserved_quantity']);

                // 3. Log the activity
                $this->logActivity($reservation, 'Create Reservation', [
                    'description' => "Reserved {$item['reserved_quantity']} {$stock->uom} of {$stock->material->nama_material} for request {$reservationRequest->request_number}.",
                    'material_id' => $stock->material_id,
                    'batch_lot' => $stock->batch_lot,
                    'qty_after' => $item['reserved_quantity'],
                    'reference_document' => $reservationNumber,
                ]);
            }

            // 4. Update reservation request status
            $reservationRequest->update(['status' => 'Processed']);

            DB::commit();

            return redirect()->back()->with('success', "Reservation {$reservationNumber} created successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Reservation failed: ' . $e->getMessage());
        }
    }

    private function generateReservationNumber()
    {
        $date = date('Ymd');
        $lastReservation = Reservation::whereDate('created_at', today())->latest()->first();
        $sequence = $lastReservation ? (intval(substr($lastReservation->reservation_number, -4)) + 1) : 1;
        return "RES/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
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
