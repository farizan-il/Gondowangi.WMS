<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\StockMovement;
use App\Models\WarehouseBin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Traits\ActivityLogger;

class BintobinController extends Controller
{
    use ActivityLogger;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Bintobin', [
            'title' => 'Perpindahan Barang'
        ]);
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
            'from_bin_id' => 'required|exists:warehouse_bins,id',
            'to_bin_id' => 'required|exists:warehouse_bins,id',
            'stock_id' => 'required|exists:inventory_stocks,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            $stock = InventoryStock::findOrFail($validated['stock_id']);
            $fromBin = WarehouseBin::findOrFail($validated['from_bin_id']);
            $toBin = WarehouseBin::findOrFail($validated['to_bin_id']);

            // 1. Check if enough stock is available
            if ($stock->qty_available < $validated['quantity']) {
                throw new \Exception('Insufficient stock quantity.');
            }

            // 2. Decrement stock from the source bin
            $stock->decrement('qty_on_hand', $validated['quantity']);
            $stock->decrement('qty_available', $validated['quantity']);

            // 3. Increment stock in the destination bin or create new stock entry
            $destinationStock = InventoryStock::firstOrCreate(
                [
                    'material_id' => $stock->material_id,
                    'warehouse_id' => $toBin->warehouse_id,
                    'bin_id' => $toBin->id,
                    'batch_lot' => $stock->batch_lot,
                    'status' => $stock->status,
                ],
                [
                    'exp_date' => $stock->exp_date,
                    'qty_on_hand' => 0,
                    'qty_reserved' => 0,
                    'qty_available' => 0,
                    'uom' => $stock->uom,
                    'gr_id' => $stock->gr_id,
                ]
            );
            $destinationStock->increment('qty_on_hand', $validated['quantity']);
            $destinationStock->increment('qty_available', $validated['quantity']);

            // 4. Create Stock Movement Record
            $movementNumber = $this->generateMovementNumber();
            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'movement_type' => 'BIN_TO_BIN',
                'material_id' => $stock->material_id,
                'batch_lot' => $stock->batch_lot,
                'from_warehouse_id' => $fromBin->warehouse_id,
                'from_bin_id' => $fromBin->id,
                'to_warehouse_id' => $toBin->warehouse_id,
                'to_bin_id' => $toBin->id,
                'qty' => $validated['quantity'],
                'uom' => $stock->uom,
                'reference_type' => 'self',
                'reference_id' => null,
                'movement_date' => now(),
                'executed_by' => Auth::id(),
                'notes' => "Transfer from {$fromBin->bin_code} to {$toBin->bin_code}",
            ]);

            // 5. Log the activity
            $this->logActivity($movement, 'Move', [
                'description' => "Moved {$validated['quantity']} {$stock->uom} of {$stock->material->nama_material} from {$fromBin->bin_code} to {$toBin->bin_code}.",
                'material_id' => $stock->material_id,
                'batch_lot' => $stock->batch_lot,
                'qty_before' => $stock->qty_on_hand + $validated['quantity'],
                'qty_after' => $stock->qty_on_hand,
                'bin_from' => $fromBin->bin_code,
                'bin_to' => $toBin->bin_code,
                'reference_document' => $movementNumber,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Bin transfer successful.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Bin transfer failed: ' . $e->getMessage());
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
