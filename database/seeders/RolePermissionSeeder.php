<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Roles
        $roles = [
            [
                'role_name' => 'Super Admin',
                'description' => 'Administrator dengan akses penuh ke seluruh sistem'
            ],
            [
                'role_name' => 'QC Inspector',
                'description' => 'Role untuk tim QC yang memeriksa barang masuk'
            ],
            [
                'role_name' => 'Warehouse Supervisor',
                'description' => 'Supervisor gudang dengan akses approve dan monitoring'
            ],
            [
                'role_name' => 'Operator Gudang',
                'description' => 'Operator untuk kegiatan operasional gudang sehari-hari'
            ],
            [
                'role_name' => 'Purchasing Staff',
                'description' => 'Staff purchasing untuk mengelola pembelian dan supplier'
            ]
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        // Buat Permissions
        $permissionsData = [
            // Incoming/Receipt
            ['module' => 'incoming', 'action' => 'view', 'description' => 'Lihat data penerimaan barang'],
            ['module' => 'incoming', 'action' => 'create', 'description' => 'Buat penerimaan barang baru'],
            ['module' => 'incoming', 'action' => 'edit', 'description' => 'Edit data penerimaan barang'],
            ['module' => 'incoming', 'action' => 'delete', 'description' => 'Hapus data penerimaan barang'],
            ['module' => 'incoming', 'action' => 'approve', 'description' => 'Approve penerimaan barang'],

            // Quality Control
            ['module' => 'qc', 'action' => 'view', 'description' => 'Lihat data quality control'],
            ['module' => 'qc', 'action' => 'input_qc_result', 'description' => 'Input hasil QC'],
            ['module' => 'qc', 'action' => 'approve', 'description' => 'Approve hasil QC'],
            ['module' => 'qc', 'action' => 'reject', 'description' => 'Reject hasil QC'],

            // Label Karantina
            ['module' => 'label_karantina', 'action' => 'view', 'description' => 'Lihat label karantina'],
            ['module' => 'label_karantina', 'action' => 'cetak_label', 'description' => 'Cetak label karantina'],
            ['module' => 'label_karantina', 'action' => 'release', 'description' => 'Release dari karantina'],
            ['module' => 'label_karantina', 'action' => 'reject', 'description' => 'Reject barang karantina'],

            // Putaway & Transfer Order
            ['module' => 'putaway', 'action' => 'view', 'description' => 'Lihat putaway & TO'],
            ['module' => 'putaway', 'action' => 'kerjakan_to', 'description' => 'Kerjakan transfer order'],
            ['module' => 'putaway', 'action' => 'cetak_slip', 'description' => 'Cetak slip putaway'],

            // Reservation
            ['module' => 'reservation', 'action' => 'view', 'description' => 'Lihat reservation'],
            ['module' => 'reservation', 'action' => 'create_request', 'description' => 'Buat request reservation'],
            ['module' => 'reservation', 'action' => 'approve_request', 'description' => 'Approve request reservation'],
            ['module' => 'reservation', 'action' => 'cetak_form', 'description' => 'Cetak form reservation'],

            // Picking
            ['module' => 'picking', 'action' => 'view', 'description' => 'Lihat picking list'],
            ['module' => 'picking', 'action' => 'kerjakan_picking', 'description' => 'Kerjakan picking'],
            ['module' => 'picking', 'action' => 'cetak_picking_list', 'description' => 'Cetak picking list'],

            // Return
            ['module' => 'return', 'action' => 'view', 'description' => 'Lihat data return'],
            ['module' => 'return', 'action' => 'create_return', 'description' => 'Buat return baru'],
            ['module' => 'return', 'action' => 'approve_return', 'description' => 'Approve return'],
            ['module' => 'return', 'action' => 'cetak_slip', 'description' => 'Cetak slip return'],

            // Central Data - SKU Management
            ['module' => 'central_data', 'action' => 'sku_management_view', 'description' => 'Lihat SKU'],
            ['module' => 'central_data', 'action' => 'sku_management_create', 'description' => 'Buat SKU'],
            ['module' => 'central_data', 'action' => 'sku_management_edit', 'description' => 'Edit SKU'],
            ['module' => 'central_data', 'action' => 'sku_management_delete', 'description' => 'Hapus SKU'],
            ['module' => 'central_data', 'action' => 'sku_management_admin', 'description' => 'Admin SKU'],

            // Central Data - Supplier Management
            ['module' => 'central_data', 'action' => 'supplier_management_view', 'description' => 'Lihat Supplier'],
            ['module' => 'central_data', 'action' => 'supplier_management_create', 'description' => 'Buat Supplier'],
            ['module' => 'central_data', 'action' => 'supplier_management_edit', 'description' => 'Edit Supplier'],
            ['module' => 'central_data', 'action' => 'supplier_management_delete', 'description' => 'Hapus Supplier'],
            ['module' => 'central_data', 'action' => 'supplier_management_admin', 'description' => 'Admin Supplier'],

            // Central Data - Bin Management
            ['module' => 'central_data', 'action' => 'bin_management_view', 'description' => 'Lihat Bin'],
            ['module' => 'central_data', 'action' => 'bin_management_create', 'description' => 'Buat Bin'],
            ['module' => 'central_data', 'action' => 'bin_management_edit', 'description' => 'Edit Bin'],
            ['module' => 'central_data', 'action' => 'bin_management_delete', 'description' => 'Hapus Bin'],
            ['module' => 'central_data', 'action' => 'bin_management_admin', 'description' => 'Admin Bin'],

            // Central Data - User Management
            ['module' => 'central_data', 'action' => 'user_management_view', 'description' => 'Lihat User'],
            ['module' => 'central_data', 'action' => 'user_management_create', 'description' => 'Buat User'],
            ['module' => 'central_data', 'action' => 'user_management_edit', 'description' => 'Edit User'],
            ['module' => 'central_data', 'action' => 'user_management_delete', 'description' => 'Hapus User'],
            ['module' => 'central_data', 'action' => 'user_management_admin', 'description' => 'Admin User'],

            // Central Data - Role Management
            ['module' => 'central_data', 'action' => 'role_management_view', 'description' => 'Lihat Role'],
            ['module' => 'central_data', 'action' => 'role_management_create', 'description' => 'Buat Role'],
            ['module' => 'central_data', 'action' => 'role_management_edit', 'description' => 'Edit Role'],
            ['module' => 'central_data', 'action' => 'role_management_delete', 'description' => 'Hapus Role'],
            ['module' => 'central_data', 'action' => 'role_management_admin', 'description' => 'Admin Role'],
        ];

        foreach ($permissionsData as $permData) {
            Permission::create([
                'permission_name' => "{$permData['module']}.{$permData['action']}",
                'module' => $permData['module'],
                'action' => $permData['action'],
                'description' => $permData['description']
            ]);
        }

        // Assign semua permissions ke Super Admin
        $superAdmin = Role::where('role_name', 'Super Admin')->first();
        $allPermissions = Permission::all();
        $superAdmin->permissions()->attach($allPermissions->pluck('id'));
    }
}