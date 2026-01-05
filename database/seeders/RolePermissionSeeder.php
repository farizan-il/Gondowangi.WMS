<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Truncate Tables
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('role_permissions')->truncate();
        \Illuminate\Support\Facades\DB::table('roles')->truncate();
        \Illuminate\Support\Facades\DB::table('permissions')->truncate();
        \Illuminate\Support\Facades\DB::table('users')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Define Permissions
        $permissionsData = [
            // Dashboard
            ['module' => 'dashboard', 'action' => 'wms', 'description' => 'Akses Dashboard WMS'],
            ['module' => 'it_dashboard', 'action' => 'view', 'description' => 'Akses Dashboard IT'],

            // Incoming (Penerimaan Barang)
            ['module' => 'incoming', 'action' => 'view', 'description' => 'Lihat Penerimaan Barang'],
            ['module' => 'incoming', 'action' => 'create', 'description' => 'Buat Penerimaan Barang'],
            ['module' => 'incoming', 'action' => 'edit', 'description' => 'Edit Penerimaan Barang'],
            ['module' => 'incoming', 'action' => 'delete', 'description' => 'Hapus Penerimaan Barang'],
            ['module' => 'incoming', 'action' => 'approve', 'description' => 'Approve Penerimaan Barang'],

            // Quality Control
            ['module' => 'qc', 'action' => 'view', 'description' => 'Lihat Quality Control'],
            ['module' => 'qc', 'action' => 'create', 'description' => 'Input Hasil QC'], // Mapping create to input
            ['module' => 'qc', 'action' => 'edit', 'description' => 'Edit Hasil QC'],
            ['module' => 'qc', 'action' => 'delete', 'description' => 'Hapus Hasil QC'],
            ['module' => 'qc', 'action' => 'approve', 'description' => 'Approve QC'],
            ['module' => 'qc', 'action' => 'reject', 'description' => 'Reject QC'],

            // OnHand (Inventory)
            ['module' => 'onhand', 'action' => 'view', 'description' => 'Lihat Stock On Hand'],

            // Cycle Count
            ['module' => 'cycle_count', 'action' => 'view', 'description' => 'Lihat Cycle Count'],
            ['module' => 'cycle_count', 'action' => 'create', 'description' => 'Buat/Proses Cycle Count'],
            ['module' => 'cycle_count', 'action' => 'approve', 'description' => 'Approve Cycle Count'],

            // Activity Log
            ['module' => 'activity_log', 'action' => 'view', 'description' => 'Lihat Riwayat Aktivitas'],

            // Reservation
            ['module' => 'reservation', 'action' => 'view', 'description' => 'Lihat Reservasi'],
            ['module' => 'reservation', 'action' => 'create', 'description' => 'Buat Request Reservasi'],
            ['module' => 'reservation', 'action' => 'approve', 'description' => 'Approve Reservasi'],

            // Return
            ['module' => 'return', 'action' => 'view', 'description' => 'Lihat Return'],
            ['module' => 'return', 'action' => 'create', 'description' => 'Buat Return'],
            ['module' => 'return', 'action' => 'approve', 'description' => 'Approve Return'],

            // Outbound / Logistik Ops
            ['module' => 'putaway', 'action' => 'view', 'description' => 'Lihat Putaway'],
            ['module' => 'putaway', 'action' => 'create', 'description' => 'Proses Putaway'], // Create usually means execute here
            ['module' => 'bintobin', 'action' => 'view', 'description' => 'Lihat Bin to Bin'],
            ['module' => 'bintobin', 'action' => 'create', 'description' => 'Proses Bin to Bin'],
            ['module' => 'picking', 'action' => 'view', 'description' => 'Lihat Picking List'],
            ['module' => 'picking', 'action' => 'execute', 'description' => 'Kerjakan Picking'],

            // Master Data
            ['module' => 'master_data', 'action' => 'view', 'description' => 'Lihat Master Data'],
            ['module' => 'master_data', 'action' => 'manage', 'description' => 'Kelola Master Data'],

            // Role Permission
            ['module' => 'role_permission', 'action' => 'view', 'description' => 'Lihat Role Permission'],
            ['module' => 'role_permission', 'action' => 'edit', 'description' => 'Kelola Role Permission'],
        ];

        $permIds = [];
        foreach ($permissionsData as $pd) {
            $p = Permission::create([
                'permission_name' => "{$pd['module']}.{$pd['action']}",
                'module' => $pd['module'],
                'action' => $pd['action'],
                'description' => $pd['description']
            ]);
            $permIds[$p->permission_name] = $p->id;
        }

        // 3. Create Roles & Assign Permissions

        // A. IT (Akses Semua)
        $roleIT = Role::create(['role_name' => 'IT', 'description' => 'IT Department - Full Access']);
        $roleIT->permissions()->attach(Permission::pluck('id'));

        // B. Logistik SPV - Full Access: Dashboard, On Hand, Cycle Count (Approve), Riwayat Aktivitas, Penerimaan Barang, QC (Read Only), Put Away, Bin to Bin, Reservation (Read Only - FOH only), Picking, Return
        $roleSpv = Role::create(['role_name' => 'Logistik SPV', 'description' => 'Supervisor Logistik']);
        $roleSpv->permissions()->attach([
            $permIds['dashboard.wms'],
            $permIds['onhand.view'],
            $permIds['cycle_count.view'], $permIds['cycle_count.approve'],
            $permIds['activity_log.view'],
            $permIds['incoming.view'], $permIds['incoming.create'], $permIds['incoming.edit'], $permIds['incoming.approve'],
            $permIds['qc.view'], // Read only - no create/edit/delete
            $permIds['putaway.view'], $permIds['putaway.create'],
            $permIds['bintobin.view'], $permIds['bintobin.create'],
            $permIds['reservation.view'], // Read only untuk semua, tapi bisa create untuk FOH-RS only
            $permIds['picking.view'], $permIds['picking.execute'],
            $permIds['return.view'], $permIds['return.create'], $permIds['return.approve'],
        ]);

        // C. Logistik Admin - Full Access: Penerimaan Barang Only
        $roleAdm = Role::create(['role_name' => 'Logistik Admin', 'description' => 'Admin Logistik']);
        $roleAdm->permissions()->attach([
            $permIds['incoming.view'], $permIds['incoming.create'], $permIds['incoming.edit'], $permIds['incoming.delete'], $permIds['incoming.approve'],
        ]);

        // D. Logistik Operator - Full Access: Put Away, Bin to Bin, Picking List Only
        $roleOp = Role::create(['role_name' => 'Logistik Operator', 'description' => 'Operator Gudang']);
        $roleOp->permissions()->attach([
            $permIds['putaway.view'], $permIds['putaway.create'],
            $permIds['bintobin.view'], $permIds['bintobin.create'],
            $permIds['picking.view'], $permIds['picking.execute'],
        ]);

        // E. QAC - Full Access: Quality Control, On Hand (Restricted - Only expiring materials)
        $roleQAC = Role::create(['role_name' => 'QAC', 'description' => 'Quality Assurance Control']);
        $roleQAC->permissions()->attach([
            $permIds['qc.view'], $permIds['qc.create'], $permIds['qc.edit'], $permIds['qc.approve'], $permIds['qc.reject'],
            $permIds['onhand.view'], // Restricted in controller to only show materials expiring in 30 days or already expired
        ]);

        // F. Produksi - Full Access: Reservation, Return (Create Only - From Produksi category)
        $roleProd = Role::create(['role_name' => 'Produksi', 'description' => 'Department Produksi']);
        $roleProd->permissions()->attach([
            $permIds['reservation.view'], $permIds['reservation.create'],
            $permIds['return.view'], $permIds['return.create'], // Create only - cannot approve
        ]);

        // 4. Create Users
        $users = [
            ['name' => 'IT User', 'email' => 'it@gondowangi.com', 'role_id' => $roleIT->id],
            ['name' => 'SPV Logistik', 'email' => 'spv.log@gondowangi.com', 'role_id' => $roleSpv->id],
            ['name' => 'Admin Logistik', 'email' => 'adm.log@gondowangi.com', 'role_id' => $roleAdm->id],
            ['name' => 'Operator Logistik', 'email' => 'op.log@gondowangi.com', 'role_id' => $roleOp->id],
            ['name' => 'QAC User', 'email' => 'qac@gondowangi.com', 'role_id' => $roleQAC->id],
            ['name' => 'Produksi User', 'email' => 'prod@gondowangi.com', 'role_id' => $roleProd->id],
        ];

        foreach ($users as $index => $u) {
            \App\Models\User::create([
                'name' => $u['name'],
                'email' => $u['email'],
                'nik' => 'NIK-' . str_pad((string)($index + 1), 5, '0', STR_PAD_LEFT),
                'password' => bcrypt('gondowangi-123'),
                'role_id' => $u['role_id'],
                'status' => 'active'
            ]);
        }
    }
}