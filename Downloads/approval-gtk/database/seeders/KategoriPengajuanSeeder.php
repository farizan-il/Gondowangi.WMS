<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KategoriPengajuan;
use App\Models\FormField;

class KategoriPengajuanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Perjalanan Dinas
        $perdin = KategoriPengajuan::create([
            'nama' => 'Perjalanan Dinas',
            'kode' => 'PERDIN',
            'deskripsi' => 'Pengajuan untuk perjalanan dinas karyawan',
            'icon' => 'map-pin',
            'warna' => 'dc3545',
            'status' => 'aktif'
        ]);

        // Form fields untuk Perjalanan Dinas
        $perdinarFields = [
            [
                'nama_field' => 'tujuan_perjalanan',
                'label' => 'Tujuan Perjalanan',
                'tipe_field' => 'text',
                'urutan' => 1,
                'posisi_row' => 1,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'keperluan',
                'label' => 'Keperluan',
                'tipe_field' => 'textarea',
                'placeholder' => 'Jelaskan keperluan perjalanan dinas',
                'urutan' => 2,
                'posisi_row' => 1,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'tanggal_keberangkatan',
                'label' => 'Tanggal Keberangkatan',
                'tipe_field' => 'date',
                'urutan' => 3,
                'posisi_row' => 2,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'tanggal_kembali',
                'label' => 'Tanggal Kembali',
                'tipe_field' => 'date',
                'urutan' => 4,
                'posisi_row' => 2,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'transportasi',
                'label' => 'Jenis Transportasi',
                'tipe_field' => 'select',
                'opsi' => json_encode(['Pesawat', 'Kereta', 'Bus', 'Mobil Pribadi', 'Mobil Dinas']),
                'urutan' => 5,
                'posisi_row' => 3,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'estimasi_biaya_transport',
                'label' => 'Estimasi Biaya Transportasi',
                'tipe_field' => 'number',
                'placeholder' => '0',
                'urutan' => 6,
                'posisi_row' => 3,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'estimasi_biaya_akomodasi',
                'label' => 'Estimasi Biaya Akomodasi',
                'tipe_field' => 'number',
                'placeholder' => '0',
                'urutan' => 7,
                'posisi_row' => 4,
                'lebar_col' => 6,
                'wajib' => false
            ],
            [
                'nama_field' => 'estimasi_biaya_konsumsi',
                'label' => 'Estimasi Biaya Konsumsi',
                'tipe_field' => 'number',
                'placeholder' => '0',
                'urutan' => 8,
                'posisi_row' => 4,
                'lebar_col' => 6,
                'wajib' => false
            ]
        ];

        foreach ($perdinarFields as $field) {
            $field['kategori_pengajuan_id'] = $perdin->id;
            $field['status'] = 'aktif';
            FormField::create($field);
        }

        // 2. Reimbursement
        $reimburse = KategoriPengajuan::create([
            'nama' => 'Reimbursement',
            'kode' => 'REIMB',
            'deskripsi' => 'Penggantian biaya yang telah dikeluarkan',
            'icon' => 'credit-card',
            'warna' => '28a745',
            'status' => 'aktif'
        ]);

        // Form fields untuk Reimbursement
        $reimburseFields = [
            [
                'nama_field' => 'jenis_pengeluaran',
                'label' => 'Jenis Pengeluaran',
                'tipe_field' => 'select',
                'opsi' => json_encode(['Transportasi', 'Konsumsi', 'Akomodasi', 'Komunikasi', 'Operasional', 'Lain-lain']),
                'urutan' => 1,
                'posisi_row' => 1,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'tanggal_pengeluaran',
                'label' => 'Tanggal Pengeluaran',
                'tipe_field' => 'date',
                'urutan' => 2,
                'posisi_row' => 1,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'detail_pengeluaran',
                'label' => 'Detail Pengeluaran',
                'tipe_field' => 'textarea',
                'placeholder' => 'Jelaskan detail pengeluaran yang akan di-reimburse',
                'urutan' => 3,
                'posisi_row' => 2,
                'lebar_col' => 12,
                'wajib' => true
            ],
            [
                'nama_field' => 'bukti_pembayaran',
                'label' => 'Bukti Pembayaran Tersedia',
                'tipe_field' => 'radio',
                'opsi' => json_encode(['Ya, Lengkap', 'Ya, Sebagian', 'Tidak Ada']),
                'urutan' => 4,
                'posisi_row' => 3,
                'lebar_col' => 12,
                'wajib' => true
            ]
        ];

        foreach ($reimburseFields as $field) {
            $field['kategori_pengajuan_id'] = $reimburse->id;
            $field['status'] = 'aktif';
            FormField::create($field);
        }

        // 3. Purchase Request
        $purchase = KategoriPengajuan::create([
            'nama' => 'Purchase Request',
            'kode' => 'PR',
            'deskripsi' => 'Permintaan pembelian barang/jasa',
            'icon' => 'shopping-cart',
            'warna' => '007bff',
            'status' => 'aktif'
        ]);

        // Form fields untuk Purchase Request
        $purchaseFields = [
            [
                'nama_field' => 'jenis_pembelian',
                'label' => 'Jenis Pembelian',
                'tipe_field' => 'select',
                'opsi' => json_encode(['Barang', 'Jasa', 'Software', 'Hardware', 'Peralatan Kantor']),
                'urutan' => 1,
                'posisi_row' => 1,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'kategori_barang',
                'label' => 'Kategori Barang/Jasa',
                'tipe_field' => 'text',
                'placeholder' => 'Contoh: ATK, Furniture, Konsultan IT',
                'urutan' => 2,
                'posisi_row' => 1,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'spesifikasi',
                'label' => 'Spesifikasi Detail',
                'tipe_field' => 'textarea',
                'placeholder' => 'Jelaskan spesifikasi lengkap barang/jasa yang dibutuhkan',
                'urutan' => 3,
                'posisi_row' => 2,
                'lebar_col' => 12,
                'wajib' => true
            ],
            [
                'nama_field' => 'kuantitas',
                'label' => 'Kuantitas',
                'tipe_field' => 'number',
                'placeholder' => '1',
                'urutan' => 4,
                'posisi_row' => 3,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'vendor_usulan',
                'label' => 'Vendor Usulan (Opsional)',
                'tipe_field' => 'text',
                'placeholder' => 'Nama vendor yang diusulkan',
                'urutan' => 5,
                'posisi_row' => 3,
                'lebar_col' => 6,
                'wajib' => false
            ],
            [
                'nama_field' => 'justifikasi',
                'label' => 'Justifikasi Kebutuhan',
                'tipe_field' => 'textarea',
                'placeholder' => 'Jelaskan mengapa barang/jasa ini diperlukan',
                'urutan' => 6,
                'posisi_row' => 4,
                'lebar_col' => 12,
                'wajib' => true
            ]
        ];

        foreach ($purchaseFields as $field) {
            $field['kategori_pengajuan_id'] = $purchase->id;
            $field['status'] = 'aktif';
            FormField::create($field);
        }

        // 4. Training Request
        $training = KategoriPengajuan::create([
            'nama' => 'Training Request',
            'kode' => 'TRAIN',
            'deskripsi' => 'Pengajuan untuk mengikuti training/pelatihan',
            'icon' => 'book-open',
            'warna' => 'ffc107',
            'status' => 'aktif'
        ]);

        // Form fields untuk Training
        $trainingFields = [
            [
                'nama_field' => 'nama_training',
                'label' => 'Nama Training/Pelatihan',
                'tipe_field' => 'text',
                'urutan' => 1,
                'posisi_row' => 1,
                'lebar_col' => 12,
                'wajib' => true
            ],
            [
                'nama_field' => 'penyelenggara',
                'label' => 'Penyelenggara',
                'tipe_field' => 'text',
                'urutan' => 2,
                'posisi_row' => 2,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'jenis_training',
                'label' => 'Jenis Training',
                'tipe_field' => 'select',
                'opsi' => json_encode(['Online', 'Offline', 'Hybrid']),
                'urutan' => 3,
                'posisi_row' => 2,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'tanggal_mulai',
                'label' => 'Tanggal Mulai',
                'tipe_field' => 'date',
                'urutan' => 4,
                'posisi_row' => 3,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'tanggal_selesai',
                'label' => 'Tanggal Selesai',
                'tipe_field' => 'date',
                'urutan' => 5,
                'posisi_row' => 3,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'manfaat_training',
                'label' => 'Manfaat Training untuk Pekerjaan',
                'tipe_field' => 'textarea',
                'placeholder' => 'Jelaskan bagaimana training ini akan membantu pekerjaan Anda',
                'urutan' => 6,
                'posisi_row' => 4,
                'lebar_col' => 12,
                'wajib' => true
            ],
            [
                'nama_field' => 'rencana_sharing',
                'label' => 'Rencana Knowledge Sharing',
                'tipe_field' => 'textarea',
                'placeholder' => 'Bagaimana Anda akan membagikan ilmu yang didapat?',
                'urutan' => 7,
                'posisi_row' => 5,
                'lebar_col' => 12,
                'wajib' => false
            ]
        ];

        foreach ($trainingFields as $field) {
            $field['kategori_pengajuan_id'] = $training->id;
            $field['status'] = 'aktif';
            FormField::create($field);
        }

        // 5. Cuti/Leave Request
        $leave = KategoriPengajuan::create([
            'nama' => 'Pengajuan Cuti',
            'kode' => 'LEAVE',
            'deskripsi' => 'Pengajuan cuti karyawan',
            'icon' => 'calendar',
            'warna' => '17a2b8',
            'status' => 'aktif'
        ]);

        // Form fields untuk Leave
        $leaveFields = [
            [
                'nama_field' => 'jenis_cuti',
                'label' => 'Jenis Cuti',
                'tipe_field' => 'select',
                'opsi' => json_encode(['Cuti Tahunan', 'Cuti Sakit', 'Cuti Melahirkan', 'Cuti Menikah', 'Cuti Khusus']),
                'urutan' => 1,
                'posisi_row' => 1,
                'lebar_col' => 12,
                'wajib' => true
            ],
            [
                'nama_field' => 'tanggal_mulai_cuti',
                'label' => 'Tanggal Mulai Cuti',
                'tipe_field' => 'date',
                'urutan' => 2,
                'posisi_row' => 2,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'tanggal_selesai_cuti',
                'label' => 'Tanggal Selesai Cuti',
                'tipe_field' => 'date',
                'urutan' => 3,
                'posisi_row' => 2,
                'lebar_col' => 6,
                'wajib' => true
            ],
            [
                'nama_field' => 'alasan_cuti',
                'label' => 'Alasan Cuti',
                'tipe_field' => 'textarea',
                'placeholder' => 'Jelaskan alasan mengambil cuti',
                'urutan' => 4,
                'posisi_row' => 3,
                'lebar_col' => 12,
                'wajib' => true
            ],
            [
                'nama_field' => 'pic_pengganti',
                'label' => 'PIC Pengganti Selama Cuti',
                'tipe_field' => 'text',
                'placeholder' => 'Nama karyawan yang akan menggantikan',
                'urutan' => 5,
                'posisi_row' => 4,
                'lebar_col' => 12,
                'wajib' => false
            ]
        ];

        foreach ($leaveFields as $field) {
            $field['kategori_pengajuan_id'] = $leave->id;
            $field['status'] = 'aktif';
            FormField::create($field);
        }
    }
}
