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

class MasterDataController extends Controller
{
    public function index()
    {
        return Inertia::render('MasterData', [
            'initialSkuData' => Material::all()->map(fn($item) => [
                'id' => $item->id,
                'code' => $item->kode_item,
                'name' => $item->nama_material,
                'uom' => $item->satuan,
                'category' => $item->kategori,
                'qcRequired' => (bool)$item->qc_required,
                'expiry' => (bool)$item->expiry_required,
                'supplierDefault' => $item->default_supplier_id,
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
            'initialBinData' => WarehouseBin::all()->map(fn($item) => [
                'id' => $item->id,
                'code' => $item->bin_code,
                'zone' => $item->zone_id,
                'capacity' => $item->capacity,
                'type' => $item->bin_type,
                'status' => ucfirst($item->status)
            ]),
            'initialUserData' => User::all()->map(fn($item) => [
                'id' => $item->id,
                'jabatan' => $item->jabatan,
                'fullName' => $item->nama_lengkap,
                'role' => $item->role->role_name ?? 'N/A',
                'department' => $item->departement,
                'status' => $item->status === 'active' ? 'Active' : 'Inactive'
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
            'supplierDefault' => 'nullable|string',
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
                'abc_class' => $validated['abcClass'],
                'status' => strtolower($validated['status'])
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
            'supplierDefault' => 'nullable|string',
            'abcClass' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        try {
            $material = Material::findOrFail($id);
            $material->update([
                'kode_item' => $validated['code'],
                'nama_material' => $validated['name'],
                'satuan' => $validated['uom'],
                'kategori' => $validated['category'],
                'qc_required' => $validated['qcRequired'] ?? false,
                'expiry_required' => $validated['expiry'] ?? false,
                'abc_class' => $validated['abcClass'],
                'status' => strtolower($validated['status'])
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
            Material::findOrFail($id)->delete();

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

        Supplier::create([
            'kode_supplier' => $validated['code'],
            'nama_supplier' => $validated['name'],
            'alamat' => $validated['address'],
            'contact_person' => $validated['contactPerson'],
            'telepon' => $validated['phone'],
            'status' => strtolower($validated['status'])
        ]);

        return redirect()->route('master-data')->with('message', ['type' => 'success', 'text' => 'Supplier berhasil ditambahkan']);
    }

    public function updateSupplier(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|unique:suppliers,kode_supplier,' . $id,
            'name' => 'required|string',
            'address' => 'required|string',
            'contactPerson' => 'nullable|string',
            'phone' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        $supplier->update([
            'kode_supplier' => $validated['code'],
            'nama_supplier' => $validated['name'],
            'alamat' => $validated['address'],
            'contact_person' => $validated['contactPerson'],
            'telepon' => $validated['phone'],
            'status' => strtolower($validated['status'])
        ]);

        return redirect()->route('master-data')->with('message', ['type' => 'success', 'text' => 'Supplier berhasil diupdate']);
    }

    public function deleteSupplier($id)
    {
        Supplier::findOrFail($id)->delete();

        return redirect()->route('master-data')->with('message', ['type' => 'success', 'text' => 'Supplier berhasil dihapus']);
    }

    // ========== BIN LOCATION ==========
    public function storeBin(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:warehouse_bins,bin_code',
            'zone' => 'required|string',
            'capacity' => 'nullable|numeric',
            'type' => 'required|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        WarehouseBin::create([
            'bin_code' => $validated['code'],
            'bin_name' => $validated['code'],
            'zone_id' => 1, // Sesuaikan dengan logic Anda
            'warehouse_id' => 1,
            'bin_type' => $validated['type'],
            'capacity' => $validated['capacity'],
            'status' => strtolower($validated['status'] === 'Active' ? 'available' : 'inactive')
        ]);

        return redirect()->route('master-data')->with('message', ['type' => 'success', 'text' => 'Bin berhasil ditambahkan']);
    }

    public function updateBin(Request $request, $id)
    {
        $bin = WarehouseBin::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|unique:warehouse_bins,bin_code,' . $id,
            'zone' => 'required|string',
            'capacity' => 'nullable|numeric',
            'type' => 'required|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        $bin->update([
            'bin_code' => $validated['code'],
            'bin_name' => $validated['code'],
            'bin_type' => $validated['type'],
            'capacity' => $validated['capacity'],
            'status' => strtolower($validated['status'] === 'Active' ? 'available' : 'inactive')
        ]);

        return redirect()->route('master-data')->with('message', ['type' => 'success', 'text' => 'Bin berhasil diupdate']);
    }

    public function deleteBin($id)
    {
        WarehouseBin::findOrFail($id)->delete();

        return redirect()->route('master-data')->with('message', ['type' => 'success', 'text' => 'Bin berhasil dihapus']);
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

        User::create([
            'nama_lengkap' => $validated['fullName'],
            'email' => strtolower(str_replace(' ', '.', $validated['fullName'])) . '@company.com',
            'nik' => 'NIK-' . time(),
            'password' => bcrypt($validated['password']),
            'jabatan' => $validated['jabatan'],
            'departement' => $validated['department'],
            'status' => strtolower($validated['status']),
            'role_id' => $this->getRoleId($validated['role'])
        ]);

        return redirect()->route('master-data')->with('message', ['type' => 'success', 'text' => 'User berhasil ditambahkan']);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'jabatan' => 'required|string',
            'fullName' => 'required|string',
            'role' => 'required|string',
            'department' => 'required|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        $user->update([
            'nama_lengkap' => $validated['fullName'],
            'jabatan' => $validated['jabatan'],
            'departement' => $validated['department'],
            'status' => strtolower($validated['status']),
            'role_id' => $this->getRoleId($validated['role'])
        ]);

        return redirect()->route('master-data')->with('message', ['type' => 'success', 'text' => 'User berhasil diupdate']);
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->route('master-data')->with('message', ['type' => 'success', 'text' => 'User berhasil dihapus']);
    }

    private function getRoleId($roleName)
    {
        $role = Role::where('role_name', $roleName)->first();
        return $role->id ?? 1;
    }
}