<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\IncomingGood;
use App\Models\IncomingGoodsItem;
use App\Models\InventoryStock;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Material;
use App\Models\WarehouseBin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Smalot\PdfParser\Parser;
use App\Traits\ActivityLogger;

class GoodsReceiptController extends Controller
{
    use ActivityLogger;
    public function index(Request $request)
    {
        $query = IncomingGood::with([
            'purchaseOrder',
            'supplier',
            'items.material',
            'receiver'
        ])
        ->orderBy('created_at', 'desc');

        // Filter: Search (No Incoming, No PO, No SJ, No Kendaraan, Nama Driver, Material Name)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('incoming_number', 'LIKE', "%{$search}%")
                  ->orWhere('no_surat_jalan', 'LIKE', "%{$search}%")
                  ->orWhere('no_kendaraan', 'LIKE', "%{$search}%")
                  ->orWhere('nama_driver', 'LIKE', "%{$search}%")
                  // Search PO number from relationship (not po_id)
                  ->orWhereHas('purchaseOrder', function($subQ) use ($search) {
                      $subQ->where('no_po', 'LIKE', "%{$search}%");
                  })
                  // Search material names from items
                  ->orWhereHas('items.material', function($subQ) use ($search) {
                      $subQ->where('nama_material', 'LIKE', "%{$search}%")
                           ->orWhere('kode_item', 'LIKE', "%{$search}%");
                  });
            });
        }

        // // Filter: Supplier (REMOVED as per request)
        // if ($request->has('supplier_id') && $request->supplier_id != '') {
        //     $query->where('supplier_id', $request->supplier_id);
        // }

        // Filter: Date Range
        if ($request->has('date_start') && $request->date_start != '') {
            $query->whereDate('tanggal_terima', '>=', $request->date_start);
        }
        if ($request->has('date_end') && $request->date_end != '') {
            $query->whereDate('tanggal_terima', '<=', $request->date_end);
        }

        // Pagination with Dynamic Limit (Show Entries)
        $limit = $request->input('limit', 10); // Default 10
        // Jika limit 'all', kita bisa set ke angka besar atau handle khusus. 
        // Namun paginate() butuh integer. Kita set 1000 jika 'all' atau user kirim angka besar.
        if ($limit === 'all') {
            $limit = 1000;
        }

        $incomingGoods = $query->paginate($limit)
            ->withQueryString()
            ->through(function ($incoming) {
                // 1. Ambil semua item
                $items = $incoming->items->map(function ($item) {
                    return [
                        'kodeItem' => $item->material->kode_item ?? '',
                        'namaMaterial' => $item->material->nama_material ?? '',
                        'satuanMaterial' => $item->material->satuan ?? '',
                        'uom' => $item->material->satuan ?? '', // UoM for display in table
                        'batchLot' => $item->batch_lot,
                        'expDate' => $item->exp_date,
                        'qtyWadah' => $item->qty_wadah,
                        'qtyUnit' => $item->qty_unit,
                        'kondisiBaik' => $item->kondisi_baik,
                        'kondisiTidakBaik' => $item->kondisi_tidak_baik,
                        'coaAda' => $item->coa_ada,
                        'coaTidakAda' => $item->coa_tidak_ada,
                        'labelMfgAda' => $item->label_mfg_ada,
                        'labelMfgTidakAda' => $item->label_mfg_tidak_ada,
                        'labelCoaSesuai' => $item->label_coa_sesuai,
                        'labelCoaTidakSesuai' => $item->label_coa_tidak_sesuai,
                        'pabrikPembuat' => $item->pabrik_pembuat,
                        'statusQC' => $item->status_qc,
                        'binTarget' => $item->bin_target,
                        'isHalal' => $item->is_halal,
                        'isNonHalal' => $item->is_non_halal,
                        'qrCode' => $item->qr_code,
                    ];
                });

                // 2. LOGIKA PENENTUAN STATUS GR BARU
                $isStillToQC = $items->contains(fn($item) => $item['statusQC'] === 'To QC');
                $finalStatus = $isStillToQC ? 'Proses' : 'Selesai';
                
                return [
                    'id' => $incoming->id,
                    'incomingNumber' => $incoming->incoming_number,
                    'noPo' => $incoming->purchaseOrder->no_po ?? '',
                    'noSuratJalan' => $incoming->no_surat_jalan,
                    'supplier' => $incoming->supplier->nama_supplier ?? '',
                    'tanggalTerima' => $incoming->tanggal_terima,
                    'noKendaraan' => $incoming->no_kendaraan,
                    'namaDriver' => $incoming->nama_driver,
                    'kategori' => $incoming->kategori,
                    'status' => $finalStatus, 
                    'items' => $items,
                ];
            });

        $suppliers = Supplier::where('status', 'active')
            ->orderBy('nama_supplier')
            ->get(['id', 'kode_supplier', 'nama_supplier']);

        $materials = Material::where('status', 'active')
            ->orderBy('kode_item')
            ->get()
            ->map(function ($material) {
                return [
                    'id' => $material->id,
                    'code' => $material->kode_item,
                    'name' => $material->nama_material,
                    'unit' => $material->satuan,
                    'mfg' => $material->defaultSupplier->nama_supplier ?? '',
                    'qcRequired' => $material->qc_required,
                    'kategori' => $material->kategori,
                    'subCategory' => $material->subkategori,
                    'halalStatus' => $material->halal_status,
                ];
            });

        return Inertia::render('PenerimaanBarang', [
            'shipments' => $incomingGoods,
            'suppliers' => $suppliers,
            'materials' => $materials,
            'filters' => $request->only(['search', 'date_start', 'date_end', 'limit']), // Send filters back for UI state
        ]);
    }

    public function parseErpPdf(Request $request)
    {
        $request->validate([
            'erp_pdf' => 'required|mimes:pdf|max:5120',
        ]);
        
        $pdfFile = $request->file('erp_pdf');

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfFile->getPathname());
            $rawText = $pdf->getText();
            
            
            // STEP 1: NORMALISASI TEKS YANG LEBIH AGRESIF
            // Hapus semua newline, tab, dan multiple spaces menjadi SATU spasi
            $text = preg_replace('/[\r\n\t]+/', ' ', $rawText);
            $text = preg_replace('/\s{2,}/', ' ', $text); // Multiple spaces -> single space
            $text = trim($text);
            
            // STEP 1.5: CLEANUP SERIAL NUMBERS YANG TERPECAH
            // Fix serial numbers yang terpecah karena line break di PDF
            
            // Pattern 1: "28.D.JY2025010- 6" -> "28.D.JY2025010-6"
            // [angka/huruf][-/][spasi][angka/huruf] -> hapus spasi
            $text = preg_replace('/([A-Z0-9])([.\-\/])\s+([A-Z0-9])/i', '$1$2$3', $text);
            
            // Pattern 2: "JY2025010 6" -> "JY20250106"
            // [2huruf][6+angka][spasi][1-3angka] -> gabung
            $text = preg_replace('/([A-Z]{2}\d{6,})\s+(\d{1,3})\b/i', '$1$2', $text);
            
            // Pattern 3: "28.D.JY2025010 6" -> "28.D.JY2025010-6" (serial dengan titik yang terpecah)
            // Format: XX.X.XXXXXXX [spasi] angka -> tambahkan dash
            $text = preg_replace('/(\d{2,3}\.[A-Z]\.[A-Z0-9]+)\s+(\d{1,2})\b/i', '$1-$2', $text);
            
            // STEP 1.6: FIX SPLIT QUANTITIES
            // Handle quantities yang terpecah karena newline
            // Contoh: "12. 320,0000" -> "12.320,0000"
            $text = preg_replace('/([\d]+)\.\s+(\d{3},\d+)/', '$1.$2', $text);
            
            
            
            \Log::info('PDF Normalized Text:', [
                'length' => strlen($text),
                'preview' => substr($text, 0, 800)
            ]);

            $extractedData = [
                'incoming_number' => '',
                'no_surat_jalan' => 'N/A', // Default jika tidak ada
                'no_po' => '',
                'truck_number' => '',
                'driver_name' => '',
                'date' => '',
                'items' => [],
                'supplier_name' => '',
                'supplier_code' => '',
            ];
            
            // STEP 2: EXTRACT INCOMING NUMBER
            // Pattern: IN/angka5digit (misal: IN/27866)
            if (preg_match('/\b(IN\/\d{5})\b/i', $text, $matches)) {
                $extractedData['incoming_number'] = trim($matches[1]);
                
                // Cek duplikasi
                if (IncomingGood::where('incoming_number', $extractedData['incoming_number'])->exists()) {
                    return response()->json([
                        'error' => "Nomor {$extractedData['incoming_number']} sudah ada dalam sistem."
                    ], 422);
                }
            }
            
            // STEP 3: EXTRACT NO PO (ANCHOR UTAMA)
            // Pattern: PO diikuti angka (PO66666, PO54196, dll)
            if (preg_match('/\b(PO\d+)\b/i', $text, $poMatch)) {
                $extractedData['no_po'] = $poMatch[1];
                
                // EXTRACT NO SURAT JALAN (SEBELUM NO PO)
                // Strategi: Cari string alfanumerik dengan slash/strip SEBELUM PO
                // Hindari kata kunci seperti "Supplier", "Order", "Invoice"
                $blacklistWords = [
                    'Supplier', 'Invoice', 'Control', 'Stock', 'Journal', 
                    'Truck', 'Driver', 'Purchase', 'Order', 'Creation', 
                    'Date', 'Scheduled', 'Time', 'Source', 'Document',
                    'To', 'Be', 'Invoiced', 'Physical', 'Locations'
                ];
                
                // Ambil 100 karakter sebelum PO untuk mencari kandidat No SJ
                $poPosition = strpos($text, $poMatch[1]);
                $textBeforePO = substr($text, max(0, $poPosition - 100), 100);
                
                // Pattern: Kata terakhir sebelum PO (bisa punya slash, strip, titik)
                if (preg_match('/([A-Z0-9\/\.\-]{3,30})\s+' . preg_quote($poMatch[1], '/') . '/i', $text, $sjMatch)) {
                    $candidate = trim($sjMatch[1]);
                    
                    // Filter: Pastikan bukan header/label
                    if (!in_array($candidate, $blacklistWords) && 
                        strlen($candidate) >= 3 &&
                        !preg_match('/^(Supplier|Order|Document|Source)$/i', $candidate)) {
                        
                        $extractedData['no_surat_jalan'] = $candidate;
                    }
                }
            }


            // STEP 4: EXTRACT SUPPLIER NAME (with multiple fallback patterns)
            // Try multiple patterns in order of specificity
            
            // Pattern 1 (Most Specific): "Supplier [NAME] Invoice|Stock|Truck|PO"
            // Contoh: "Supplier Bahtera Adi Jaya, PT Invoice" -> "Bahtera Adi Jaya, PT"
            if (preg_match('/Supplier\s+([A-Za-z0-9\s,\.]+?)(?=\s+Invoice|\s+Stock|\s+Truck|\s+PO)/i', $text, $supplierMatch)) {
                $extractedData['supplier_name'] = trim($supplierMatch[1]);
            }
            // Pattern 2 (Fallback): "Supplier Address : [NAME]" (format internal PDF)
            elseif (preg_match('/Supplier\s+Address\s*:\s*([^\n]+?)(?=\s+Contact|\s+Incoming|$)/i', $text, $supplierMatch)) {
                $extractedData['supplier_name'] = trim($supplierMatch[1]);
            }
            // Pattern 3 (Fallback): "Supplier [NAME] Back Order|Invoice Control"
            elseif (preg_match('/Supplier\s+([A-Za-z0-9\s,\.]+?)(?=\s+Back\s+Order|\s+Invoice\s+Control)/i', $text, $supplierMatch)) {
                $extractedData['supplier_name'] = trim($supplierMatch[1]);
            }
            // Pattern 4 (Last Resort): Ambil apa saja setelah "Supplier" sampai whitespace besar
            elseif (preg_match('/Supplier\s+([A-Za-z0-9\s,\.]{5,50}?)(?=\s{2,}|\n)/i', $text, $supplierMatch)) {
                $extractedData['supplier_name'] = trim($supplierMatch[1]);
            }
            
            
            // STEP 5: EXTRACT TRUCK NUMBER & DRIVER NAME
            // Support 2 formats:
            // 1. PDF Internal: "No Truck Driver Name F 8013 MC INDRAWAN"
            // 2. PDF Odoo ERP: "Truck Number Driver Name" (tanpa value)
            
            // Try Format 1: "No Truck" pattern (PDF Internal Gondowangi)
            // Pattern: No Truck [VALUE] Driver Name
            if (preg_match('/No\s+Truck\s+(.+?)(?=\s+Driver\s+Name)/i', $text, $truckMatch)) {
                $candidate = trim($truckMatch[1]);
                // Filter: Skip jika candidate adalah bagian dari SJ/PO number
                if (strlen($candidate) > 0 && 
                    !preg_match('/^(Driver|Purchase|Order|Creation|PO\d+|IN\/)/i', $candidate)) {
                    $extractedData['truck_number'] = $candidate;
                }
            }
            // Fallback Format 2: "Truck Number" pattern (PDF Odoo ERP)
            elseif (preg_match('/Truck\s+Number\s+(.+?)(?=\s+Driver)/i', $text, $truckMatch)) {
                $candidate = trim($truckMatch[1]);
                if (strlen($candidate) > 0 && 
                    !preg_match('/^(Driver|Purchase|Creation|Scheduled)/i', $candidate)) {
                    $extractedData['truck_number'] = $candidate;
                }
            }
            
            // Try Format 1: Extract driver name dari "Driver Name [VALUE] Purchase/Order"
            if (preg_match('/Driver\s+Name\s+(.+?)(?=\s+(?:Purchase|Order|PO\d+|Incoming))/i', $text, $driverMatch)) {
                $candidate = trim($driverMatch[1]);
                if (strlen($candidate) > 0 && 
                    !preg_match('/^(Purchase|Creation|Scheduled|Stock|Order|Incoming|PO\d+)/i', $candidate)) {
                    $extractedData['driver_name'] = $candidate;
                }
            }
            // Fallback Format 2: Pattern lama untuk compatibility
            elseif (preg_match('/Driver\s+Name\s+(.+?)(?=\s+Purchase|Creation|Scheduled)/i', $text, $driverMatch)) {
                $candidate = trim($driverMatch[1]);
                if (strlen($candidate) > 0 && 
                    !preg_match('/^(Purchase|Creation|Scheduled|Stock)/i', $candidate)) {
                    $extractedData['driver_name'] = $candidate;
                }
            }

            // STEP 6: EXTRACT DATE (CREATION DATE)
            // Pattern: DD/MM/YYYY HH:MM:SS
            if (preg_match('/(\d{2}\/\d{2}\/\d{4})\s+(\d{2}:\d{2}:\d{2})/i', $text, $dateMatch)) {
                $extractedData['date'] = trim("{$dateMatch[1]} {$dateMatch[2]}");
            }
            
            // STEP 7: EXTRACT ITEMS (PALING KRUSIAL - IMPROVED LOGIC)
            $extractedData['items'] = $this->extractItems($text);
            
            \Log::info('PDF Extracted Items:', [
                'count' => count($extractedData['items']),
                'items' => $extractedData['items']
            ]);

            // STEP 8: FINALISASI
            // Format tanggal untuk input datetime-local
            if (!empty($extractedData['date'])) {
                try {
                    $dateObj = \DateTime::createFromFormat('d/m/Y H:i:s', $extractedData['date']);
                    if (!$dateObj) {
                        $dateObj = \DateTime::createFromFormat('d/m/Y', $extractedData['date']);
                    }
                    $extractedData['tanggal_terima'] = $dateObj ? $dateObj->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
                } catch (\Exception $e) {
                    $extractedData['tanggal_terima'] = now()->format('Y-m-d\TH:i');
                }
            } else {
                $extractedData['tanggal_terima'] = now()->format('Y-m-d\TH:i');
            }

            // Lookup Supplier di database
            if (!empty($extractedData['supplier_name'])) {
                $searchName = explode(',', $extractedData['supplier_name'])[0];
                $supplier = Supplier::where('nama_supplier', 'LIKE', '%' . trim($searchName) . '%')->first();
                
                if ($supplier) {
                    $extractedData['supplier_name'] = $supplier->nama_supplier;
                    $extractedData['supplier_code'] = $supplier->kode_supplier;
                }
            }

            // STEP 9: DETECT MULTIPLE ITEM CODES (WIZARD MODE)
            $uniqueItemCodes = array_unique(array_column($extractedData['items'], 'kode_material'));
            $hasMultipleItemCodes = count($uniqueItemCodes) > 1;
            
            $extractedData['has_multiple_item_codes'] = $hasMultipleItemCodes;
            $extractedData['unique_item_codes'] = array_values($uniqueItemCodes);
            
            if ($hasMultipleItemCodes) {
                $extractedData['items_grouped_by_code'] = $this->groupItemsByCode($extractedData['items']);
            }

            // STEP 10: TRACK AUTO-FILLED FIELDS (for frontend visual indicators)
            $autoFilledFields = [];
            if (!empty($extractedData['incoming_number'])) $autoFilledFields[] = 'incoming_number';
            if (!empty($extractedData['no_surat_jalan'])) $autoFilledFields[] = 'no_surat_jalan';
            if (!empty($extractedData['no_po'])) $autoFilledFields[] = 'no_po';
            if (!empty($extractedData['supplier_name'])) $autoFilledFields[] = 'supplier';
            if (!empty($extractedData['truck_number'])) $autoFilledFields[] = 'truck';
            if (!empty($extractedData['driver_name'])) $autoFilledFields[] = 'driver';
            if (!empty($extractedData['date'])) $autoFilledFields[] = 'date';
            if (count($extractedData['items']) > 0) $autoFilledFields[] = 'items';
            
            $extractedData['auto_filled_fields'] = $autoFilledFields;

            return response()->json($extractedData);

        } catch (\Exception $e) {
            \Log::error('PDF Parse Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Gagal memproses PDF.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    private function extractItems($text)
    {
        $items = [];
        
        // STRATEGY 1: Extract semua serial numbers dulu dengan pattern yang lebih fleksibel
        // Format yang didukung:
        // - 27.A.250028 (angka.huruf.angka)
        // - 27.K.KIA121PU1B (angka.huruf.alfanumerik)
        // - 30.B.AB0511 (angka.huruf.alfanumerik)
        // - 26.K.0029792187 (angka.huruf.angka panjang)
        // - 50003031125 (angka saja, 11+ digit)
        $serialNumbers = [];
        
        $serialPatterns = [
            // Pattern 1: XX.X.XXXXXX (titik sebagai separator, bisa huruf & angka)
            '/\b(\d{2,3}\.[A-Z]\.[A-Z0-9]{6,})\b/i',
            
            // Pattern 2: Angka panjang tanpa titik (min 10 digit)
            '/\b(\d{10,})\b/',
            
            // Pattern 3: Alfanumerik tanpa titik (min 8 karakter, harus ada angka DAN huruf)
            // Contoh: 20008291225NP, ABC123456789, etc.
            '/\b([0-9]{5,}[A-Z]{1,}[A-Z0-9]*|[A-Z]{1,}[0-9]{5,}[A-Z0-9]*)\b/i',
        ];
        
        foreach ($serialPatterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[1] as $serial) {
                    // Filter: Skip jika ini adalah angka quantity (biasanya ada koma/titik desimal)
                    if (!preg_match('/[\d,\.]{4,}\s+(Kg|Pcs|Ltr)/i', $serial)) {
                        $serialNumbers[] = trim($serial);
                    }
                }
            }
        }
        
        
        // Deduplicate serial numbers
        $serialNumbers = array_unique($serialNumbers);
        $serialNumbers = array_values($serialNumbers); // Re-index
        
        // BLACKLIST FILTER: Remove non-serial patterns
        // Filter out PO numbers, IN numbers, dates, quantities, etc.
        $serialNumbers = array_filter($serialNumbers, function($serial) {
            // Blacklist Pattern 1: PO numbers (PO66576, PO12345, etc.)
            if (preg_match('/^PO\d+$/i', $serial)) {
                \Log::info("Filtered out PO number: {$serial}");
                return false;
            }
            
            // Blacklist Pattern 2: IN numbers (IN/27866, IN27866, etc.)
            if (preg_match('/^IN[\\/]?\d+$/i', $serial)) {
                \Log::info("Filtered out IN number: {$serial}");
                return false;
            }
            
            // Blacklist Pattern 3: Pure dates (20251230, 20250115, etc. - 8 digit dates)
            if (preg_match('/^\d{8}$/', $serial)) {
                \Log::info("Filtered out date: {$serial}");
                return false;
            }
            
            // Blacklist Pattern 4: Quantities with decimal (12320,0000, 2720,00, etc.)
            if (preg_match('/,\d{2,}/', $serial)) {
                \Log::info("Filtered out quantity: {$serial}");
                return false;
            }
            
            // Blacklist Pattern 5: Source Document pattern (similar to PO)
            if (preg_match('/^(Source|Document|SJ)\d*/i', $serial)) {
                \Log::info("Filtered out document keyword: {$serial}");
                return false;
            }
            
            return true; // Keep this serial number
        });
        
        // Re-index after filtering
        $serialNumbers = array_values($serialNumbers);
        
        // POST-PROCESSING: Fix incomplete serials (extracted before cleanup)
        // Example: "28.D.JY2025010" in array but "28.D.JY2025010-6" in text
        foreach ($serialNumbers as $index => $serial) {
            // If serial match pattern XX.X.XXXXXXX (without suffix), search for complete version
            if (preg_match('/^(\d{2,3}\.[A-Z]\.[A-Z0-9]+)$/i', $serial)) {
                // Search in cleaned text for this serial + possibly more chars
                if (preg_match('/' . preg_quote($serial, '/') . '(-\d{1,2})\b/i', $text, $match)) {
                    // Found complete version! Replace
                    $serialNumbers[$index] = $serial . $match[1];
                    \Log::info("Serial completed: {$serial} → {$serialNumbers[$index]}");
                }
            }
        }
        
        \Log::info('Found Serial Numbers:', [
            'count' => count($serialNumbers),
            'serials' => $serialNumbers
        ]);
        
        // STRATEGY 2: Pattern untuk material items
        // Format umum: [KODE] Nama Qty UoM UoM
        // Support multiple UoM: Kg, Pcs, Ltr, Box, Rol
        // Contoh:
        // - [60006] Ms 1000 2.720,0000 Kg Kg
        // - [20008] Botol 140 ml 12.320,0000 Pcs Pcs
        // - [14063] Propylene Glycol USP 645,0000 Kg Kg
        
        // Pattern FLEKSIBEL untuk menangkap berbagai format:
        // CRITICAL: PDF tidak selalu punya "Kg Kg" atau "Pcs Pcs"
        // Kadang format nya: [CODE] Description 140 ml 12.320,000
        // Strategy: Ambil semua text sampai bracket berikutnya, extract largest qty number
        $patterns = [
            // Pattern 1: [kode] ... qty UoM UoM Serial (format lengkap dengan double UoM)
            '/\[(\d+)\]\s+(.+?)\s+([\d\.,]+)\s+(Pcs|Kg|Ltr|Box|Rol)\s+\4\s+([^\s]+)?/i',
            
            // Pattern 2: [kode] Description ... LargeQty (ambil sampai bracket berikutnya)
            // Akan di-process manual untuk extract qty yang benar
            '/\[(\d+)\]\s+([^\[]+?)(?=\[|$)/i',
        ];
        
        foreach ($patterns as $patternIndex => $pattern) {
            if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
                $itemIndex = 0;
                
                foreach ($matches as $match) {
                    // Declare variables to be used in common validation and itemData
                    $quantity = 0;
                    $description = '';
                    $serialNumber = '';

                    // SPECIAL HANDLING untuk Pattern 2 (fallback - full text capture)
                    if ($patternIndex === 1) {
                        // Pattern 2: $match[1] = kode, $match[2] = full text
                        $fullText = $match[2];
                        
                        // Extract ALL numbers dengan format qty (dengan koma/titik)
                        // Contoh: "Botol 140 ml 12.320,000" -> ["140", "12.320,000"]
                        preg_match_all('/([\d]{1,}[\.,][\d,]+)/', $fullText, $qtyMatches);
                        
                        if (empty($qtyMatches[1])) {
                            continue; // Skip jika no qty found
                        }
                        
                        // Pilih yang TERBESAR (untuk handle "140 ml 12.320,000")
                        $quantities = [];
                        foreach ($qtyMatches[1] as $qtyStr) {
                            $normalized = str_replace('.', '', $qtyStr); // Remove ribuan separator
                            $normalized = str_replace(',', '.', $normalized); // Koma -> decimal
                            $quantities[] = floatval($normalized);
                        }
                        
                        $quantity = max($quantities); // Ambil yang terbesar
                        
                        // Extract description (sampai angka qty pertama)
                        $description = preg_split('/([\d]{1,}[\.,][\d,]+)/', $fullText, 2)[0];
                        $description = preg_replace('/\s+/', ' ', trim($description));
                        
                        // Try find serial number (alfanumerik panjang di akhir)
                        $serialNumber = '';
                        if (preg_match('/([A-Z0-9]{8,})$/i', trim($fullText), $serialMatch)) {
                            $serialNumber = $serialMatch[1];
                        } elseif (isset($serialNumbers[$itemIndex])) {
                            $serialNumber = $serialNumbers[$itemIndex];
                        }
                        
                    } else {
                        // Pattern 1 (normal dengan qty capture group)
                        // Normalisasi quantity (2.720,0000 -> 2720.0000)
                        $qtyString = str_replace('.', '', $match[3]); // Hapus separator ribuan
                        $qtyString = str_replace(',', '.', $qtyString); // Koma -> titik desimal
                        $quantity = floatval($qtyString);
                        
                        // Clean description (hapus extra spaces)
                        $description = preg_replace('/\s+/', ' ', trim($match[2]));
                        
                        // Serial Number dari capture group atau array
                        $serialNumber = '';
                        if (isset($match[5]) && !empty($match[5])) { // Corrected index for serial number
                            $serialNumber = trim($match[5]);
                        } elseif (isset($serialNumbers[$itemIndex])) {
                            // Ambil dari array serial numbers
                            $serialNumber = $serialNumbers[$itemIndex];
                        }
                    }
                    
                    // Common validation
                    if ($quantity <= 0 || empty($description)) {
                        continue;
                    }
                    
                    $itemData = [
                        'kode_material' => trim($match[1]),
                        'description' => $description,
                        'uom' => 'Kg',
                        'quantity' => $quantity,
                        'serial_number' => $serialNumber,
                    ];
                    
                    // Hindari duplikat (cek kode material + quantity yang sama)
                    $isDuplicate = false;
                    foreach ($items as $existingItem) {
                        if ($existingItem['kode_material'] === $itemData['kode_material'] &&
                            abs($existingItem['quantity'] - $itemData['quantity']) < 0.01) {
                            $isDuplicate = true;
                            break;
                        }
                    }
                    
                    if (!$isDuplicate) {
                        $items[] = $itemData;
                        $itemIndex++;
                    }
                }
                
                // Jika pattern ini berhasil, tidak perlu coba pattern lain
                if (count($items) > 0) {
                    \Log::info("Pattern {$patternIndex} berhasil, found " . count($items) . " items");
                    break;
                }
            }
        }
        
        // FALLBACK: Jika tidak ada items yang terdeteksi, coba pattern paling sederhana
        if (empty($items)) {
            // Pattern emergency: [KODE] apa_saja QUANTITY
            if (preg_match_all('/\[(\d+)\]\s+(.+?)\s+([\d\.,]+)/i', $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $idx => $match) {
                    $qtyString = str_replace(['.', ','], ['', '.'], $match[3]);
                    $quantity = floatval($qtyString);
                    
                    if ($quantity > 0) {
                        $items[] = [
                            'kode_material' => trim($match[1]),
                            'description' => trim($match[2]),
                            'uom' => 'Kg',
                            'quantity' => $quantity,
                            'serial_number' => $serialNumbers[$idx] ?? '',
                        ];
                    }
                }
            }
        }
        
        return $items;
    }

    private function groupItemsByCode(array $items): array
    {
        $grouped = [];
        
        foreach ($items as $item) {
            $code = $item['kode_material'];
            
            if (!isset($grouped[$code])) {
                $grouped[$code] = [
                    'code' => $code,
                    'items' => []
                ];
            }
            
            $grouped[$code]['items'][] = $item;
        }
        
        return array_values($grouped);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'noPo' => 'required|string|max:255',
            'noSuratJalan' => 'required|string|max:255',
            'supplier' => 'required|exists:suppliers,id',
            'noKendaraan' => 'required|string|max:50',
            'namaDriver' => 'required|string|max:255',
            'tanggalTerima' => 'required|date',
            'kategori' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.kodeItem' => 'required|exists:materials,id',
            'items.*.batchLot' => 'required|string|max:255',
            'items.*.expDate' => 'nullable|date',
            'items.*.qtyWadah' => 'required|numeric|min:1',
            'items.*.qtyUnit' => 'required|numeric|min:1',
            'items.*.binTarget' => 'required|string|max:255', // Memastikan binTarget divvalidasi
            'items.*.isHalal' => 'nullable|boolean',
            'items.*.isNonHalal' => 'nullable|boolean',
            'items.*.pabrikPembuat' => 'nullable|string|max:255',
            'items.*.kondisiBaik' => 'nullable|boolean',
            'items.*.kondisiTidakBaik' => 'nullable|boolean',
            'items.*.coaAda' => 'nullable|boolean',
            'items.*.coaTidakAda' => 'nullable|boolean',
            'items.*.labelMfgAda' => 'nullable|boolean',
            'items.*.labelMfgTidakAda' => 'nullable|boolean',
            'items.*.labelCoaSesuai' => 'nullable|boolean',
            'items.*.labelCoaTidakSesuai' => 'nullable|boolean',
        ]);

        $incomingNumberFromRequest = $request->input('incomingNumber');
        DB::beginTransaction();
        try {
            // Generate incoming number
            $date = date('Ymd');
            $lastIncoming = IncomingGood::whereDate('created_at', today())->latest()->first();
            $sequence = $lastIncoming ? (intval(substr($lastIncoming->incoming_number, -4)) + 1) : 1;
            
            $incomingNumber = $incomingNumberFromRequest;

            if (empty($incomingNumber)){
                $date = date('Ymd');
                $lastIncoming = IncomingGood::whereDate('created_at', today())->latest()->first();
                $sequence = $lastIncoming ? (intval(substr($lastIncoming->incoming_number, -4)) + 1) : 1;
                $incomingNumber = "IN/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }else {
                if (IncomingGood::where('incoming_number', $incomingNumber)->exists()) {
                    throw new \Exception("Nomor Incoming ERP ({$incomingNumber}) sudah ada dalam sistem.");
                }
            }

            $purchaseOrder = PurchaseOrder::firstOrCreate(
                ['no_po' => $validated['noPo']], // Kriteria pencarian
                [
                    // Data default jika PO baru dibuat
                    'supplier_id' => $validated['supplier'], 
                    'tanggal_po' => now(), 
                    'status' => 'Open', 
                    'created_by' => Auth::id() ?? 1
                ]
            );

            // 2. Gunakan ID ($purchaseOrder->id), BUKAN string ($validated['noPo'])
            $incoming = IncomingGood::create([
                'incoming_number' => $incomingNumber,
                'no_surat_jalan' => $validated['noSuratJalan'],
                
                'po_id' => $purchaseOrder->id, // <--- UBAH INI (Pakai ID dari database)
                
                'supplier_id' => $validated['supplier'],
                'no_kendaraan' => $validated['noKendaraan'],
                'nama_driver' => $validated['namaDriver'],
                'tanggal_terima' => $validated['tanggalTerima'],
                'kategori' => $validated['kategori'] ?? 'Raw Material',
                'status' => 'Karantina',
                'received_by' => Auth::id(),
            ]);

            // Dapatkan Warehouse ID (Asumsi Bin Target semua berada di Warehouse yang sama)
            $sampleBin = WarehouseBin::where('bin_code', $validated['items'][0]['binTarget'])->first();
            $warehouseId = $sampleBin ? $sampleBin->warehouse_id : 1; // Fallback ke ID 1 jika tidak ditemukan.

            // Create incoming items AND Inventory Stock
            foreach ($validated['items'] as $itemData) {
                $material = Material::find($itemData['kodeItem']);
                
                // Cari WarehouseBin berdasarkan kode yang diinput
                $binTarget = WarehouseBin::where('bin_code', $itemData['binTarget'])->first();

                if (!$binTarget) {
                    throw new \Exception("Warehouse Bin dengan kode {$itemData['binTarget']} tidak ditemukan.");
                }

                // HITUNG TOTAL QTY DARI QTY_WADAH * QTY_UNIT
                $jumlahWadah = (float) $itemData['qtyWadah'];
                $qtyPerWadah = (float) $itemData['qtyUnit'];
                $totalQtyReceived = $jumlahWadah * $qtyPerWadah;
                
                // Generate QR code
                $qrCode = $this->generateQRCode(
                    $incomingNumber,
                    $material->kode_item,
                    $itemData['batchLot'],
                    $totalQtyReceived,
                    $itemData['expDate'] ?? ''
                );

                // 1. CREATE INCOMING GOODS ITEM
                IncomingGoodsItem::create([
                    'incoming_id' => $incoming->id,
                    'material_id' => $material->id,
                    'batch_lot' => $itemData['batchLot'],
                    'exp_date' => $itemData['expDate'] ?? null,
                    'qty_wadah' => $jumlahWadah,   // Jumlah wadah (e.g., 1)
                    'qty_unit' => $qtyPerWadah,    // Qty per wadah (e.g., 2720) 

                    'satuan' => $material->satuan,
                    
                    // Semua field checklist kondisi/coa/label
                    'kondisi_baik' => $itemData['kondisiBaik'] ?? true,
                    'kondisi_tidak_baik' => $itemData['kondisiTidakBaik'] ?? true,
                    'coa_ada' => $itemData['coaAda'] ?? true,
                    'coa_tidak_ada' => $itemData['coaTidakAda'] ?? true,
                    'label_mfg_ada' => $itemData['labelMfgAda'] ?? true,
                    'label_mfg_tidak_ada' => $itemData['labelMfgTidakAda'] ?? true,
                    'label_coa_sesuai' => $itemData['labelCoaSesuai'] ?? true,
                    'label_coa_tidak_sesuai' => $itemData['labelCoaTidakSesuai'] ?? true,

                    'bin_target' => $itemData['binTarget'],
                    'is_halal' => $itemData['isHalal'] ?? true,
                    'is_non_halal' => $itemData['isNonHalal'] ?? true,
                    
                    'pabrik_pembuat' => $itemData['pabrikPembuat'] ?? '',
                    'status_qc' => 'To QC',
                    'qr_code' => $qrCode,
                ]);

                // 2. TAMBAHKAN STOK KE INVENTORY_STOCK (Ke Bin Karantina)
                // Cek apakah stok dengan Batch/Lot yang sama sudah ada di Bin Target QRT
                $inventory = InventoryStock::firstOrNew([
                    'material_id' => $material->id,
                    'warehouse_id' => $warehouseId, // Gunakan Warehouse ID Karantina
                    'bin_id' => $binTarget->id,
                    'batch_lot' => $itemData['batchLot'],
                    'status' => 'KARANTINA', // Status Karantina
                ]);
                
                // Jika stok baru, inisialisasi nilainya
                if (!$inventory->exists) {
                    $inventory->fill([
                        'exp_date' => $itemData['expDate'] ?? null,
                        'qty_on_hand' => 0, 
                        'qty_reserved' => 0,
                        'uom' => $material->satuan, 
                        'status' => 'KARANTINA', // Status Invetory: Karantina
                        'gr_id' => $incoming->id,
                        'last_movement_date' => now(),
                    ]);
                }
                
                // *** PERBAHARUAN KALKULASI SUDAH BENAR ***
                // Tambahkan Qty baru ke stok yang sudah ada/baru. $totalQtyReceived sudah hasil kali QTY_WADAH * QTY_UNIT.
                $inventory->qty_on_hand += $totalQtyReceived;
                $inventory->qty_available = $inventory->qty_on_hand; // Stok QRT dianggap available untuk QC
                $inventory->save();

                // Log activity for each item
                $this->logActivity($incoming, 'Create', [
                    'description' => "Penerimaan Material {$material->name} ({$material->code}) Batch {$itemData['batchLot']} ke Bin {$binTarget->location_code}. Qty: {$totalQtyReceived} {$material->uom}",
                    'material_id' => $material->id,
                    'batch_lot' => $itemData['batchLot'],
                    'exp_date' => $itemData['expDate'] ?? null,
                    'qty_before' => $inventory->qty_on_hand - $totalQtyReceived,
                    'qty_after' => $inventory->qty_on_hand, 
                    'bin_to' => $binTarget->id,
                    'reference_document' => $incoming->no_surat_jalan,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', "Penerimaan berhasil disimpan dengan nomor: {$incomingNumber}");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan penerimaan: ' . $e->getMessage());
        }
    }


    /**
     * Store multiple shipments (untuk handle PDF dengan multiple item codes)
     */
    public function storeMultiple(Request $request)
    {
        $validated = $request->validate([
            'shipments' => 'required|array|min:1',
            'shipments.*.noPo' => 'required|string|max:255',
            'shipments.*.noSuratJalan' => 'required|string|max:255',
            'shipments.*.supplier' => 'required|exists:suppliers,id',
            'shipments.*.noKendaraan' => 'required|string|max:50',
            'shipments.*.namaDriver' => 'required|string|max:255',
            'shipments.*.tanggalTerima' => 'required|date',
            'shipments.*.kategori' => 'nullable|string|max:100',
            'shipments.*.incomingNumber' => 'required|string|max:255',
            'shipments.*.items' => 'required|array|min:1',
            'shipments.*.items.*.kodeItem' => 'required|exists:materials,id',
            'shipments.*.items.*.batchLot' => 'required|string|max:255',
            'shipments.*.items.*.expDate' => 'nullable|date',
            'shipments.*.items.*.qtyWadah' => 'required|numeric|min:1',
            'shipments.*.items.*.qtyUnit' => 'required|numeric|min:1',
            'shipments.*.items.*.binTarget' => 'required|string|max:255',
            'shipments.*.items.*.isHalal' => 'nullable|boolean',
            'shipments.*.items.*.isNonHalal' => 'nullable|boolean',
            'shipments.*.items.*.pabrikPembuat' => 'nullable|string|max:255',
            'shipments.*.items.*.kondisiBaik' => 'nullable|boolean',
            'shipments.*.items.*.kondisiTidakBaik' => 'nullable|boolean',
            'shipments.*.items.*.coaAda' => 'nullable|boolean',
            'shipments.*.items.*.coaTidakAda' => 'nullable|boolean',
            'shipments.*.items.*.labelMfgAda' => 'nullable|boolean',
            'shipments.*.items.*.labelMfgTidakAda' => 'nullable|boolean',
            'shipments.*.items.*.labelCoaSesuai' => 'nullable|boolean',
            'shipments.*.items.*.labelCoaTidakSesuai' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $createdShipments = [];

            foreach ($validated['shipments'] as $shipmentData) {
                // Gunakan incoming number dari PDF (sama untuk semua split)
                $incomingNumber = $shipmentData['incomingNumber'];
                
                // Check jika incoming number sudah ada (hanya cek sekali untuk yang pertama)
                if (count($createdShipments) === 0 && IncomingGood::where('incoming_number', $incomingNumber)->exists()) {
                    throw new \Exception("Nomor Incoming ({$incomingNumber}) sudah ada dalam sistem.");
                }

                // Create atau get PO
                $purchaseOrder = PurchaseOrder::firstOrCreate(
                    ['no_po' => $shipmentData['noPo']],
                    [
                        'supplier_id' => $shipmentData['supplier'],
                        'tanggal_po' => now(),
                        'status' => 'Open',
                        'created_by' => Auth::id() ?? 1
                    ]
                );

                // Create incoming good
                $incoming = IncomingGood::create([
                    'incoming_number' => $incomingNumber,
                    'no_surat_jalan' => $shipmentData['noSuratJalan'],
                    'po_id' => $purchaseOrder->id,
                    'supplier_id' => $shipmentData['supplier'],
                    'no_kendaraan' => $shipmentData['noKendaraan'],
                    'nama_driver' => $shipmentData['namaDriver'],
                    'tanggal_terima' => $shipmentData['tanggalTerima'],
                    'kategori' => $shipmentData['kategori'] ?? 'Raw Material',
                    'status' => 'Karantina',
                    'received_by' => Auth::id(),
                ]);

                // Get warehouse ID from first bin
                $sampleBin = WarehouseBin::where('bin_code', $shipmentData['items'][0]['binTarget'])->first();
                $warehouseId = $sampleBin ? $sampleBin->warehouse_id : 1;

                // Create items and inventory stock
                foreach ($shipmentData['items'] as $itemData) {
                    $material = Material::find($itemData['kodeItem']);
                    
                    $binTarget = WarehouseBin::where('bin_code', $itemData['binTarget'])->first();
                    if (!$binTarget) {
                        throw new \Exception("Warehouse Bin dengan kode {$itemData['binTarget']} tidak ditemukan.");
                    }

                    $jumlahWadah = (float) $itemData['qtyWadah'];
                    $qtyPerWadah = (float) $itemData['qtyUnit'];
                    $totalQtyReceived = $jumlahWadah * $qtyPerWadah;
                    
                    $qrCode = $this->generateQRCode(
                        $incomingNumber,
                        $material->kode_item,
                        $itemData['batchLot'],
                        $totalQtyReceived,
                        $itemData['expDate'] ?? ''
                    );

                    IncomingGoodsItem::create([
                        'incoming_id' => $incoming->id,
                        'material_id' => $material->id,
                        'batch_lot' => $itemData['batchLot'],
                        'exp_date' => $itemData['expDate'] ?? null,
                        'qty_wadah' => $jumlahWadah,   // Jumlah wadah (e.g., 1)
                        'qty_unit' => $qtyPerWadah,    // Qty per wadah (e.g., 2720)
                        'satuan' => $material->satuan,
                        'kondisi_baik' => $itemData['kondisiBaik'] ?? true,
                        'kondisi_tidak_baik' => $itemData['kondisiTidakBaik'] ?? false,
                        'coa_ada' => $itemData['coaAda'] ?? true,
                        'coa_tidak_ada' => $itemData['coaTidakAda'] ?? false,
                        'label_mfg_ada' => $itemData['labelMfgAda'] ?? true,
                        'label_mfg_tidak_ada' => $itemData['labelMfgTidakAda'] ?? false,
                        'label_coa_sesuai' => $itemData['labelCoaSesuai'] ?? true,
                        'label_coa_tidak_sesuai' => $itemData['labelCoaTidakSesuai'] ?? false,
                        'bin_target' => $itemData['binTarget'],
                        'is_halal' => $itemData['isHalal'] ?? false,
                        'is_non_halal' => $itemData['isNonHalal'] ?? false,
                        'pabrik_pembuat' => $itemData['pabrikPembuat'] ?? '',
                        'status_qc' => 'To QC',
                        'qr_code' => $qrCode,
                    ]);

                    // Update inventory stock
                    $inventory = InventoryStock::firstOrNew([
                        'material_id' => $material->id,
                        'warehouse_id' => $warehouseId,
                        'bin_id' => $binTarget->id,
                        'batch_lot' => $itemData['batchLot'],
                        'status' => 'KARANTINA',
                    ]);
                    
                    if (!$inventory->exists) {
                        $inventory->fill([
                            'exp_date' => $itemData['expDate'] ?? null,
                            'qty_on_hand' => 0,
                            'qty_reserved' => 0,
                            'uom' => $material->satuan,
                            'status' => 'KARANTINA',
                            'gr_id' => $incoming->id,
                            'last_movement_date' => now(),
                        ]);
                    }
                    
                    $inventory->qty_on_hand += $totalQtyReceived;
                    $inventory->qty_available = $inventory->qty_on_hand;
                    $inventory->save();

                    // Log activity
                    $this->logActivity($incoming, 'Create', [
                        'description' => "Penerimaan Material {$material->nama_material} ({$material->kode_item}) Batch {$itemData['batchLot']} ke Bin {$binTarget->bin_code}. Qty: {$totalQtyReceived} {$material->satuan}",
                        'material_id' => $material->id,
                        'batch_lot' => $itemData['batchLot'],
                        'exp_date' => $itemData['expDate'] ?? null,
                        'qty_before' => $inventory->qty_on_hand - $totalQtyReceived,
                        'qty_after' => $inventory->qty_on_hand,
                        'bin_to' => $binTarget->id,
                        'reference_document' => $incoming->no_surat_jalan,
                    ]);
                }

                $createdShipments[] = [
                    'id' => $incoming->id,
                    'incoming_number' => $incoming->incoming_number,
                    'no_surat_jalan' => $incoming->no_surat_jalan,
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'created_count' => count($createdShipments),
                'shipments' => $createdShipments,
                'message' => "Berhasil membuat " . count($createdShipments) . " penerimaan barang."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available statuses for a shipment from inventory
     * Used for QR label printing with status selector
     */
    public function getAvailableStatuses($shipmentId)
    {
        try {
            $shipment = IncomingGood::with(['items.material', 'supplier'])->findOrFail($shipmentId);
            
            // Get first item (karena 1 shipment = 1 item code)
            $firstItem = $shipment->items->first();
            
            if (!$firstItem) {
                return response()->json(['error' => 'No items found in this shipment'], 404);
            }
            
            // Cek status yang tersedia di inventory untuk material + batch lot ini
            $availableStatuses = InventoryStock::where('material_id', $firstItem->material_id)
                ->where('batch_lot', $firstItem->batch_lot)
                ->where('qty_on_hand', '>', 0) // Hanya ambil yang masih ada stocknya
                ->distinct()
                ->pluck('status')
                ->toArray();
            
            // Prepare shipment data untuk printing
            $shipmentData = [
                'kodeItem' => $firstItem->material->kode_item,
                'namaMaterial' => $firstItem->material->nama_material,
                'batchLot' => $firstItem->batch_lot,
                'noLot' => $firstItem->batch_lot, // alias
                'expDate' => $firstItem->exp_date,
                // PERBAIKAN: Nama kolom database SUDAH BENAR
                // qty_wadah di database = jumlah wadah (misal: 10 box)
                // qty_unit di database = qty per wadah (misal: 500 Kg per box)
                'qtyWadah' => $firstItem->qty_wadah, // Jumlah wadah (untuk cetak QR) - DIPERBAIKI
                'qtyUnit' => $firstItem->qty_unit, // Qty per wadah - DIPERBAIKI
                'qtyUnitPerWadah' => $firstItem->qty_unit, // Sama dengan qtyUnit
                'supplier' => $shipment->supplier->nama_supplier ?? 'N/A',
                'tanggalTerima' => $shipment->tanggal_terima,
                'uom' => $firstItem->material->satuan,
                'qrCode' => $firstItem->qr_code,
            ];
            
            return response()->json([
                'success' => true,
                'shipment_id' => $shipmentId,
                'available_statuses' => $availableStatuses,
                'shipment_data' => $shipmentData,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function generateQRCode($incomingNumber, $itemCode, $batchLot, $qty, $expDate)
    {
        return implode('|', [
            $incomingNumber,
            $itemCode,
            $batchLot,
            $qty,
            $expDate
        ]);
    }
}