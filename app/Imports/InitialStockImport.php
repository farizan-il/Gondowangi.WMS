<?php

namespace App\Imports;

use App\Models\InventoryStock;
use App\Models\Material;
use App\Models\WarehouseBin;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithLimit;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InitialStockImport implements ToModel, WithHeadingRow, WithChunkReading, WithLimit
{
    private $materials = [];
    private $bins = [];
    private $suppliers = [];

    public function limit(): int
    {
        return 100; // Limit to 100 rows for testing
    }

    public function model(array $row)
    {
        try {
            // Log::info('Import Row Keys:', array_keys($row)); // Optional: Keep for debug if needed
            
            // 1. Map Columns (Handle Aliases)
            $code = $row['code'] ?? $row['kode'] ?? null;
            $name = $this->sanitizeString($row['name'] ?? $row['product'] ?? 'Unknown Material');
            $uom = $this->sanitizeString($row['uom'] ?? 'PCS');
            $category = $this->sanitizeString($row['kategori'] ?? 'General');
            $location = $row['location'] ?? 'TEMP-IMPORT';
            $supplierName = $this->sanitizeString($row['supplier'] ?? null);

            // Skip if Code is empty
            if (empty($code)) {
                return null;
            }

            // 2. Find or Create Material (Cached)
            if (!isset($this->materials[$code])) {
                $this->materials[$code] = Material::firstOrCreate(
                    ['kode_item' => $code],
                    [
                        'nama_material' => $name,
                        'satuan' => $uom,
                        'kategori' => $category,
                        'deskripsi' => 'Imported from Excel',
                        'status' => 'active'
                    ]
                );
            }
            $material = $this->materials[$code];

            // 3. Find or Create Bin (Cached)
            if (!isset($this->bins[$location])) {
                $this->bins[$location] = WarehouseBin::firstOrCreate(
                    ['bin_code' => $location],
                    [
                        'bin_name' => $location,
                        'warehouse_id' => 1,
                        'zone_id' => 1,
                        'bin_type' => 'Standard',
                        'capacity' => 4,
                        'current_items' => 0,
                        'status' => 'available'
                    ]
                );
            }
            $bin = $this->bins[$location];

            // 4. Find or Create Supplier (Cached)
            if (!empty($supplierName)) {
                if (!isset($this->suppliers[$supplierName])) {
                    $this->suppliers[$supplierName] = Supplier::firstOrCreate(
                        ['nama_supplier' => $supplierName],
                        [
                            'kode_supplier' => 'SUP-' . strtoupper(substr($supplierName, 0, 3)) . rand(100, 999),
                            'alamat' => '-',
                            'telepon' => '-',
                            'email' => '-',
                            'contact_person' => '-',
                            'status' => 'active'
                        ]
                    );
                }
            }

            // 5. Determine Quantity and Status
            $batchLot = $row['serial_number'] ?? 'BATCH-' . time();
            $incomingDate = $this->parseDate($row['data_incoming'] ?? $row['tanggal_receive'] ?? null);

            $createdStocks = [];

            // Helper to get quantity from multiple possible columns
            $qtyQrt = $this->sanitizeValue($row['qrt'] ?? 0);
            $qtyRelease = $this->sanitizeValue($row['release'] ?? 0);
            $qtyRiject = $this->sanitizeValue($row['riject'] ?? 0);
            $qtyGeneral = $this->sanitizeValue($row['quantity'] ?? 0); // Handle single 'quantity' column

            // A. QRT
            if ($qtyQrt > 0) {
                $createdStocks[] = $this->createStock($material, $bin, $batchLot, $qtyQrt, 'QUARANTINE', $incomingDate);
            }

            // B. Release
            if ($qtyRelease > 0) {
                $createdStocks[] = $this->createStock($material, $bin, $batchLot, $qtyRelease, 'RELEASED', $incomingDate);
            }

            // C. Riject
            if ($qtyRiject > 0) {
                $createdStocks[] = $this->createStock($material, $bin, $batchLot, $qtyRiject, 'REJECTED', $incomingDate);
            }

            // D. General Quantity (Default to RELEASED if status not specified)
            if ($qtyGeneral > 0 && $qtyQrt == 0 && $qtyRelease == 0 && $qtyRiject == 0) {
                 $createdStocks[] = $this->createStock($material, $bin, $batchLot, $qtyGeneral, 'RELEASED', $incomingDate);
            }

            // Update Bin Current Items
            if (count($createdStocks) > 0) {
                $bin->increment('current_items');
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error importing row: ' . json_encode($row) . ' Error: ' . $e->getMessage());
            return null;
        }
    }

    private function sanitizeValue($value)
    {
        if (is_string($value) && str_starts_with($value, '=')) {
            return 0; // Treat formulas as 0 if not calculated
        }
        return is_numeric($value) ? $value : 0;
    }

    private function sanitizeString($value)
    {
        if (is_string($value) && str_starts_with($value, '=')) {
            return '-'; // Treat formulas as dash if not calculated
        }
        return $value;
    }

    private function createStock($material, $bin, $batchLot, $qty, $status, $date)
    {
        return InventoryStock::create([
            'material_id' => $material->id,
            'warehouse_id' => $bin->warehouse_id,
            'bin_id' => $bin->id,
            'batch_lot' => $batchLot,
            'qty_on_hand' => $qty,
            'qty_available' => $qty,
            'qty_reserved' => 0,
            'uom' => $material->satuan,
            'status' => $status,
            'gr_id' => null,
            'exp_date' => $date ? $date->copy()->addYears(2) : now()->addYears(2),
            'last_movement_date' => $date ?? now(),
            'created_at' => $date ?? now(),
        ]);
    }

    private function parseDate($value)
    {
        // 1. If it's a formula string, return now()
        if (is_string($value) && str_starts_with($value, '=')) {
            return now();
        }

        if (empty($value)) return now();

        try {
            // 2. Only use excelToDateTimeObject if it's numeric
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            }
            
            // 3. Otherwise try Carbon parse
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return now();
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
