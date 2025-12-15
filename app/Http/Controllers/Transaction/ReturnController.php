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

        return Inertia::render('Return', [
            'suppliers' => $suppliers,
            'shipments' => $shipments
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
