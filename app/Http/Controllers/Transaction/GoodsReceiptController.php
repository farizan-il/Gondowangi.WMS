<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\IncomingGood;
use App\Models\IncomingGoodsItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Traits\ActivityLogger;

class GoodsReceiptController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        $incomingGoods = IncomingGood::with([
            'purchaseOrder',
            'supplier',
            'items.material',
            'receiver'
        ])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($incoming) {
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
                'status' => $incoming->status,
                'items' => $incoming->items->map(function ($item) {
                    return [
                        'kodeItem' => $item->material->kode_item ?? '',
                        'namaMaterial' => $item->material->nama_material ?? '',
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
                        'qrCode' => $item->qr_code,
                    ];
                })
            ];
        });

        // Data untuk dropdown
        $purchaseOrders = PurchaseOrder::with('supplier')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($po) {
                return [
                    'id' => $po->id,
                    'no_po' => $po->no_po,
                    'supplier_id' => $po->supplier_id,
                    'supplier_name' => $po->supplier->nama_supplier ?? ''
                ];
            });

        $suppliers = Supplier::where('status', 'active')
            ->orderBy('nama_supplier')
            ->get(['id', 'kode_supplier', 'nama_supplier']);

        $materials = Material::where('status', 'active')
            ->with('defaultSupplier')
            ->orderBy('kode_item')
            ->get()
            ->map(function ($material) {
                return [
                    'id' => $material->id,
                    'code' => $material->kode_item,
                    'name' => $material->nama_material,
                    'mfg' => $material->defaultSupplier->nama_supplier ?? '',
                    'qcRequired' => $material->qc_required,
                    'kategori' => $material->kategori,
                ];
            });

        return Inertia::render('PenerimaanBarang', [
            'shipments' => $incomingGoods,
            'purchaseOrders' => $purchaseOrders,
            'suppliers' => $suppliers,
            'materials' => $materials,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'noPo' => 'required|string|max:255',
            // 'noPo' => 'required|exists:purchase_orders,id',
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
            'items.*.qtyWadah' => 'required|numeric|min:0',
            'items.*.qtyUnit' => 'required|numeric|min:0',
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

        DB::beginTransaction();
        try {
            // Generate incoming number
            $date = date('Ymd');
            $lastIncoming = IncomingGood::whereDate('created_at', today())->latest()->first();
            $sequence = $lastIncoming ? (intval(substr($lastIncoming->incoming_number, -4)) + 1) : 1;
            $incomingNumber = "IN/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // Create incoming good record
            $incoming = IncomingGood::create([
                'incoming_number' => $incomingNumber,
                'no_surat_jalan' => $validated['noSuratJalan'],
                'po_id' => $validated['noPo'],
                'supplier_id' => $validated['supplier'],
                'no_kendaraan' => $validated['noKendaraan'],
                'nama_driver' => $validated['namaDriver'],
                'tanggal_terima' => $validated['tanggalTerima'],
                'kategori' => $validated['kategori'] ?? 'Raw Material',
                'status' => 'Karantina',
                'received_by' => Auth::id(),
            ]);

            // Create incoming items
            foreach ($validated['items'] as $itemData) {
                $material = Material::find($itemData['kodeItem']);
                
                // Generate QR code
                $qrCode = $this->generateQRCode(
                    $incomingNumber,
                    $material->kode_item,
                    $itemData['batchLot'],
                    $itemData['qtyUnit'],
                    $itemData['expDate'] ?? ''
                );

                $tester = IncomingGoodsItem::create([
                    'incoming_id' => $incoming->id,
                    'material_id' => $material->id,
                    'batch_lot' => $itemData['batchLot'],
                    'exp_date' => $itemData['expDate'] ?? null,
                    'qty_wadah' => $itemData['qtyWadah'],
                    'qty_unit' => $itemData['qtyUnit'],
                    'satuan' => $material->satuan,
                    
                    'kondisi_baik' => $itemData['kondisiBaik'] ?? false,
                    'kondisi_tidak_baik' => $itemData['kondisiTidakBaik'] ?? false,
                    'coa_ada' => $itemData['coaAda'] ?? false,
                    'coa_tidak_ada' => $itemData['coaTidakAda'] ?? false,
                    'label_mfg_ada' => $itemData['labelMfgAda'] ?? false,
                    'label_mfg_tidak_ada' => $itemData['labelMfgTidakAda'] ?? false,
                    'label_coa_sesuai' => $itemData['labelCoaSesuai'] ?? false,
                    'label_coa_tidak_sesuai' => $itemData['labelCoaTidakSesuai'] ?? false,
                    
                    'pabrik_pembuat' => $itemData['pabrikPembuat'] ?? '',
                    'status_qc' => 'To QC',
                    'qr_code' => $qrCode,
                ]);
                // dd($tester);

                // Log activity for each item
                $this->logActivity($incoming, 'Create', [
                    'description' => "Material {$material->nama_material} diterima.",
                    'material_id' => $material->id,
                    'batch_lot' => $itemData['batchLot'],
                    'exp_date' => $itemData['expDate'] ?? null,
                    'qty_after' => $itemData['qtyUnit'],
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