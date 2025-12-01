<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\ReturnModel;
use App\Models\ReturnSlip;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Traits\ActivityLogger;

class ReturnController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        return Inertia::render('Return');
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
            'return_slip_id' => 'required|exists:return_slips,id',
            'return_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $returnSlip = ReturnSlip::findOrFail($validated['return_slip_id']);

            // 1. Create Return Record
            $returnModel = ReturnModel::create([
                'return_slip_id' => $returnSlip->id,
                'return_date' => $validated['return_date'],
                'status' => 'Returned',
                'notes' => $validated['notes'],
                'returned_by' => Auth::id(),
            ]);

            // 2. Create Stock Movement Record
            $movementNumber = $this->generateMovementNumber();
            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'movement_type' => 'RETURN',
                'material_id' => $returnSlip->material_id,
                'batch_lot' => $returnSlip->batch_lot,
                'from_warehouse_id' => null, // Or the quarantine warehouse
                'from_bin_id' => null, // Or the quarantine bin
                'to_warehouse_id' => null, // Represents supplier
                'to_bin_id' => null, // Represents supplier
                'qty' => $returnSlip->qty_return,
                'uom' => $returnSlip->uom,
                'reference_type' => 'return_slip',
                'reference_id' => $returnSlip->id,
                'movement_date' => now(),
                'executed_by' => Auth::id(),
                'notes' => "Return to supplier for slip {$returnSlip->return_number}",
            ]);

            // 3. Log the activity
            $this->logActivity($returnModel, 'Create Return', [
                'description' => "Returned {$returnSlip->qty_return} {$returnSlip->uom} of {$returnSlip->material->nama_material} to supplier.",
                'material_id' => $returnSlip->material_id,
                'batch_lot' => $returnSlip->batch_lot,
                'qty_after' => $returnSlip->qty_return,
                'reference_document' => $returnSlip->return_number,
            ]);

            // 4. Update return slip status
            $returnSlip->update(['status' => 'Returned']);

            DB::commit();

            return redirect()->back()->with('success', 'Return successful.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Return failed: ' . $e->getMessage());
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
