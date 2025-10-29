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

    public function getReservations()
    {
        $reservations = ReservationRequest::with('items')->get();
        return response()->json($reservations);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'noReservasi' => 'required|string|unique:reservation_requests,no_reservasi',
            'tanggalPermintaan' => 'required|date',
            'departemen' => 'nullable|string',
            'alasanReservasi' => 'nullable|string',
            'namaProduk' => 'nullable|string',
            'noBetsFilling' => 'nullable|string',
            'kodeProduk' => 'nullable|string',
            'noBets' => 'nullable|string',
            'besarBets' => 'nullable|numeric',
            'items' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $reservationRequest = ReservationRequest::create([
                'no_reservasi' => $validated['noReservasi'],
                'request_type' => $request->selectedCategory,
                'tanggal_permintaan' => $validated['tanggalPermintaan'],
                'status' => 'Submitted',
                'departemen' => $validated['departemen'],
                'alasan_reservasi' => $validated['alasanReservasi'],
                'nama_produk' => $validated['namaProduk'],
                'no_bets_filling' => $validated['noBetsFilling'],
                'kode_produk' => $validated['kodeProduk'],
                'no_bets' => $validated['noBets'],
                'besar_bets' => $validated['besarBets'],
                'requested_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                $reservationRequest->items()->create($item);
            }

            DB::commit();

            return response()->json(['message' => 'Reservation request created successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create reservation request.'], 500);
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
