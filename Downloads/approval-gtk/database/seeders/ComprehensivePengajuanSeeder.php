<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\RoleLevel;
use App\Models\Karyawan;
use App\Models\KategoriPengajuan;
use App\Models\FormField;
use App\Models\FlowApproval;
use App\Models\Pengajuan;
use App\Models\DetailPengajuan;
use App\Models\ProgressApproval;
use App\Models\HistoryPengajuan;
use App\Models\DelegasiApproval;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ComprehensivePengajuanSeeder extends Seeder
{
    public function run()
    {
        // Reset semua AUTO_INCREMENT ke 1
        // $this->resetAutoIncrement();

        // Buat Department dengan ID eksplisit
        $departments = [
            ['id' => 1, 'nama' => 'IT', 'kode' => 'IT', 'deskripsi' => 'Information Technology', 'status' => 'aktif'],
            ['id' => 2, 'nama' => 'Finance', 'kode' => 'FIN', 'deskripsi' => 'Finance Department', 'status' => 'aktif'],
            ['id' => 3, 'nama' => 'HR', 'kode' => 'HR', 'deskripsi' => 'Human Resources', 'status' => 'aktif'],
            ['id' => 4, 'nama' => 'Operations', 'kode' => 'OPS', 'deskripsi' => 'Operations Department', 'status' => 'aktif'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // Buat Role Level dengan ID eksplisit
        $roleLevels = [
            ['id' => 1, 'nama' => 'Staff', 'deskripsi' => 'Staff Level'],
            ['id' => 2, 'nama' => 'Supervisor', 'deskripsi' => 'Supervisor Level'],
            ['id' => 3, 'nama' => 'Manager', 'deskripsi' => 'Manager Level'],
            ['id' => 4, 'nama' => 'Director', 'deskripsi' => 'Director Level'],
        ];

        foreach ($roleLevels as $role) {
            RoleLevel::create($role);
        }

        // Buat Karyawan dengan ID eksplisit
        $karyawans = [
            [
                'id' => 1,
                'nik' => 'EMP001',
                'nama' => 'John Doe',
                'email' => 'john@company.com',
                'password' => Hash::make('password'),
                'department_id' => 1, // IT Department
                'jabatan' => 'Staff IT',
                'atasan_id' => null, // akan di-update nanti
                'status' => 'aktif'
            ],
            [
                'id' => 2,
                'nik' => 'EMP002',
                'nama' => 'Jane Smith',
                'email' => 'jane@company.com',
                'password' => Hash::make('password'),
                'department_id' => 1, // IT Department
                'jabatan' => 'IT Manager',
                'atasan_id' => null,
                'status' => 'aktif'
            ],
            [
                'id' => 3,
                'nik' => 'EMP003',
                'nama' => 'Bob Wilson',
                'email' => 'bob@company.com',
                'password' => Hash::make('password'),
                'department_id' => 2, // Finance Department
                'jabatan' => 'Finance Staff',
                'atasan_id' => null, // akan di-update nanti
                'status' => 'aktif'
            ],
            [
                'id' => 4,
                'nik' => 'EMP004',
                'nama' => 'Alice Brown',
                'email' => 'alice@company.com',
                'password' => Hash::make('password'),
                'department_id' => 2, // Finance Department
                'jabatan' => 'Finance Manager',
                'atasan_id' => null,
                'status' => 'aktif'
            ],
            [
                'id' => 5,
                'nik' => 'EMP005',
                'nama' => 'Charlie Davis',
                'email' => 'charlie@company.com',
                'password' => Hash::make('password'),
                'department_id' => 3, // HR Department
                'jabatan' => 'HR Manager',
                'atasan_id' => null,
                'status' => 'aktif'
            ],
        ];

        foreach ($karyawans as $karyawan) {
            Karyawan::create($karyawan);
        }

        // Update atasan_id dengan relasi yang benar
        Karyawan::where('id', 1)->update(['atasan_id' => 2]); // John Doe -> Jane Smith (IT Manager)
        Karyawan::where('id', 3)->update(['atasan_id' => 4]); // Bob Wilson -> Alice Brown (Finance Manager)

        // Buat Kategori Pengajuan dengan ID eksplisit
        $kategoriPengajuans = [
            [
                'id' => 1,
                'nama' => 'Pengajuan Cuti',
                'kode' => 'CUT',
                'deskripsi' => 'Pengajuan untuk cuti karyawan',
                'icon' => 'calendar',
                'warna' => '#4CAF50',
                'status' => 'aktif'
            ],
            [
                'id' => 2,
                'nama' => 'Reimbursement',
                'kode' => 'RMB',
                'deskripsi' => 'Pengajuan penggantian biaya',
                'icon' => 'money',
                'warna' => '#2196F3',
                'status' => 'aktif'
            ],
            [
                'id' => 3,
                'nama' => 'Purchase Request',
                'kode' => 'PR',
                'deskripsi' => 'Pengajuan pembelian barang/jasa',
                'icon' => 'shopping-cart',
                'warna' => '#FF9800',
                'status' => 'aktif'
            ],
            [
                'id' => 4,
                'nama' => 'Training Request',
                'kode' => 'TRN',
                'deskripsi' => 'Pengajuan pelatihan karyawan',
                'icon' => 'book',
                'warna' => '#9C27B0',
                'status' => 'aktif'
            ],
        ];

        foreach ($kategoriPengajuans as $kategori) {
            KategoriPengajuan::create($kategori);
        }

        // Buat Form Fields dengan ID eksplisit
        $formFields = [
            [
                'id' => 1,
                'kategori_pengajuan_id' => 1, // Pengajuan Cuti
                'nama_field' => 'jenis_cuti',
                'label' => 'Jenis Cuti',
                'tipe_field' => 'select',
                'placeholder' => 'Pilih jenis cuti',
                'validasi' => json_encode(['required']),
                'opsi' => json_encode(['Cuti Tahunan', 'Cuti Sakit', 'Cuti Melahirkan']),
                'urutan' => 1,
                'posisi_row' => 1,
                'posisi_col' => 1,
                'lebar_col' => 6,
                'wajib' => true,
                'status' => 'aktif'
            ],
            [
                'id' => 2,
                'kategori_pengajuan_id' => 1, // Pengajuan Cuti
                'nama_field' => 'tanggal_mulai_cuti',
                'label' => 'Tanggal Mulai Cuti',
                'tipe_field' => 'date',
                'placeholder' => 'Pilih tanggal mulai',
                'validasi' => json_encode(['required', 'date']),
                'opsi' => null,
                'urutan' => 2,
                'posisi_row' => 1,
                'posisi_col' => 2,
                'lebar_col' => 6,
                'wajib' => true,
                'status' => 'aktif'
            ],
            [
                'id' => 3,
                'kategori_pengajuan_id' => 2, // Reimbursement
                'nama_field' => 'kategori_biaya',
                'label' => 'Kategori Biaya',
                'tipe_field' => 'select',
                'placeholder' => 'Pilih kategori biaya',
                'validasi' => json_encode(['required']),
                'opsi' => json_encode(['Transport', 'Makan', 'Akomodasi', 'Komunikasi']),
                'urutan' => 1,
                'posisi_row' => 1,
                'posisi_col' => 1,
                'lebar_col' => 6,
                'wajib' => true,
                'status' => 'aktif'
            ],
        ];

        foreach ($formFields as $field) {
            FormField::create($field);
        }

        // Buat Flow Approval dengan ID eksplisit
        $flowApprovals = [
            [
                'id' => 1,
                'kategori_pengajuan_id' => 1, // Pengajuan Cuti
                'department_id' => 1, // IT Department
                'urutan' => 1,
                'role_level_id' => 2, // Supervisor
                'nama_step' => 'Approval Supervisor',
                'deskripsi' => 'Persetujuan dari supervisor langsung',
                'status' => 'aktif'
            ],
            [
                'id' => 2,
                'kategori_pengajuan_id' => 1, // Pengajuan Cuti
                'department_id' => 1, // IT Department
                'urutan' => 2,
                'role_level_id' => 3, // Manager
                'nama_step' => 'Approval HR Manager',
                'deskripsi' => 'Persetujuan dari HR Manager',
                'status' => 'aktif'
            ],
            [
                'id' => 3,
                'kategori_pengajuan_id' => 2, // Reimbursement
                'department_id' => 2, // Finance Department
                'urutan' => 1,
                'role_level_id' => 3, // Manager
                'nama_step' => 'Approval Manager',
                'deskripsi' => 'Persetujuan dari manager departemen',
                'status' => 'aktif'
            ],
        ];

        foreach ($flowApprovals as $flow) {
            FlowApproval::create($flow);
        }

        // Buat Delegasi Approval dengan ID eksplisit
        DelegasiApproval::create([
            'id' => 1,
            'pemberi_id' => 2, // Jane Smith
            'penerima_id' => 5, // Charlie Davis
            'kategori_pengajuan_id' => 1, // Pengajuan Cuti
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(7),
            'alasan' => 'Sedang cuti, delegasi approval ke HR Manager',
            'status' => 'aktif'
        ]);

        // Buat Pengajuan dengan ID eksplisit
        $pengajuans = [
            [
                'id' => 1,
                'nomor_pengajuan' => 'CUT/202412/0001',
                'kategori_pengajuan_id' => 1, // Pengajuan Cuti
                'requester_id' => 1, // John Doe
                'judul' => 'Pengajuan Cuti Tahunan',
                'deskripsi' => 'Pengajuan cuti tahunan untuk liburan keluarga',
                'nominal_pengajuan' => 0.00,
                'mata_uang' => 'IDR',
                'tanggal_pengajuan' => now(),
                'tanggal_kebutuhan' => now()->addDays(5),
                'status_pengajuan' => 'pending',
                'current_step' => 1,
                'total_step' => 2,
                'catatan_requester' => 'Mohon persetujuan untuk cuti tahunan',
                'file_pendukung' => null,
                'is_settlement_required' => false,
                'settlement_id' => null
            ],
            [
                'id' => 2,
                'nomor_pengajuan' => 'RMB/202412/0001',
                'kategori_pengajuan_id' => 2, // Reimbursement
                'requester_id' => 3, // Bob Wilson
                'judul' => 'Reimbursement Transport Meeting',
                'deskripsi' => 'Penggantian biaya transport untuk meeting dengan klien',
                'nominal_pengajuan' => 250000.00,
                'mata_uang' => 'IDR',
                'tanggal_pengajuan' => now()->subDays(1),
                'tanggal_kebutuhan' => now()->addDays(3),
                'status_pengajuan' => 'approved',
                'current_step' => 1,
                'total_step' => 1,
                'catatan_requester' => 'Meeting dengan klien ABC untuk project baru',
                'file_pendukung' => json_encode(['receipt_transport.pdf']),
                'is_settlement_required' => true,
                'settlement_id' => null
            ],
            [
                'id' => 3,
                'nomor_pengajuan' => 'PR/202412/0001',
                'kategori_pengajuan_id' => 3, // Purchase Request
                'requester_id' => 1, // John Doe
                'judul' => 'Purchase Request Laptop',
                'deskripsi' => 'Pengajuan pembelian laptop untuk development',
                'nominal_pengajuan' => 15000000.00,
                'mata_uang' => 'IDR',
                'tanggal_pengajuan' => now()->subDays(2),
                'tanggal_kebutuhan' => now()->addDays(10),
                'status_pengajuan' => 'rejected',
                'current_step' => 1,
                'total_step' => 2,
                'catatan_requester' => 'Laptop lama sudah tidak memadai untuk development',
                'file_pendukung' => json_encode(['quotation_laptop.pdf', 'specs_requirement.pdf']),
                'is_settlement_required' => false,
                'settlement_id' => null
            ],
            [
                'id' => 4,
                'nomor_pengajuan' => 'TRN/202412/0001',
                'kategori_pengajuan_id' => 4, // Training Request
                'requester_id' => 3, // Bob Wilson
                'judul' => 'Training Laravel Advanced',
                'deskripsi' => 'Pengajuan training Laravel untuk meningkatkan skill development',
                'nominal_pengajuan' => 5000000.00,
                'mata_uang' => 'IDR',
                'tanggal_pengajuan' => now()->subDays(3),
                'tanggal_kebutuhan' => now()->addDays(14),
                'status_pengajuan' => 'revision',
                'current_step' => 1,
                'total_step' => 2,
                'catatan_requester' => 'Training untuk meningkatkan kemampuan development team',
                'file_pendukung' => json_encode(['training_proposal.pdf']),
                'is_settlement_required' => false,
                'settlement_id' => null
            ],
        ];

        foreach ($pengajuans as $pengajuan) {
            Pengajuan::create($pengajuan);
        }

        // Buat Detail Pengajuan dengan ID eksplisit
        $detailPengajuans = [
            [
                'id' => 1,
                'pengajuan_id' => 1, // Pengajuan Cuti John Doe
                'form_field_id' => 1, // jenis_cuti
                'nilai' => 'Cuti Tahunan'
            ],
            [
                'id' => 2,
                'pengajuan_id' => 1, // Pengajuan Cuti John Doe
                'form_field_id' => 2, // tanggal_mulai_cuti
                'nilai' => now()->addDays(5)->format('Y-m-d')
            ],
            [
                'id' => 3,
                'pengajuan_id' => 2, // Reimbursement Bob Wilson
                'form_field_id' => 3, // kategori_biaya
                'nilai' => 'Transport'
            ],
        ];

        foreach ($detailPengajuans as $detail) {
            DetailPengajuan::create($detail);
        }

        // Buat Progress Approval dengan ID eksplisit
        $progressApprovals = [
            [
                'id' => 1,
                'pengajuan_id' => 1, // Pengajuan Cuti John Doe
                'flow_approval_id' => 1, // Approval Supervisor IT
                'approver_id' => 2, // Jane Smith (IT Manager)
                'status' => 'pending',
                'tanggal_approval' => null,
                'catatan' => null,
                'delegasi_dari' => null
            ],
            [
                'id' => 2,
                'pengajuan_id' => 1, // Pengajuan Cuti John Doe
                'flow_approval_id' => 2, // Approval HR Manager
                'approver_id' => 5, // Charlie Davis (HR Manager)
                'status' => 'pending',
                'tanggal_approval' => null,
                'catatan' => null,
                'delegasi_dari' => null
            ],
            [
                'id' => 3,
                'pengajuan_id' => 2, // Reimbursement Bob Wilson
                'flow_approval_id' => 3, // Approval Finance Manager
                'approver_id' => 4, // Alice Brown (Finance Manager)
                'status' => 'approved',
                'tanggal_approval' => now()->subHours(2),
                'catatan' => 'Approved, dokumen lengkap',
                'delegasi_dari' => null
            ],
            [
                'id' => 4,
                'pengajuan_id' => 3, // Purchase Request John Doe
                'flow_approval_id' => 1, // Approval Supervisor IT
                'approver_id' => 2, // Jane Smith (IT Manager)
                'status' => 'rejected',
                'tanggal_approval' => now()->subHours(4),
                'catatan' => 'Budget tidak mencukupi untuk pembelian ini',
                'delegasi_dari' => null
            ],
            [
                'id' => 5,
                'pengajuan_id' => 4, // Training Request Bob Wilson
                'flow_approval_id' => 1, // Approval Supervisor IT
                'approver_id' => 2, // Jane Smith (IT Manager)
                'status' => 'revision',
                'tanggal_approval' => now()->subHours(6),
                'catatan' => 'Perlu penjelasan lebih detail tentang materi training',
                'delegasi_dari' => null
            ],
        ];

        foreach ($progressApprovals as $progress) {
            ProgressApproval::create($progress);
        }

        // Buat History Pengajuan dengan ID eksplisit
        $historyPengajuans = [
            [
                'id' => 1,
                'pengajuan_id' => 1, // Pengajuan Cuti John Doe
                'step_ke' => 1,
                'approver_id' => 1, // John Doe (submitter)
                'aksi' => 'submit',
                'status_sebelum' => 'draft',
                'status_sesudah' => 'pending',
                'catatan' => 'Pengajuan cuti tahunan disubmit',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ],
            [
                'id' => 2,
                'pengajuan_id' => 2, // Reimbursement Bob Wilson
                'step_ke' => 1,
                'approver_id' => 3, // Bob Wilson (submitter)
                'aksi' => 'submit',
                'status_sebelum' => 'draft',
                'status_sesudah' => 'pending',
                'catatan' => 'Pengajuan reimbursement disubmit',
                'ip_address' => '192.168.1.101',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ],
            [
                'id' => 3,
                'pengajuan_id' => 2, // Reimbursement Bob Wilson
                'step_ke' => 1,
                'approver_id' => 4, // Alice Brown (Finance Manager)
                'aksi' => 'approve',
                'status_sebelum' => 'pending',
                'status_sesudah' => 'approved',
                'catatan' => 'Approved, dokumen lengkap',
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ],
            [
                'id' => 4,
                'pengajuan_id' => 3, // Purchase Request John Doe
                'step_ke' => 1,
                'approver_id' => 1, // John Doe (submitter)
                'aksi' => 'submit',
                'status_sebelum' => 'draft',
                'status_sesudah' => 'pending',
                'catatan' => 'Pengajuan purchase request disubmit',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ],
            [
                'id' => 5,
                'pengajuan_id' => 3, // Purchase Request John Doe
                'step_ke' => 1,
                'approver_id' => 2, // Jane Smith (IT Manager)
                'aksi' => 'reject',
                'status_sebelum' => 'pending',
                'status_sesudah' => 'rejected',
                'catatan' => 'Budget tidak mencukupi untuk pembelian ini',
                'ip_address' => '192.168.1.103',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ],
            [
                'id' => 6,
                'pengajuan_id' => 4, // Training Request Bob Wilson
                'step_ke' => 1,
                'approver_id' => 3, // Bob Wilson (submitter)
                'aksi' => 'submit',
                'status_sebelum' => 'draft',
                'status_sesudah' => 'pending',
                'catatan' => 'Pengajuan training disubmit',
                'ip_address' => '192.168.1.101',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ],
            [
                'id' => 7,
                'pengajuan_id' => 4, // Training Request Bob Wilson
                'step_ke' => 1,
                'approver_id' => 2, // Jane Smith (IT Manager)
                'aksi' => 'revise',
                'status_sebelum' => 'pending',
                'status_sesudah' => 'revision',
                'catatan' => 'Perlu penjelasan lebih detail tentang materi training',
                'ip_address' => '192.168.1.103',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ],
        ];

        foreach ($historyPengajuans as $history) {
            HistoryPengajuan::create($history);
        }

        echo "Seeder berhasil dijalankan dengan ID yang konsisten!\n";
        echo "Data yang dibuat:\n";
        echo "- 4 Department (ID: 1-4)\n";
        echo "- 4 Role Level (ID: 1-4)\n";
        echo "- 5 Karyawan (ID: 1-5)\n";
        echo "- 4 Kategori Pengajuan (ID: 1-4)\n";
        echo "- 3 Form Field (ID: 1-3)\n";
        echo "- 3 Flow Approval (ID: 1-3)\n";
        echo "- 1 Delegasi Approval (ID: 1)\n";
        echo "- 4 Pengajuan (ID: 1-4)\n";
        echo "- 3 Detail Pengajuan (ID: 1-3)\n";
        echo "- 5 Progress Approval (ID: 1-5)\n";
        echo "- 7 History Pengajuan (ID: 1-7)\n";
        echo "\nSemua relasi antar tabel sudah konsisten dengan ID yang eksplisit.\n";
    }

    /**
     * Reset AUTO_INCREMENT untuk semua tabel yang terlibat
     */
    // private function resetAutoIncrement()
    // {
    //     $tables = [
    //         'Department',
    //         'RoleLevel', 
    //         'Karyawan',
    //         'KategoriPengajuan',
    //         'FormField',
    //         'FlowApproval',
    //         'DelegasiApproval',
    //         'Pengajuan',
    //         'DetailPengajuan',
    //         'ProgressApprovals',
    //         'HistoryPengajuan'
    //     ];

    //     foreach ($tables as $table) {
    //         // Truncate untuk membersihkan data dan reset AUTO_INCREMENT
    //         DB::statement("TRUNCATE TABLE `$table`");
    //     }

    //     echo "Semua tabel telah direset AUTO_INCREMENT ke 1\n";
    // }
}
