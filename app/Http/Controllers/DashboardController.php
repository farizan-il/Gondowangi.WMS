<?php

namespace App\Http\Controllers;
use App\Models\InventoryStock;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Daftar status InventoryStock yang ingin ditampilkan di dashboard
        $validStatuses = ['KARANTINA', 'RELEASED', 'HOLD', 'REJECTED'];

        // 1. Ambil Data Inventory Stock dengan Kriteria Pemantauan
        $materialItems = InventoryStock::query()
            ->where('qty_on_hand', '>', 0)
            // 💡 PERBAIKAN UTAMA: Menggunakan status yang dikonfirmasi oleh pengguna
            ->whereIn('status', $validStatuses)
            ->with([
                'material',
                'bin',
                'movements.executedBy',
                'movements.fromBin',
                'movements.toBin'
            ])
            ->get()->map(function ($item) {

                // Logika pemrosesan history (sama seperti sebelumnya)
                $history = $item->movements->map(function ($movement) {
                    $detail = '';
                    if ($movement->movement_type === 'GR') {
                        $detail = "Penerimaan $movement->qty $movement->uom di lokasi " . ($movement->toBin->bin_code ?? 'N/A') . ". Ref: " . ($movement->reference_document ?? 'N/A');
                    } elseif ($movement->movement_type === 'B2B') {
                        $detail = "Transfer Bin to Bin: Pindah $movement->qty $movement->uom dari " . ($movement->fromBin->bin_code ?? 'N/A') . " ke " . ($movement->toBin->bin_code ?? 'N/A') . ". Ref: " . ($movement->reference_document ?? 'N/A');
                    } else {
                        $detail = "$movement->movement_type $movement->qty $movement->uom. Lokasi: " . ($movement->toBin->bin_code ?? 'N/A') . ". Ref: " . ($movement->reference_type ?? 'N/A');
                    }

                    return [
                        'id' => $movement->id,
                        'date' => $movement->movement_date->toISOString(),
                        'action' => $movement->movement_type,
                        'detail' => $detail,
                        'user' => $movement->executedBy->name ?? 'System',
                    ];
                })->toArray(); // Konversi ke array untuk serialisasi

                // --- Logika Status Khusus Dashboard ---
                $binCode = $item->bin->bin_code ?? '';
                $requiresPutAway = false;
                $requiresQC = false;
                $displayStatus = $item->status;
                
                if ($item->status === 'RELEASED' && str_starts_with($binCode, 'QRT-')) {
                    $requiresPutAway = true;
                    // Ubah status yang ditampilkan agar jelas di tabel
                    $displayStatus = 'Released (Perlu Put Away)'; 
                }

                // 2. Cek apakah item KARANTINA (membutuhkan QC/tindak lanjut)
                if ($item->status === 'KARANTINA') {
                    $requiresQC = true;  
                    $displayStatus = 'Karantina (Perlu QC/Tindak Lanjut)';
                }
                
                // 3. Cek jika item HOLD
                if ($item->status === 'HOLD') {
                    $displayStatus = 'HOLD (Menunggu Persetujuan)';
                }

                // 4. Cek jika item REJECT
                if ($item->status === 'REJECT') {  
                    $displayStatus = 'Ditolak (Perlu Tindak Lanjut)';
                }

                return [
                    'id' => 'INV-' . $item->id,
                    'source_table' => 'inventory_stock',
                    'type' => $item->material->kategori,
                    'kode' => $item->material->kode_item,
                    'nama' => $item->material->nama_material,
                    'lot' => $item->batch_lot,
                    'lokasi' => $binCode,
                    'qty' => $item->qty_on_hand,
                    'uom' => $item->uom,
                    'expiredDate' => $item->exp_date ? $item->exp_date->toDateString() : 'N/A',
                    // 💡 PENTING: Menggunakan $displayStatus di sini
                    'status' => $displayStatus, 
                    'history' => $history,
                    'qr_data' => json_encode([
                        'id' => $item->id,
                        'kode' => $item->material->kode_item,
                        'lot' => $item->batch_lot,
                        'status' => $item->status, // Status asli (RELEASED/KARANTINA) untuk QR
                    ]),
                    // 💡 PENTING: Menggunakan status asli (sebelum diubah $displayStatus)
                    'qr_type' => $item->status, 
                    'requiresPutAway' => $requiresPutAway,
                    'requiresQC' => $requiresQC,
                ];
            });
        
        $putAwayCount = $materialItems->filter(fn($item) => $item['requiresPutAway'])->count();

        // Hitungan QC: Item KARANTINA
        $qcCount = $materialItems->filter(fn($item) => $item['requiresQC'])->count();

        // Hitungan Expired dan Expiring Soon
        // Hitung ulang di sini untuk akurasi penuh
        $expiredCount = InventoryStock::where('qty_on_hand', '>', 0)
            ->where('exp_date', '<=', now())
            ->whereIn('status', $validStatuses) // Tambahkan filter status
            ->count();
        $expiringSoonCount = InventoryStock::where('qty_on_hand', '>', 0)
            ->where('exp_date', '<=', now()->addDays(30))
            ->where('exp_date', '>', now())
            ->whereIn('status', $validStatuses) // Tambahkan filter status
            ->count();

        $alerts = [];
        
        // Notifikasi Cerdas Put Away
        if ($putAwayCount > 0) {
            $alerts[] = ['id' => '0', 'type' => 'info', 'message' => "💡 **$putAwayCount item** status Released tapi masih di Bin Karantina (`QRT-*`). Segera lakukan **Put Away**."];
        }

        // Notifikasi QC yang Dibutuhkan (Karantina)
        if ($qcCount > 0) {
            $alerts[] = ['id' => '3', 'type' => 'info', 'message' => "📝 **$qcCount item** berada di status **KARANTINA** dan membutuhkan Quality Check/Tindak Lanjut."
            ];
        }

        if ($expiringSoonCount > 0) {
            $alerts[] = ['id' => '1', 'type' => 'warning', 'message' => "$expiringSoonCount item akan expired dalam 30 hari"];
        }
        if ($expiredCount > 0) {
            $alerts[] = ['id' => '2', 'type' => 'error', 'message' => "$expiredCount item sudah expired!"];
        }

        return Inertia::render('Dashboard', [
            'materialItems' => $materialItems,
            'alerts' => $alerts,
        ]);
    }
}