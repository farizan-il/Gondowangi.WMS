<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\ReturnModel;
use App\Models\ReturnSlip;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Added Log import
use Inertia\Inertia;
use App\Traits\ActivityLogger;

use Smalot\PdfParser\Parser;

class ReturnController extends Controller
{
    use ActivityLogger;

    public function parsePdf(Request $request)
    {
        $request->validate([
            'erp_pdf' => 'required|mimes:pdf|max:5120',
        ]);

        $pdfFile = $request->file('erp_pdf');

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfFile->getPathname());
            $text = $pdf->getText();

            // Normalize text: remove excessive whitespace
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);

            $extractedData = [
                'return_number' => '',
                'date' => '',
                'items' => [],
                'origin' => '',
            ];

            // 1. Header Extraction
            // Return Number: Internal Shipment : RET/16619
            if (preg_match('/Internal Shipment\s*:\s*([A-Za-z0-9\/]+)/i', $text, $matches)) {
                $extractedData['return_number'] = trim($matches[1]);
            }

            // Date: Schedule Date 12/12/2025 07:56:26
            if (preg_match('/Schedule Date\s*([0-9]{2}\/[0-9]{2}\/[0-9]{4}\s*[0-9]{2}:[0-9]{2}:[0-9]{2})/i', $text, $dateMatch)) {
                 $extractedData['date'] = trim($dateMatch[1]);
            } elseif (preg_match('/Schedule Date\s*([0-9]{2}\/[0-9]{2}\/[0-9]{4})/i', $text, $dateMatch)) {
                 $extractedData['date'] = trim($dateMatch[1]);
            }

            // Order/Origin
             if (preg_match('/Order\(Origin\)\s*(.*?)\s+Schedule Date/i', $text, $originMatch)) {
                $extractedData['origin'] = trim($originMatch[1]);
            }

            // 2. Items Extraction
            // Pattern: [Code] Description ... Status Location Qty Unit
            // Example: [GNAB001] NATUR Shampoo Aloe Vera 270 ml - R23 512015 Waiting Availability Production 8,0000 Pcs
            // Regex:
            // \[([A-Za-z0-9]+)\] : Code
            // \s+(.*?) : Description (and Serial if present)
            // \s+(Waiting.*?) : Status (Assuming it starts with Waiting)
            // \s+((?:Production|Stock).*?) : Location (Assuming Production or Stock)
            // \s+([0-9]+(?:[,.][0-9]+)?)\s+ : Qty
            // ([A-Za-z]+) : Unit

            $itemPattern = '/\[([A-Za-z0-9]+)\]\s+(.*?)\s+(Waiting.*?)\s+(Production.*?)\s+([0-9]+(?:[,.][0-9]+)?)\s+([A-Za-z]+)/i';
            
            if (preg_match_all($itemPattern, $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    // Handle numeric format "29,0000" -> 29.0
                    $qtyString = str_replace('.', '', $match[5]); 
                    $qtyString = str_replace(',', '.', $qtyString);
                    // Double check if comma was decimal separator. 
                    // If match was "29,0000", str_replace(',', '.', ..) gives "29.0000". Correct.
                    
                    // Sanity check description: remove Serial Number from description if it leaked?
                    // Usually description is just text. The regex (.*?) matches until Waiting...
                    // "Dus Satuan ... - R23"
                    
                    $extractedData['items'][] = [
                        'item_code' => trim($match[1]),
                        'description' => trim($match[2]),
                        'status' => trim($match[3]),
                        'location' => trim($match[4]),
                        'qty' => floatval($qtyString),
                        'uom' => trim($match[6]),
                    ];
                }
            }

             // Format Date to Y-m-d
            if (!empty($extractedData['date'])) {
                 try {
                    $dateObj = \DateTime::createFromFormat('d/m/Y H:i:s', $extractedData['date']);
                    if (!$dateObj) $dateObj = \DateTime::createFromFormat('d/m/Y', $extractedData['date']);
                    $extractedData['formatted_date'] = $dateObj ? $dateObj->format('Y-m-d') : date('Y-m-d');
                } catch (\Exception $e) { 
                    $extractedData['formatted_date'] = date('Y-m-d'); 
                }
            } else {
                $extractedData['formatted_date'] = date('Y-m-d');
            }

            return response()->json($extractedData);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memproses PDF.', 'details' => $e->getMessage()], 500);
        }
    }
    public function index()
    {
        $suppliers = \App\Models\Supplier::select('nama_supplier')->orderBy('nama_supplier')->get();
        // Fetch shipments (Incoming Goods) for reference
        $shipments = \App\Models\IncomingGood::select('incoming_number', 'no_surat_jalan')
            ->orderBy('created_at', 'desc')
            ->limit(50) // Limit to recent 50 for performance
            ->get();

        // Fetch Returns List
        $returns = \App\Models\ReturnModel::with(['items.material', 'supplier', 'reservationRequest', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($ret) {
                return [
                    'id' => $ret->id,
                    'returnNumber' => $ret->return_number,
                    'date' => $ret->return_date ? $ret->return_date->format('Y-m-d') : $ret->created_at->format('Y-m-d'),
                    'type' => $ret->return_type ?? 'Supplier',
                    'supplier' => $ret->return_type === 'Production' ? ($ret->department ?? 'Production') : ($ret->supplier->nama_supplier ?? '-'),
                    'shipmentNo' => $ret->reference_number,
                    // Map first item for summary or handle multiple in details
                    'itemCode' => $ret->items->first()->material->kode_item ?? '-',
                    'itemName' => $ret->items->first()->material->nama_material ?? '-',
                    'lotBatch' => $ret->items->first()->batch_lot ?? '-',
                    'qty' => $ret->items->sum('qty_return'),
                    'uom' => $ret->items->first()->uom ?? '-',
                    'reason' => $ret->items->first()->return_reason ?? '-',
                    'status' => $ret->status,
                ];
            });

        return Inertia::render('Return', [
            'suppliers' => $suppliers,
            'shipments' => $shipments,
            'userDept' => Auth::user()->departement, // Pass User Dept
            'initialReturns' => $returns,
        ]);
    }

    public function getMaterial($code)
    {
        $material = \App\Models\Material::where('kode_item', $code)->first();

        if (!$material) {
            return response()->json(['message' => 'Material not found'], 404);
        }

        return response()->json([
            'nama_material' => $material->nama_material,
            'satuan' => $material->satuan,
            'id' => $material->id
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
        // Handle Production Return (New Logic)
        // Handle Production Return (New Logic)
        if ($request->input('type') === 'Production') {
             $validated = $request->validate([
                'type' => 'required|in:Production',
                'date' => 'required|date',
                'shipmentNo' => 'required|string', // Reservation No
                'items' => 'required|array',
                'items.*.itemCode' => 'required|exists:materials,kode_item',
                'items.*.qty' => 'required|numeric|min:0.01',
                'items.*.uom' => 'nullable|string',
                'items.*.reason' => 'required|string',
            ]);

            DB::beginTransaction();
            try {
                $reservation = \App\Models\ReservationRequest::where('no_reservasi', $validated['shipmentNo'])->first();

                // 2. Create Return Record Header
                $returnModel = \App\Models\ReturnModel::create([
                    'return_number' => $this->generateReturnNumber(),
                    'return_type' => 'Production',
                    'return_date' => $validated['date'],
                    'department' => Auth::user()->departement,
                    'reservation_request_id' => $reservation->id ?? null,
                    'reference_number' => $validated['shipmentNo'],
                    'status' => 'Pending Approval', // Changed from 'Submitted'
                    'created_by' => Auth::id(),
                ]);

                foreach ($request->items as $itemData) {
                    $material = \App\Models\Material::where('kode_item', $itemData['itemCode'])->firstOrFail();
                    $uom = $itemData['uom'] ?? $material->satuan;

                    // 3. Create Return Item
                    \App\Models\ReturnItem::create([
                        'return_id' => $returnModel->id,
                        'material_id' => $material->id,
                        'batch_lot' => $itemData['lotBatch'],
                        'qty_return' => $itemData['qty'],
                        'uom' => $uom,
                        'return_reason' => $itemData['reason'],
                        'stock_deducted' => false, 
                        'item_condition' => 'Good',
                    ]);
                    
                    // 6. Activity Log (Request Only)
                    $this->logActivity($returnModel, 'Return Request Created', [
                        'description' => "Request Return {$itemData['qty']} {$uom} of {$material->nama_material} (Pending Approval).",
                        'material_id' => $material->id,
                        'batch_lot' => $itemData['lotBatch'],
                        'reference_document' => $validated['shipmentNo'],
                    ]);
                }

                DB::commit();
                return redirect()->back()->with('success', 'Permintaan Return berhasil dikirim. Menunggu persetujuan Supervisor.');


            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Return Store Error: " . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal menyimpan return: ' . $e->getMessage());
            }

        }

        // Existing Supplier Return Logic
        $validated = $request->validate([
            'return_slip_id' => 'required|exists:return_slips,id',
            'return_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $returnSlip = \App\Models\ReturnSlip::findOrFail($validated['return_slip_id']);

            // 1. Create Return Record
            $returnModel = \App\Models\ReturnModel::create([
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
                'from_warehouse_id' => null, // Or the KARANTINA warehouse
                'from_bin_id' => null, // Or the KARANTINA bin
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

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'return_id' => 'required|exists:returns,id',
            'target_bin_id' => 'required', // Bin Code e.g., 'QRT-HALAL'
        ]);

        DB::beginTransaction();
        try {
            $returnModel = \App\Models\ReturnModel::with('items.material')->findOrFail($validated['return_id']);

            if ($returnModel->status !== 'Pending Approval') {
                throw new \Exception("Return status is not Pending Approval.");
            }

            // Find Target Bin
            $targetBin = \App\Models\WarehouseBin::where('bin_code', $validated['target_bin_id'])->first();
            if (!$targetBin) {
                throw new \Exception("Bin '{$validated['target_bin_id']}' tidak ditemukan.");
            }

            foreach ($returnModel->items as $item) {
                // 4. Create/Update Stock in QRT (Handling Check)
                $stock = \App\Models\InventoryStock::where('material_id', $item->material_id)
                    ->where('bin_id', $targetBin->id)
                    ->where('batch_lot', $item->batch_lot)
                    ->where('status', 'KARANTINA') 
                    ->first();

                if ($stock) {
                    $stock->increment('qty_on_hand', $item->qty_return);
                    $stock->increment('qty_available', $item->qty_return);
                } else {
                    // Fetch exp_date from existing stock of the same batch to maintain consistency
                    $refStock = \App\Models\InventoryStock::where('material_id', $item->material_id)
                        ->where('batch_lot', $item->batch_lot)
                        ->whereNotNull('exp_date')
                        ->first();
                        
                    // Fallback: If no stock found use now + 1 year
                    $expDate = $refStock ? $refStock->exp_date : now()->addYears(1);

                    \App\Models\InventoryStock::create([
                        'material_id' => $item->material_id,
                        'warehouse_id' => $targetBin->warehouse_id,
                        'bin_id' => $targetBin->id,
                        'batch_lot' => $item->batch_lot,
                        'qty_on_hand' => $item->qty_return,
                        'qty_reserved' => 0,
                        'qty_available' => $item->qty_return,
                        'uom' => $item->uom,
                        'status' => 'KARANTINA', 
                        'exp_date' => $expDate, 
                        'last_movement_date' => now(),
                    ]);
                }

                // 5. Log Movement
                $movementNumber = $this->generateMovementNumber();
                StockMovement::create([
                    'movement_number' => $movementNumber,
                    'movement_type' => 'APPROVE RETURN MATERIAL',
                    'material_id' => $item->material_id,
                    'batch_lot' => $item->batch_lot,
                    'from_warehouse_id' => null, 
                    'from_bin_id' => null, 
                    'to_warehouse_id' => $targetBin->warehouse_id,
                    'to_bin_id' => $targetBin->id,
                    'qty' => $item->qty_return,
                    'uom' => $item->uom,
                    'reference_type' => \App\Models\ReturnModel::class, 
                    'reference_id' => $returnModel->id,
                    'movement_date' => now(),
                    'executed_by' => Auth::id(),
                    'notes' => "Return Approved to {$targetBin->bin_code}. Ref: " . $returnModel->reference_number,
                ]);
            }

            $returnModel->update([
                'status' => 'Approved',
                'approved_by' => Auth::id(),
            ]);
            
            // Log Approval
            $this->logActivity($returnModel, 'Return Approved', [
                'description' => "Return {$returnModel->return_number} Approved. Items moved to {$targetBin->bin_code}.",
                'approved_by' => Auth::user()->name
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Return berhasil disetujui.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Return Approve Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
    public function getDeptReservations()
    {
        $user = Auth::user();
        if (!$user || !$user->departement) {
            Log::warning("ReturnController: User has no department. User ID: " . ($user->id ?? 'unknown'));
            return response()->json([]);
        }

        Log::info("ReturnController: Fetching reservations for Dept: " . $user->departement);

        // Use ReservationRequest (Header) for filtering by status 'Completed'
        $reservations = \App\Models\ReservationRequest::query()
            ->whereIn('status', ['Completed', 'Short-Pick']) // Include Short-Pick just in case
            ->where(function ($query) use ($user) {
                // Check direct column OR via relationship
                $query->where('departemen', $user->departement)
                      ->orWhereHas('requestedBy', function ($q) use ($user) {
                          $q->where('departement', $user->departement);
                      });
            })
            ->select('no_reservasi')
            ->distinct()
            ->orderBy('created_at', 'desc')
            ->get()
            ->pluck('no_reservasi');

        Log::info("ReturnController: Found " . $reservations->count() . " reservations.");

        return response()->json($reservations);
    }

    public function getReservationDetails(Request $request)
    {
        $reservationNo = $request->input('no');
        
        if (!$reservationNo) {
             return response()->json(['error' => 'Reservation Number required'], 400);
        }

        $items = \App\Models\Reservation::with('material')
            ->where('reservation_no', $reservationNo)
            ->get()
            ->map(function ($item) {
                return [
                    'item_code' => $item->material->kode_item,
                    'item_name' => $item->material->nama_material,
                    'batch_lot' => $item->batch_lot,
                    'original_qty' => $item->qty_reserved,
                    'qty' => 0, // Default qty return 0
                    'uom' => $item->uom ?? $item->material->satuan,
                ];
            });

        return response()->json($items);
    }

    private function generateReturnNumber()
    {
        $date = date('Ymd');
        $lastReturn = \App\Models\ReturnModel::whereDate('created_at', today())
            ->latest('id')
            ->first();
            
        // Assuming format RET/YYYYMMDD/XXXX (17 chars)
        // If last return exists and follows format
        $sequence = 1;
        if ($lastReturn && preg_match('/RET\/\d+\/(\d+)/', $lastReturn->return_number, $matches)) {
            $sequence = intval($matches[1]) + 1;
        }
        
        return "RET/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
