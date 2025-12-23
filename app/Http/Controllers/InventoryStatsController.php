<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryStatsController extends Controller
{
    /**
     * Get inventory statistics for print QR modal
     * Returns count of materials by status and expired materials
     */
    public function getInventoryStats()
    {
        try {
            // Get stats by status - count unique material_id + batch_lot combinations
            $stats = InventoryStock::select('status', DB::raw('COUNT(DISTINCT CONCAT(material_id, "-", batch_lot)) as count'))
                ->where('qty_on_hand', '>', 0)
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');

            // Get expired materials count (exp_date < today)
            $expiredCount = InventoryStock::select(DB::raw('COUNT(DISTINCT CONCAT(material_id, "-", batch_lot)) as count'))
                ->where('qty_on_hand', '>', 0)
                ->where('exp_date', '<', Carbon::now())
                ->value('count') ?? 0;

            // Calculate total QR codes to print (1 QR per inventory record)
            $totalQRCodes = InventoryStock::where('qty_on_hand', '>', 0)->count();

            $result = [
                'released' => $stats['RELEASED'] ?? 0,
                'karantina' => $stats['KARANTINA'] ?? 0,
                'rejected' => $stats['REJECTED'] ?? 0,
                'hold' => $stats['HOLD'] ?? 0,
                'expired' => $expiredCount,
                'total_qr_codes' => (int) $totalQRCodes,
                'estimated_time_seconds' => (int) ceil($totalQRCodes / 20), // Estimate: 20 QR/second
            ];

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting inventory stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik inventory',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all materials for print QR
     * Returns expanded data (1 entry per unit based on qty_on_hand)
     */
    public function getAllMaterialsForPrint(Request $request)
    {
        try {
            $limit = $request->input('limit', 100); // Batch size
            $offset = $request->input('offset', 0);

            // Get materials with qty_on_hand > 0
            $materials = InventoryStock::with(['material', 'bin', 'warehouse'])
                ->where('qty_on_hand', '>', 0)
                ->orderBy('status', 'ASC') // Released first, then others
                ->orderBy('material_id', 'ASC')
                ->skip($offset)
                ->take($limit)
                ->get();

            $result = [];

            foreach ($materials as $stock) {
                // Generate QR content for this inventory record
                $qrContent = implode('|', [
                    $stock->material->kode_item,
                    $stock->batch_lot,
                    $stock->status,
                    $stock->exp_date ? $stock->exp_date->format('Y-m-d') : 'N/A',
                    $stock->qty_on_hand . ' ' . $stock->uom
                ]);

                $result[] = [
                    'qr_content' => $qrContent,
                    'kode_item' => $stock->material->kode_item,
                    'nama_material' => $stock->material->nama_material,
                    'batch_lot' => $stock->batch_lot,
                    'serial_number' => sprintf('%s-%s', $stock->material->kode_item, $stock->batch_lot),
                    'status' => $stock->status,
                    'exp_date' => $stock->exp_date ? $stock->exp_date->format('Y-m-d') : null,
                    'lokasi' => $stock->bin ? $stock->bin->bin_code : 'N/A',
                    'uom' => $stock->uom,
                    'qty_on_hand' => $stock->qty_on_hand,
                ];
            }

            // Get total count for pagination info
            $totalRecords = InventoryStock::where('qty_on_hand', '>', 0)->count();
            return response()->json([
                'success' => true,
                'data' => $result,
                'pagination' => [
                    'total_stock_records' => $totalRecords,
                    'total_qr_codes' => $totalRecords, // Same as records count
                    'current_batch_size' => count($result),
                    'offset' => $offset,
                    'limit' => $limit,
                    'has_more' => ($offset + $limit) < $totalRecords
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting materials for print: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data material',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
