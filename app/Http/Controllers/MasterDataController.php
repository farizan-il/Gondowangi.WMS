<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Supplier;
use App\Models\WarehouseBin;
use App\Models\WarehouseZone;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use App\Traits\ActivityLogger;

class MasterDataController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        return Inertia::render('MasterData', [
            'initialSkuData' => Material::with('defaultSupplier')->get()->map(fn($item) => [
                'id' => $item->id,
                'code' => $item->kode_item,
                'name' => $item->nama_material,
                'uom' => $item->satuan,
                'category' => $item->kategori,
                'qcRequired' => (bool)$item->qc_required,
                'expiry' => (bool)$item->expiry_required,
                'supplierDefault' => $item->defaultSupplier ? $item->defaultSupplier->nama_supplier : '-',
                'abcClass' => $item->abc_class,
                'status' => $item->status === 'active' ? 'Active' : 'Inactive'
            ]),
            'initialSupplierData' => Supplier::all()->map(fn($item) => [
                'id' => $item->id,
                'code' => $item->kode_supplier,
                'name' => $item->nama_supplier,
                'address' => $item->alamat,
                'contactPerson' => $item->contact_person,
                'phone' => $item->telepon,
                'status' => $item->status === 'active' ? 'Active' : 'Inactive'
            ]),
            'initialBinData' => WarehouseBin::with('zone')->get()->map(fn($item) => [
                'id' => $item->id,
                'code' => $item->bin_code,
                'zone' => $item->zone ? $item->zone->zone_name : 'N/A',
                'capacity' => $item->capacity,
                'type' => $item->bin_type,
                'qrCode' => $item->qr_code_path ? asset('storage/' . $item->qr_code_path) : null,
                'status' => ucfirst($item->status)
            ]),
            'initialUserData' => User::with('role')->get()->map(fn($item) => [
                'id' => $item->id,
                'jabatan' => $item->jabatan,
                'fullName' => $item->nama_lengkap,
                'role' => $item->role->role_name ?? 'N/A',
                'department' => $item->departement,
                'status' => $item->status === 'active' ? 'Active' : 'Inactive'
            ]),
            'supplierList' => Supplier::where('status', 'active')->get()->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->nama_supplier
            ]),
            'zoneList' => WarehouseZone::where('status', 'active')->get()->map(fn($z) => [
                'id' => $z->id,
                'name' => $z->zone_name
            ])
        ]);
    }

    // ========== SKU/MATERIAL ==========
    public function storeSku(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:materials,kode_item',
            'name' => 'required|string',
            'uom' => 'required|string',
            'category' => 'required|string',
            'qcRequired' => 'boolean',
            'expiry' => 'boolean',
            'supplierDefault' => 'nullable|integer|exists:suppliers,id',
            'abcClass' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        try {
            $material = Material::create([
                'kode_item' => $validated['code'],
                'nama_material' => $validated['name'],
                'satuan' => $validated['uom'],
                'kategori' => $validated['category'],
                'qc_required' => $validated['qcRequired'] ?? false,
                'expiry_required' => $validated['expiry'] ?? false,
                'default_supplier_id' => $validated['supplierDefault'],
                'abc_class' => $validated['abcClass'],
                'status' => strtolower($validated['status'])
            ]);

            $this->logActivity($material, 'Create', [
                'description' => "Created new SKU: {$material->nama_material}",
                'new_value' => json_encode($material),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SKU berhasil ditambahkan',
                'data' => $material
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error storing SKU: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSku(Request $request, $id)
    {
        $validated = $request->validate([
            'code' => 'required|unique:materials,kode_item,' . $id,
            'name' => 'required|string',
            'uom' => 'required|string',
            'category' => 'required|string',
            'qcRequired' => 'boolean',
            'expiry' => 'boolean',
            'supplierDefault' => 'nullable|integer|exists:suppliers,id',
            'abcClass' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        try {
            $material = Material::findOrFail($id);
            $oldValue = $material->replicate();

            $material->update([
                'kode_item' => $validated['code'],
                'nama_material' => $validated['name'],
                'satuan' => $validated['uom'],
                'kategori' => $validated['category'],
                'qc_required' => $validated['qcRequired'] ?? false,
                'expiry_required' => $validated['expiry'] ?? false,
                'default_supplier_id' => $validated['supplierDefault'],
                'abc_class' => $validated['abcClass'],
                'status' => strtolower($validated['status'])
            ]);

            $this->logActivity($material, 'Update', [
                'description' => "Updated SKU: {$material->nama_material}",
                'old_value' => json_encode($oldValue),
                'new_value' => json_encode($material),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SKU berhasil diupdate',
                'data' => $material
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error updating SKU: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSku($id)
    {
        try {
            $material = Material::findOrFail($id);
            $oldValue = $material->replicate();
            $material->delete();

            $this->logActivity($oldValue, 'Delete', [
                'description' => "Deleted SKU: {$oldValue->nama_material}",
                'old_value' => json_encode($oldValue),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SKU berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error deleting SKU: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========== SUPPLIER ==========
    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:suppliers,kode_supplier',
            'name' => 'required|string',
            'address' => 'required|string',
            'contactPerson' => 'nullable|string',
            'phone' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        try {
            $supplier = Supplier::create([
                'kode_supplier' => $validated['code'],
                'nama_supplier' => $validated['name'],
                'alamat' => $validated['address'],
                'contact_person' => $validated['contactPerson'],
                'telepon' => $validated['phone'],
                'status' => strtolower($validated['status'])
            ]);

            $this->logActivity($supplier, 'Create', [
                'description' => "Created new Supplier: {$supplier->nama_supplier}",
                'new_value' => json_encode($supplier),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil ditambahkan',
                'data' => $supplier
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error storing Supplier: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSupplier(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $oldValue = $supplier->replicate();

        $validated = $request->validate([
            'code' => 'required|unique:suppliers,kode_supplier,' . $id,
            'name' => 'required|string',
            'address' => 'required|string',
            'contactPerson' => 'nullable|string',
            'phone' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        try {
            $supplier->update([
                'kode_supplier' => $validated['code'],
                'nama_supplier' => $validated['name'],
                'alamat' => $validated['address'],
                'contact_person' => $validated['contactPerson'],
                'telepon' => $validated['phone'],
                'status' => strtolower($validated['status'])
            ]);

            $this->logActivity($supplier, 'Update', [
                'description' => "Updated Supplier: {$supplier->nama_supplier}",
                'old_value' => json_encode($oldValue),
                'new_value' => json_encode($supplier),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil diupdate',
                'data' => $supplier
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error updating Supplier: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSupplier($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $oldValue = $supplier->replicate();
            $supplier->delete();

            $this->logActivity($oldValue, 'Delete', [
                'description' => "Deleted Supplier: {$oldValue->nama_supplier}",
                'old_value' => json_encode($oldValue),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error deleting Supplier: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========== BIN LOCATION ==========
    public function storeBin(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:warehouse_bins,bin_code',
            'zone' => 'required|integer|exists:warehouse_zones,id',
            'capacity' => 'nullable|numeric',
            'type' => 'required|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        try {
            // Get zone untuk mendapatkan warehouse_id
            $zone = WarehouseZone::findOrFail($validated['zone']);
            
            $bin = WarehouseBin::create([
                'bin_code' => $validated['code'],
                'bin_name' => $validated['code'],
                'zone_id' => $validated['zone'],
                'warehouse_id' => $zone->warehouse_id ?? 1,
                'bin_type' => $validated['type'],
                'capacity' => $validated['capacity'] ?? 0,
                'current_items' => 0,
                'status' => strtolower($validated['status'] === 'Active' ? 'available' : 'inactive')
            ]);

            // Generate QR Code
            $qrGenerated = $this->generateQRCode($bin);
            
            if (!$qrGenerated) {
                Log::warning('QR Code generation failed for bin: ' . $bin->id);
            }

            // Refresh bin untuk mendapatkan data terbaru
            $bin->refresh();

            $this->logActivity($bin, 'Create', [
                'description' => "Created new Bin: {$bin->bin_code}",
                'new_value' => json_encode($bin),
            ]);

            return response()->json([
                'success' => true,
                'message' => $qrGenerated 
                    ? 'Bin berhasil ditambahkan dengan QR Code' 
                    : 'Bin berhasil ditambahkan tapi QR Code gagal digenerate',
                'data' => [
                    'id' => $bin->id,
                    'code' => $bin->bin_code,
                    'zone' => $bin->zone->zone_name ?? 'N/A',
                    'capacity' => $bin->capacity,
                    'type' => $bin->bin_type,
                    'qrCode' => $bin->qr_code_path ? asset('storage/' . $bin->qr_code_path) : null,
                    'status' => ucfirst($bin->status)
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error storing Bin: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateBin(Request $request, $id)
    {
        $bin = WarehouseBin::findOrFail($id);
        $oldValue = $bin->replicate();

        $validated = $request->validate([
            'code' => 'required|unique:warehouse_bins,bin_code,' . $id,
            'zone' => 'required|integer|exists:warehouse_zones,id',
            'capacity' => 'nullable|numeric',
            'type' => 'required|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        try {
            $oldCode = $bin->bin_code;
            $zone = WarehouseZone::findOrFail($validated['zone']);
            
            $bin->update([
                'bin_code' => $validated['code'],
                'bin_name' => $validated['code'],
                'zone_id' => $validated['zone'],
                'warehouse_id' => $zone->warehouse_id ?? $bin->warehouse_id,
                'bin_type' => $validated['type'],
                'capacity' => $validated['capacity'] ?? 0,
                'status' => strtolower($validated['status'] === 'Active' ? 'available' : 'inactive')
            ]);

            // Regenerate QR Code if bin code changed
            if ($oldCode !== $validated['code']) {
                // Delete old QR code
                if ($bin->qr_code_path && Storage::disk('public')->exists($bin->qr_code_path)) {
                    Storage::disk('public')->delete($bin->qr_code_path);
                }
                
                // Generate new QR code
                $this->generateQRCode($bin);
            }

            $bin->refresh();
            $this->logActivity($bin, 'Update', [
                'description' => "Updated Bin: {$bin->bin_code}",
                'old_value' => json_encode($oldValue),
                'new_value' => json_encode($bin),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bin berhasil diupdate',
                'data' => [
                    'id' => $bin->id,
                    'code' => $bin->bin_code,
                    'zone' => $bin->zone->zone_name ?? 'N/A',
                    'capacity' => $bin->capacity,
                    'type' => $bin->bin_type,
                    'qrCode' => $bin->qr_code_path ? asset('storage/' . $bin->qr_code_path) : null,
                    'status' => ucfirst($bin->status)
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error updating Bin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteBin($id)
    {
        try {
            $bin = WarehouseBin::findOrFail($id);
            $oldValue = $bin->replicate();
            
            // Delete QR code file
            if ($bin->qr_code_path && Storage::disk('public')->exists($bin->qr_code_path)) {
                Storage::disk('public')->delete($bin->qr_code_path);
            }
            
            $bin->delete();

            $this->logActivity($oldValue, 'Delete', [
                'description' => "Deleted Bin: {$oldValue->bin_code}",
                'old_value' => json_encode($oldValue),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bin berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting Bin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function previewBinQRCode($id)
    {
        try {
            $bin = WarehouseBin::with('zone')->findOrFail($id);
            
            // Generate QR Code jika belum ada
            if (!$bin->qr_code_path || !Storage::disk('public')->exists($bin->qr_code_path)) {
                $qrGenerated = $this->generateQRCode($bin);
                if (!$qrGenerated) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal generate QR Code'
                    ], 500);
                }
                $bin->refresh();
            }

            $filePath = storage_path('app/public/' . $bin->qr_code_path);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code file tidak ditemukan'
                ], 404);
            }

            // Return base64 encoded image untuk preview
            $imageData = base64_encode(file_get_contents($filePath));
            $imageSrc = 'data:image/png;base64,' . $imageData;

            return response()->json([
                'success' => true,
                'data' => [
                    'image' => $imageSrc,
                    'bin_code' => $bin->bin_code,
                    'bin_name' => $bin->bin_name,
                    'zone_name' => $bin->zone ? $bin->zone->zone_name : 'N/A',
                    'bin_type' => $bin->bin_type,
                    'capacity' => $bin->capacity,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error previewing QR Code: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Download QR Code
    public function downloadBinQRCode($id)
    {
        try {
            $bin = WarehouseBin::findOrFail($id);
            
            if (!$bin->qr_code_path || !Storage::disk('public')->exists($bin->qr_code_path)) {
                $this->generateQRCode($bin);
                $bin->refresh();
            }

            $filePath = storage_path('app/public/' . $bin->qr_code_path);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code file tidak ditemukan'
                ], 404);
            }

            return response()->download($filePath, 'QR_' . $bin->bin_code . '.png');

        } catch (\Exception $e) {
            Log::error('Error downloading QR Code: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateQRCode($bin)
    {
        try {
            Log::info('=== START QR Code Generation (Endroid v4) ===');
            Log::info('Bin ID: ' . $bin->id);
            Log::info('Bin Code: ' . $bin->bin_code);
            
            $directory = 'qrcodes/bins';
            $fullPath = storage_path('app/public/' . $directory);
            
            Log::info('Full Path: ' . $fullPath);
            
            if (!file_exists($fullPath)) {
                $created = mkdir($fullPath, 0755, true);
                Log::info('Directory created: ' . ($created ? 'YES' : 'NO'));
            } else {
                Log::info('Directory already exists');
            }

            // QR Data
            $qrData = json_encode([
                'type' => 'warehouse_bin',
                'bin_id' => $bin->id,
                'bin_code' => $bin->bin_code,
                'zone_id' => $bin->zone_id,
                'timestamp' => now()->toIso8601String()
            ]);
            
            Log::info('QR Data: ' . $qrData);

            // Generate QR Code (Endroid v4 syntax)
            $qrCode = new QrCode($qrData);
            $qrCode->setSize(300);
            $qrCode->setMargin(10);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            Log::info('QR Code generated successfully');

            // Filename
            $cleanCode = preg_replace('/[^A-Za-z0-9\-]/', '_', $bin->bin_code);
            $filename = 'bin_' . $cleanCode . '_' . time() . '.png';
            $relativePath = $directory . '/' . $filename;
            $absolutePath = $fullPath . '/' . $filename;

            Log::info('Saving to: ' . $absolutePath);

            // Save file
            $bytesWritten = file_put_contents($absolutePath, $result->getString());
            
            Log::info('Bytes written: ' . $bytesWritten);

            if ($bytesWritten === false) {
                Log::error('Failed to write file');
                return false;
            }

            // Verify file exists
            if (!file_exists($absolutePath)) {
                Log::error('File does not exist after save!');
                return false;
            }

            Log::info('File exists: YES, size: ' . filesize($absolutePath) . ' bytes');

            // Update database
            $updated = $bin->update(['qr_code_path' => $relativePath]);
            
            Log::info('Database updated: ' . ($updated ? 'YES' : 'NO'));

            if (!$updated) {
                Log::error('Failed to update database');
                return false;
            }

            Log::info('=== QR Code Generation SUCCESS ===');
            return true;
            
        } catch (\Exception $e) {
            Log::error('=== QR Code Generation FAILED ===');
            Log::error('Exception: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile());
            Log::error('Line: ' . $e->getLine());
            Log::error('Trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    // Get QR Code for download/print
    public function generateBinQRCode($id)
    {
        try {
            $bin = WarehouseBin::findOrFail($id);
            
            if (!$bin->qr_code_path || !Storage::disk('public')->exists($bin->qr_code_path)) {
                $this->generateQRCode($bin);
                $bin->refresh();
            }

            $filePath = storage_path('app/public/' . $bin->qr_code_path);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code file not found'
                ], 404);
            }

            return response()->download($filePath, 'QR_' . $bin->bin_code . '.png');

        } catch (\Exception $e) {
            Log::error('Error downloading QR Code: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========== USER ==========
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'jabatan' => 'required|string',
            'fullName' => 'required|string',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'department' => 'required|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        try {
            $user = User::create([
                'nama_lengkap' => $validated['fullName'],
                'email' => strtolower(str_replace(' ', '.', $validated['fullName'])) . '@company.com',
                'nik' => 'NIK-' . time(),
                'password' => bcrypt($validated['password']),
                'jabatan' => $validated['jabatan'],
                'departement' => $validated['department'],
                'status' => strtolower($validated['status']),
                'role_id' => $this->getRoleId($validated['role'])
            ]);

            $this->logActivity($user, 'Create', [
                'description' => "Created new User: {$user->nama_lengkap}",
                'new_value' => json_encode($user),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error storing User: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $oldValue = $user->replicate();

        $validated = $request->validate([
            'jabatan' => 'required|string',
            'fullName' => 'required|string',
            'role' => 'required|string',
            'department' => 'required|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        try {
            $user->update([
                'nama_lengkap' => $validated['fullName'],
                'jabatan' => $validated['jabatan'],
                'departement' => $validated['department'],
                'status' => strtolower($validated['status']),
                'role_id' => $this->getRoleId($validated['role'])
            ]);

            $this->logActivity($user, 'Update', [
                'description' => "Updated User: {$user->nama_lengkap}",
                'old_value' => json_encode($oldValue),
                'new_value' => json_encode($user),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error updating User: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $oldValue = $user->replicate();
            $user->delete();

            $this->logActivity($oldValue, 'Delete', [
                'description' => "Deleted User: {$oldValue->nama_lengkap}",
                'old_value' => json_encode($oldValue),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error deleting User: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getRoleId($roleName)
    {
        $role = Role::where('role_name', $roleName)->first();
        return $role->id ?? 1;
    }
}