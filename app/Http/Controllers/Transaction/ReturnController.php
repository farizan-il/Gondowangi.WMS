<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\ReturnModel;
use App\Models\ReturnSlip;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use App\Models\Material;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
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

            // 1. Normalisasi teks (Enter jadi spasi, spasi ganda jadi satu)
            $normalizedText = preg_replace('/\s+/', ' ', $text);
            $normalizedText = trim($normalizedText);

            $extractedData = [
                'status' => 'success',
                'return_number' => '',
                'date' => '',
                'formatted_date' => date('Y-m-d'),
                'items' => [],
                'unknown_items' => [],
            ];

            // STEP 1: Extract Internal Shipment Number
            if (preg_match('/Internal Shipment\s*:\s*([A-Za-z0-9\/\-]+)/i', $normalizedText, $matches)) {
                $extractedData['return_number'] = trim($matches[1]);
                
                // Cek Duplicate
                $exists = ReturnModel::where('return_number', $extractedData['return_number'])->exists();
                if ($exists) {
                    return response()->json([
                        'status' => 'duplicate',
                        'message' => 'No Return / Shipment Number ini sudah terdaftar!',
                        'return_number' => $extractedData['return_number']
                    ]);
                }
            }

            // STEP 2: Extract Schedule Date
            if (preg_match('/Schedule Date\s+([0-9]{2}\/[0-9]{2}\/[0-9]{4})/i', $normalizedText, $dateMatch)) {
                try {
                    $dateObj = \DateTime::createFromFormat('d/m/Y', trim($dateMatch[1]));
                    if ($dateObj) {
                        $extractedData['formatted_date'] = $dateObj->format('Y-m-d');
                    }
                } catch (\Exception $e) {}
            }

            // STEP 3: Extract Items (LOGIKA UTAMA)
            // Regex menangkap Status 'Done'/'Waiting' dan Location 'Production'/'Productio n'
            $mainPattern = '/\[([A-Za-z0-9]+)\]\s+(.+?)\s+(Waiting\s+[A-Za-z\s]+?|Done)\s+(Production|Productio\s+n|Stock)\s+([\d.,]+)\s+(Pcs|Kg|L|Unit)/i';
            
            $parsedItems = [];

            if (preg_match_all($mainPattern, $normalizedText, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $itemCode = trim($match[1]);
                    $fullContent = trim($match[2]); // Deskripsi + Serial (misal: "...R23 2346613112 5ULG")
                    
                    $serialNumber = 'N/A';
                    $description = $fullContent;

                    // --- LOGIKA MENYATUKAN SERIAL NUMBER (CONCATENATION) ---

                    // KASUS 1: Serial Terpisah Spasi (Contoh: "2346613112 5ULG")
                    // Regex: Mencari Angka(5 digit+) [SPASI] Karakter(Huruf/Angka) di akhir kalimat
                    if (preg_match('/(\d{5,})\s+([A-Z0-9]+)$/', $fullContent, $splitMatch)) {
                        
                        // INI BAGIAN PENGGABUNGANNYA:
                        // $splitMatch[1] = "2346613112"
                        // $splitMatch[2] = "5ULG"
                        // Digabung dengan titik (.) menjadi "23466131125ULG"
                        $serialNumber = $splitMatch[1] . $splitMatch[2]; 

                        // Hapus bagian serial number ASLI (yang ada spasinya) dari deskripsi
                        // $splitMatch[0] berisi "2346613112 5ULG"
                        $description = str_replace($splitMatch[0], '', $fullContent);

                    } 
                    // KASUS 2: Serial Normal/Menyatu (Contoh: "512031")
                    elseif (preg_match('/(\d{5,}[A-Z0-9]*)$/', $fullContent, $normalMatch)) {
                        $serialNumber = trim($normalMatch[1]);
                        
                        // Hapus serial dari deskripsi
                        $description = str_replace($normalMatch[0], '', $fullContent);
                    }

                    // Bersihkan sisa spasi/dash di ujung deskripsi
                    $description = trim(preg_replace('/[\s\-]+$/', '', $description));

                    // Cleaning Qty
                    $qtyClean = str_replace(['.', ','], ['', '.'], $match[5]);

                    $parsedItems[] = [
                        'item_code' => $itemCode,
                        'description' => $description,
                        'serial_number' => $serialNumber, // Hasilnya PASTI tergabung sekarang
                        'qty' => (float) $qtyClean,
                        'uom' => trim($match[6]),
                    ];
                }
            }

            // Fallback Pattern (Jaga-jaga jika pattern utama gagal total)
            if (empty($parsedItems)) {
                $fallbackPattern = '/\[([A-Za-z0-9]+)\]\s+(.+?)\s+([\d.,]+)\s+(Pcs|Kg|L|Unit)/i';
                if (preg_match_all($fallbackPattern, $normalizedText, $fallbackMatches, PREG_SET_ORDER)) {
                     foreach ($fallbackMatches as $match) {
                        $itemCode = trim($match[1]);
                        $rawDesc = trim($match[2]);
                        $serialFallback = 'N/A';
                        
                        // Coba logika split di fallback juga
                        if (preg_match('/(\d{5,})\s+([A-Z0-9]+)$/', $rawDesc, $sm)) {
                             $serialFallback = $sm[1] . $sm[2]; // GABUNG
                             $rawDesc = str_replace($sm[0], '', $rawDesc); // HAPUS YANG ADA SPASINYA
                        } elseif (preg_match('/\b(\d{5,}[A-Z0-9]*)\b/', $rawDesc, $nm)) {
                             $serialFallback = $nm[1];
                             $rawDesc = str_replace($nm[1], '', $rawDesc);
                        }

                        $qtyClean = str_replace(['.', ','], ['', '.'], $match[3]);
                        
                        $parsedItems[] = [
                            'item_code' => $itemCode,
                            'description' => trim(preg_replace('/[\s\-]+$/', '', $rawDesc)),
                            'serial_number' => $serialFallback,
                            'qty' => (float) $qtyClean,
                            'uom' => trim($match[4]),
                        ];
                     }
                }
            }

            // Validasi Master Data & Update Nama Material
            foreach ($parsedItems as $item) {
                $materialExists = Material::where('kode_item', $item['item_code'])->first();

                if ($materialExists) {
                    $item['description'] = $materialExists->nama_material; 
                    $item['uom'] = $materialExists->satuan;
                    $extractedData['items'][] = $item;
                } else {
                    $extractedData['unknown_items'][] = [
                        'item_code' => $item['item_code'],
                        'description' => $item['description']
                    ];
                }
            }

            return response()->json($extractedData);

        } catch (\Exception $e) {
            Log::error("Return PDF Parse Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => 'Gagal memproses PDF Return.', 
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // Helper function agar kode lebih rapi (opsional, masukkan di dalam class controller)
    private function processParsedItem($match, &$parsedItems, $type) {
        $itemCode = trim($match[1]);
        $descriptionBlock = trim($match[2]);
        
        // Cleaning Qty
        $qtyString = ($type == 'main') ? $match[5] : $match[3];
        $qtyClean = str_replace('.', '', $qtyString); // Hapus ribuan
        $qtyClean = str_replace(',', '.', $qtyClean); // Ubah desimal
        
        // Extract Serial Number
        $serialNumber = 'N/A';
        if (preg_match('/\b(\d{5,7})\b/', $descriptionBlock, $serialMatch)) {
            $serialNumber = $serialMatch[1];
            $descriptionBlock = preg_replace('/\b\d{5,7}\b/', '', $descriptionBlock);
        }
        
        $description = preg_replace('/\s+/', ' ', trim($descriptionBlock));

        $parsedItems[] = [
            'item_code' => $itemCode,
            'description' => $description,
            'serial_number' => $serialNumber,
            'qty' => (float) $qtyClean,
            'uom' => ($type == 'main') ? trim($match[6]) : trim($match[4]),
            'status' => ($type == 'main') ? trim($match[3]) : '',
            'location' => ($type == 'main') ? trim($match[4]) : 'Production',
        ];
    }
    
    public function index()
    {
        $suppliers = \App\Models\Supplier::select('nama_supplier')->orderBy('nama_supplier')->get();
        $shipments = \App\Models\IncomingGood::select('incoming_number', 'no_surat_jalan')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $rejectedShipments = \App\Models\IncomingGood::whereHas('inventoryStocks', function ($query) {
            $query->where('status', 'REJECTED')
                ->whereHas('bin', function ($q) {
                    $q->where('bin_code', 'NOT LIKE', 'QRT-%');
                });
        })
        ->select('incoming_number', 'no_surat_jalan')
        ->distinct()
        ->orderBy('created_at', 'desc')
        ->get();

        $returns = \App\Models\ReturnModel::with(['items.material', 'supplier', 'reservationRequest', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($ret) {
                // Kita siapkan array items lengkap untuk Modal Detail
                $mappedItems = $ret->items->map(function($item) {
                    return [
                        'id' => $item->id, // Return item ID for editing
                        'item_code' => $item->material->kode_item ?? '-',
                        'item_name' => $item->material->nama_material ?? '-',
                        'batch_lot' => $item->batch_lot ?? '-',
                        'qty' => $item->qty_return,
                        'uom' => $item->uom ?? '-',
                        'reason' => $item->return_reason ?? '-',
                    ];
                });

                return [
                    'id' => $ret->id,
                    'returnNumber' => $ret->return_number,
                    'date' => $ret->return_date ? $ret->return_date->format('Y-m-d') : $ret->created_at->format('Y-m-d'),
                    'type' => $ret->return_type ?? 'Supplier',
                    'supplier' => $ret->return_type === 'Production' ? ($ret->department ?? 'Production') : ($ret->supplier->nama_supplier ?? '-'),
                    'shipmentNo' => $ret->reference_number,
                    
                    // PERUBAHAN DISINI: Kirim array items lengkap
                    'items' => $mappedItems, 
                    'total_qty' => $ret->items->sum('qty_return'),
                    'status' => $ret->status,
                ];
            });

        return Inertia::render('Return', [
            'suppliers' => $suppliers,
            'shipments' => $shipments,
            'rejectedShipments' => $rejectedShipments,
            'userDept' => Auth::user()->departement,
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

    public function create() {}

    public function store(Request $request)
    {
        // 0. Manual Trim Inputs (Membersihkan spasi di awal/akhir)
        $input = $request->all();
        if (isset($input['items']) && is_array($input['items'])) {
            foreach ($input['items'] as &$item) {
                if (isset($item['itemCode'])) {
                    $item['itemCode'] = trim($item['itemCode']);
                }
            }
        }
        $request->merge($input);

        // 1. Handle Rejected Material Return (To Supplier)
        if ($request->input('type') === 'Rejected Material') {
            $validated = $request->validate([
                'type' => 'required|in:Rejected Material',
                'date' => 'required|date',
                'shipmentNo' => 'required|string',
                'items' => 'required|array',
                'items.*.id' => 'nullable|exists:inventory_stock,id', 
                // Kita HAPUS Rule::exists disini agar pesan error manual di bawah bisa muncul
                'items.*.itemCode' => 'required', 
                'items.*.lotBatch' => 'required|string',
                'items.*.qty' => 'required|numeric|min:0.01',
                'items.*.uom' => 'nullable|string',
                'items.*.reason' => 'required|string',
            ]);

            DB::beginTransaction();
            try {
                // UPDATE: return_number diambil dari shipmentNo
                $returnModel = \App\Models\ReturnModel::create([
                    'return_number' => $validated['shipmentNo'], 
                    'return_type' => 'Supplier',
                    'return_date' => $validated['date'],
                    'reference_number' => $validated['shipmentNo'],
                    'status' => 'Returned',
                    'created_by' => Auth::id(),
                    'returned_by' => Auth::id(),
                ]);

                foreach ($request->items as $index => $itemData) {
                    // Cari Material Manual untuk mendapatkan ID Relasi
                    $material = \App\Models\Material::where('kode_item', $itemData['itemCode'])->first();
                    
                    if (!$material) {
                         // Pesan Error Spesifik
                         throw new \Exception("Item dengan Kode '{$itemData['itemCode']}' (Baris ke-" . ($index + 1) . ") tidak ditemukan di Master Data Material.");
                    }
                    
                    $stock = null;
                    if (!empty($itemData['id'])) {
                        $stock = \App\Models\InventoryStock::find($itemData['id']);
                    } else {
                        // Logic cari stock manual jika tidak ada ID
                        $stock = \App\Models\InventoryStock::where('material_id', $material->id)
                            ->where('batch_lot', $itemData['lotBatch'])
                            ->where('status', 'REJECTED')
                            ->whereHas('bin', function($q) {
                                $q->where('bin_code', 'NOT LIKE', 'QRT-%');
                            })
                            ->where('qty_on_hand', '>=', 0) 
                            ->orderBy('qty_on_hand', 'desc')
                            ->first();
                    }

                    if (!$stock) {
                        throw new \Exception("Stock REJECTED tidak ditemukan untuk Item '{$itemData['itemCode']}' dengan Batch '{$itemData['lotBatch']}'.");
                    }
                    
                    if ($stock->qty_on_hand < $itemData['qty']) {
                         throw new \Exception("Qty Stock Reject tidak cukup untuk Item {$itemData['itemCode']}. Tersedia: {$stock->qty_on_hand}");
                    }

                    $uom = $itemData['uom'] ?? $material->satuan;
                    
                    \App\Models\ReturnItem::create([
                        'return_id' => $returnModel->id,
                        'material_id' => $material->id, // INI RELASINYA
                        'batch_lot' => $itemData['lotBatch'],
                        'qty_return' => $itemData['qty'],
                        'uom' => $uom,
                        'return_reason' => $itemData['reason'],
                        'stock_deducted' => true, 
                        'item_condition' => 'Rejected',
                    ]);

                    $movementNumber = $this->generateMovementNumber();
                    StockMovement::create([
                        'movement_number' => $movementNumber,
                        'movement_type' => 'RETURN_REJECTED',
                        'material_id' => $material->id,
                        'batch_lot' => $itemData['lotBatch'],
                        'from_warehouse_id' => $stock->warehouse_id,
                        'from_bin_id' => $stock->bin_id,
                        'to_warehouse_id' => null, 
                        'to_bin_id' => null, 
                        'qty' => $itemData['qty'] * -1,
                        'uom' => $uom,
                        'reference_type' => 'return_model',
                        'reference_id' => $returnModel->id,
                        'movement_date' => now(),
                        'executed_by' => Auth::id(),
                        'notes' => "Return Rejected Material to Supplier. Ref: " . $validated['shipmentNo'],
                    ]);

                    if ($stock->qty_on_hand <= $itemData['qty']) {
                        $stock->delete();
                    } else {
                        $stock->decrement('qty_on_hand', $itemData['qty']);
                        $stock->decrement('qty_available', $itemData['qty']);
                    }

                    $this->logActivity($returnModel, 'Return Rejected Material', [
                        'description' => "Return {$itemData['qty']} {$uom} of {$material->nama_material} (REJECTED) to Supplier.",
                        'material_id' => $material->id,
                        'batch_lot' => $itemData['lotBatch'],
                        'reference_document' => $validated['shipmentNo'],
                    ]);
                }

                DB::commit();
                return redirect()->back()->with('success', 'Return material reject ke supplier berhasil.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Return Store Error: " . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal menyimpan return: ' . $e->getMessage());
            }
        }

        // 2. Handle Production Return
        elseif ($request->input('type') === 'Production') {
            // Validasi tanpa 'exists' untuk itemCode agar bisa ditangkap manual
            $validated = $request->validate([
                'type' => 'required|in:Production',
                'date' => 'required|date',
                'shipmentNo' => 'required|string',
                'items' => 'required|array',
                'items.*.itemCode' => 'required', // HAPUS 'exists:materials,kode_item'
                'items.*.qty' => 'required|numeric|min:0.01',
                'items.*.uom' => 'nullable|string',
                'items.*.reason' => 'required|string',
            ]);

            DB::beginTransaction();
            try {
                // UPDATE: return_number diambil dari shipmentNo
                $returnModel = \App\Models\ReturnModel::create([
                    'return_number' => $validated['shipmentNo'],
                    'return_type' => 'Production',
                    'return_date' => $validated['date'],
                    'department' => Auth::user()->departement,
                    'reference_number' => $validated['shipmentNo'],
                    'status' => 'Pending Approval',
                    'created_by' => Auth::id(),
                ]);

                foreach ($request->items as $index => $itemData) {
                    // Cek Manual Material (Relasi ke Master Data)
                    $material = \App\Models\Material::where('kode_item', $itemData['itemCode'])->first();
                    
                    if (!$material) {
                         // Pesan error spesifik jika kode item tidak ada di database
                         throw new \Exception("Material dengan Kode '{$itemData['itemCode']}' (Baris ke-" . ($index + 1) . ") tidak ditemukan di Database. Silakan cek Master Material.");
                    }

                    $uom = $itemData['uom'] ?? $material->satuan;

                    \App\Models\ReturnItem::create([
                        'return_id' => $returnModel->id,
                        'material_id' => $material->id, // INI RELASINYA
                        'batch_lot' => $itemData['lotBatch'],
                        'qty_return' => $itemData['qty'],
                        'uom' => $uom,
                        'return_reason' => $itemData['reason'],
                        'stock_deducted' => false, 
                        'item_condition' => 'Good',
                    ]);
                    
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
                return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
            }
        }

        // 3. Fallback / Legacy Supplier Return
        else {
             if ($request->has('return_slip_id')) {
                $validated = $request->validate([
                    'return_slip_id' => 'required|exists:return_slips,id',
                    'return_date' => 'required|date',
                    'notes' => 'nullable|string',
                ]);

                DB::beginTransaction();
                try {
                    $returnSlip = \App\Models\ReturnSlip::findOrFail($validated['return_slip_id']);

                    $returnModel = \App\Models\ReturnModel::create([
                        'return_slip_id' => $returnSlip->id,
                        'return_date' => $validated['return_date'],
                        'status' => 'Returned',
                        'notes' => $validated['notes'],
                        'returned_by' => Auth::id(),
                    ]);

                    $movementNumber = $this->generateMovementNumber();
                    StockMovement::create([
                        'movement_number' => $movementNumber,
                        'movement_type' => 'RETURN',
                        'material_id' => $returnSlip->material_id,
                        'batch_lot' => $returnSlip->batch_lot,
                        'from_warehouse_id' => null, 
                        'from_bin_id' => null,
                        'to_warehouse_id' => null,
                        'to_bin_id' => null,
                        'qty' => $returnSlip->qty_return,
                        'uom' => $returnSlip->uom,
                        'reference_type' => 'return_slip',
                        'reference_id' => $returnSlip->id,
                        'movement_date' => now(),
                        'executed_by' => Auth::id(),
                        'notes' => "Return to supplier for slip {$returnSlip->return_number}",
                    ]);

                    $this->logActivity($returnModel, 'Create Return', [
                        'description' => "Returned {$returnSlip->qty_return} {$returnSlip->uom} of {$returnSlip->material->nama_material} to supplier.",
                        'material_id' => $returnSlip->material_id,
                        'batch_lot' => $returnSlip->batch_lot,
                        'qty_after' => $returnSlip->qty_return,
                        'reference_document' => $returnSlip->return_number,
                    ]);

                    $returnSlip->update(['status' => 'Returned']);
                    DB::commit();
                    return redirect()->back()->with('success', 'Return successful.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Return failed: ' . $e->getMessage());
                }
             } else {
                 return redirect()->back()->with('error', 'Tipe Return tidak valid.');
             }
        }
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'return_id' => 'required|exists:returns,id',
            'target_bin_id' => 'required', 
        ]);

        DB::beginTransaction();
        try {
            $returnModel = \App\Models\ReturnModel::with('items.material')->findOrFail($validated['return_id']);

            if ($returnModel->status !== 'Pending Approval') {
                throw new \Exception("Return status is not Pending Approval.");
            }

            $targetBin = \App\Models\WarehouseBin::where('bin_code', $validated['target_bin_id'])->first();
            if (!$targetBin) {
                throw new \Exception("Bin '{$validated['target_bin_id']}' tidak ditemukan.");
            }

            foreach ($returnModel->items as $item) {
                $stock = \App\Models\InventoryStock::where('material_id', $item->material_id)
                    ->where('bin_id', $targetBin->id)
                    ->where('batch_lot', $item->batch_lot)
                    ->where('status', 'KARANTINA') 
                    ->first();

                if ($stock) {
                    $stock->increment('qty_on_hand', $item->qty_return);
                    $stock->increment('qty_available', $item->qty_return);
                } else {
                    $refStock = \App\Models\InventoryStock::where('material_id', $item->material_id)
                        ->where('batch_lot', $item->batch_lot)
                        ->whereNotNull('exp_date')
                        ->first();
                        
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
                        'status' => 'RELEASED', 
                        'exp_date' => $expDate, 
                        'last_movement_date' => now(),
                    ]);
                }

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
    
    public function getDeptReservations()
    {
        $user = Auth::user();
        if (!$user || !$user->departement) {
            return response()->json([]);
        }
        $reservations = \App\Models\ReservationRequest::query()
            ->whereIn('status', ['Completed', 'Short-Pick'])
            ->where(function ($query) use ($user) {
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
        return response()->json($reservations);
    }

    public function getReservationDetails(Request $request)
    {
        $reservationNo = $request->input('no');
        if (!$reservationNo) return response()->json(['error' => 'Reservation Number required'], 400);

        $allReservationItems = \App\Models\Reservation::with('material')
            ->where('reservation_no', $reservationNo)
            ->get();

        $returnedMaterialBatches = \App\Models\ReturnItem::whereHas('return', function ($query) use ($reservationNo) {
                $query->where('reference_number', $reservationNo)
                      ->whereIn('status', ['Approved', 'Returned']);
            })
            ->select('material_id', 'batch_lot')
            ->get()
            ->map(function ($item) {
                return $item->material_id . '|' . $item->batch_lot;
            })
            ->toArray();

        $items = $allReservationItems->filter(function ($item) use ($returnedMaterialBatches) {
                $compositeKey = $item->material_id . '|' . $item->batch_lot;
                return !in_array($compositeKey, $returnedMaterialBatches);
            })
            ->values()
            ->map(function ($item) {
                return [
                    'item_code' => $item->material->kode_item,
                    'item_name' => $item->material->nama_material,
                    'batch_lot' => $item->batch_lot,
                    'original_qty' => $item->qty_reserved,
                    'qty' => 0,
                    'uom' => $item->uom ?? $item->material->satuan,
                    'category' => $item->material->kategori,
                ];
            });
        return response()->json($items);
    }

    public function getRejectedShipmentDetails(Request $request)
    {
        $shipmentNo = $request->input('no');
        if (!$shipmentNo) return response()->json(['error' => 'Shipment Number required'], 400);

        $incomingGood = \App\Models\IncomingGood::where('incoming_number', $shipmentNo)->first();
        if (!$incomingGood) return response()->json(['error' => 'Shipment Number not found'], 404);

        $items = \App\Models\InventoryStock::with('material')
            ->where('gr_id', $incomingGood->id)
            ->where('status', 'REJECTED')
            ->whereHas('bin', function ($q) {
                $q->where('bin_code', 'NOT LIKE', 'QRT-%');
            })
            ->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'item_code' => $stock->material->kode_item,
                    'item_name' => $stock->material->nama_material,
                    'batch_lot' => $stock->batch_lot,
                    'on_hand_qty' => $stock->qty_on_hand,
                    'qty' => $stock->qty_on_hand,
                    'uom' => $stock->uom,
                    'supplier_id' => $stock->material->supplier_id,
                    'supplier_name' => $stock->material->supplier->nama_supplier ?? 'N/A',
                    'category' => $stock->material->kategori,
                ];
            });
        return response()->json($items);
    }

    public function getSupplierShipmentDetails(Request $request)
    {
        $shipmentNo = $request->input('no');
        if (!$shipmentNo) return response()->json(['error' => 'Shipment Number required'], 400);

        $incomingGood = \App\Models\IncomingGood::where('incoming_number', $shipmentNo)->first();
        if (!$incomingGood) return response()->json(['error' => 'Shipment Number not found'], 404);

        $items = \App\Models\InventoryStock::with(['material', 'material.supplier'])
            ->where('gr_id', $incomingGood->id)
            ->where('status', 'REJECTED')
            ->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'item_code' => $stock->material->kode_item,
                    'item_name' => $stock->material->nama_material,
                    'batch_lot' => $stock->batch_lot,
                    'on_hand_qty' => $stock->qty_on_hand,
                    'qty' => $stock->qty_on_hand,
                    'uom' => $stock->uom,
                    'supplier_id' => $stock->material->supplier_id,
                    'supplier_name' => $stock->material->supplier->nama_supplier ?? 'N/A',
                    'category' => $stock->material->kategori,
                ];
            });
        return response()->json($items);
    }

    public function show(string $id) {}
    public function edit(string $id) {}
    
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'nullable|exists:return_items,id', // Make ID nullable
            'items.*.item_code' => 'nullable|string', // Allow item_code for fallback matching
            'items.*.batch_lot' => 'nullable|string', // Allow batch_lot for fallback matching
            'items.*.qty' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $returnModel = \App\Models\ReturnModel::with('items.material')->findOrFail($id);

            // Check if return can be edited (only Pending Approval or Draft)
            if (!in_array($returnModel->status, ['Pending Approval', 'Draft', 'Submitted'])) {
                throw new \Exception("Return dengan status '{$returnModel->status}' tidak dapat diedit.");
            }

            // Update each item's quantity
            foreach ($validated['items'] as $itemData) {
                $returnItem = null;
                
                // Try to find by ID first
                if (!empty($itemData['id'])) {
                    $returnItem = \App\Models\ReturnItem::find($itemData['id']);
                }
                
                // Fallback: find by item_code and batch_lot within this return
                if (!$returnItem && !empty($itemData['item_code']) && !empty($itemData['batch_lot'])) {
                    $returnItem = $returnModel->items->first(function($item) use ($itemData) {
                        $materialCode = $item->material->kode_item ?? null;
                        return $materialCode === $itemData['item_code'] 
                            && $item->batch_lot === $itemData['batch_lot'];
                    });
                }
                
                if (!$returnItem) {
                    throw new \Exception("Item tidak ditemukan untuk update.");
                }
                
                // Verify this item belongs to this return
                if ($returnItem->return_id != $returnModel->id) {
                    throw new \Exception("Item tidak valid untuk return ini.");
                }

                $oldQty = $returnItem->qty_return;
                $newQty = $itemData['qty'];

                // Update quantity
                $returnItem->update([
                    'qty_return' => $newQty
                ]);

                // Log the change
                $this->logActivity($returnModel, 'Return Quantity Edited', [
                    'description' => "Qty updated from {$oldQty} to {$newQty} for material ID {$returnItem->material_id}",
                    'material_id' => $returnItem->material_id,
                    'batch_lot' => $returnItem->batch_lot,
                    'old_qty' => $oldQty,
                    'new_qty' => $newQty,
                    'edited_by' => Auth::user()->name
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Return berhasil diupdate.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Return Update Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy(string $id) {}
}