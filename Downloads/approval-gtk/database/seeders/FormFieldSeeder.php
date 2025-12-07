<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FormField;
use App\Models\KategoriPengajuan;

class FormFieldSeeder extends Seeder
{
    public function run()
    {
        // Pastikan kategori "Perjalanan Dinas" sudah ada
        $kategoriPerdin = KategoriPengajuan::where('nama', 'Perjalanan Dinas')->first();
        
        if (!$kategoriPerdin) {
            // Jika belum ada, buat kategori perjalanan dinas
            $kategoriPerdin = KategoriPengajuan::create([
                'nama' => 'Perjalanan Dinas',
                'kode' => 'PERDIN',
                'deskripsi' => 'Pengajuan biaya perjalanan dinas',
                'icon' => 'map-pin',
                'warna' => '34,197,94', // Green color
                'status' => 'aktif'
            ]);
        }

        // Data form fields untuk perjalanan dinas
        $formFields = [
            
            // Transportasi - Udara
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'transportasi_udara',
                'label' => 'Transportasi - Udara',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 1,
                'posisi_row' => 1,
                'posisi_col' => 1,
                'lebar_col' => 3,
                'wajib' => false,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'transportasi_darat',
                'label' => 'Transportasi - Darat',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 2,
                'posisi_row' => 1,
                'posisi_col' => 2,
                'lebar_col' => 3,
                'wajib' => false,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'airport_tax',
                'label' => 'Airport Tax',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 3,
                'posisi_row' => 1,
                'posisi_col' => 3,
                'lebar_col' => 3,
                'wajib' => false,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'total_transportasi',
                'label' => 'Total Transportasi',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 4,
                'posisi_row' => 1,
                'posisi_col' => 4,
                'lebar_col' => 3,
                'wajib' => false,
                'status' => 'aktif'
            ],
            
            // Hotel
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'hotel_jumlah_hari',
                'label' => 'Hotel - Jumlah Hari',
                'tipe_field' => 'number',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 5,
                'posisi_row' => 2,
                'posisi_col' => 1,
                'lebar_col' => 6,
                'wajib' => false,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'hotel_total',
                'label' => 'Total Hotel',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 6,
                'posisi_row' => 2,
                'posisi_col' => 2,
                'lebar_col' => 6,
                'wajib' => false,
                'status' => 'aktif'
            ],
            
            // Makan
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'makan_total',
                'label' => 'Makan',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 7,
                'posisi_row' => 3,
                'posisi_col' => 1,
                'lebar_col' => 12,
                'wajib' => false,
                'status' => 'aktif'
            ],
            
            // Uang Saku
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'uang_saku',
                'label' => 'Uang Saku',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 8,
                'posisi_row' => 4,
                'posisi_col' => 1,
                'lebar_col' => 12,
                'wajib' => false,
                'status' => 'aktif'
            ],
            
            // Telephone & Fax
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'telephone_fax',
                'label' => 'Telephone & Fax',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 9,
                'posisi_row' => 5,
                'posisi_col' => 1,
                'lebar_col' => 12,
                'wajib' => false,
                'status' => 'aktif'
            ],
            
            // Entertainment
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'entertainment',
                'label' => 'Entertainment',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 10,
                'posisi_row' => 6,
                'posisi_col' => 1,
                'lebar_col' => 12,
                'wajib' => false,
                'status' => 'aktif'
            ],
            
            // Dokumentasi
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'dokumentasi',
                'label' => 'Dokumentasi',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 11,
                'posisi_row' => 7,
                'posisi_col' => 1,
                'lebar_col' => 12,
                'wajib' => false,
                'status' => 'aktif'
            ],
            
            // Lain-lain
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'lain_lain',
                'label' => 'Lain-lain',
                'tipe_field' => 'textarea',
                'placeholder' => 'Sebutkan kebutuhan lain-lain...',
                'validasi' => null,
                'opsi' => null,
                'urutan' => 12,
                'posisi_row' => 8,
                'posisi_col' => 1,
                'lebar_col' => 8,
                'wajib' => false,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'lain_lain_nominal',
                'label' => 'Nominal Lain-lain',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 13,
                'posisi_row' => 8,
                'posisi_col' => 2,
                'lebar_col' => 4,
                'wajib' => false,
                'status' => 'aktif'
            ],
            
            // Perjalanan 1
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'perjalanan_1_tanggal',
                'label' => 'Perjalanan 1 - Tanggal',
                'tipe_field' => 'date',
                'placeholder' => '',
                'validasi' => null,
                'opsi' => null,
                'urutan' => 14,
                'posisi_row' => 9,
                'posisi_col' => 1,
                'lebar_col' => 3,
                'wajib' => true,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'perjalanan_1_daerah',
                'label' => 'Perjalanan 1 - Daerah',
                'tipe_field' => 'text',
                'placeholder' => 'Contoh: Semarang',
                'validasi' => null,
                'opsi' => null,
                'urutan' => 15,
                'posisi_row' => 9,
                'posisi_col' => 2,
                'lebar_col' => 3,
                'wajib' => true,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'perjalanan_1_sales_rata',
                'label' => 'Sales Rata-rata Per Bulan',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 16,
                'posisi_row' => 9,
                'posisi_col' => 3,
                'lebar_col' => 3,
                'wajib' => false,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'perjalanan_1_estimasi_sales',
                'label' => 'Estimasi Sales',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 17,
                'posisi_row' => 9,
                'posisi_col' => 4,
                'lebar_col' => 3,
                'wajib' => false,
                'status' => 'aktif'
            ],
            
            // Tambahan field untuk perjalanan lainnya (2-6)
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'perjalanan_2_tanggal',
                'label' => 'Perjalanan 2 - Tanggal',
                'tipe_field' => 'date',
                'placeholder' => '',
                'validasi' => null,
                'opsi' => null,
                'urutan' => 18,
                'posisi_row' => 10,
                'posisi_col' => 1,
                'lebar_col' => 3,
                'wajib' => false,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'perjalanan_2_daerah',
                'label' => 'Perjalanan 2 - Daerah',
                'tipe_field' => 'text',
                'placeholder' => 'Daerah tujuan',
                'validasi' => null,
                'opsi' => null,
                'urutan' => 19,
                'posisi_row' => 10,
                'posisi_col' => 2,
                'lebar_col' => 3,
                'wajib' => false,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'perjalanan_2_sales_rata',
                'label' => 'Sales Rata-rata Per Bulan',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 20,
                'posisi_row' => 10,
                'posisi_col' => 3,
                'lebar_col' => 3,
                'wajib' => false,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'perjalanan_2_estimasi_sales',
                'label' => 'Estimasi Sales',
                'tipe_field' => 'currency',
                'placeholder' => '0',
                'validasi' => json_encode(['numeric', 'min:0']),
                'opsi' => null,
                'urutan' => 21,
                'posisi_row' => 10,
                'posisi_col' => 4,
                'lebar_col' => 3,
                'wajib' => false,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'perjalanan_2_daerah',
                'label' => 'Perjalanan 2 - Daerah',
                'tipe_field' => 'text',
                'placeholder' => 'Daerah tujuan',
                'validasi' => null,
                'opsi' => null,
                'urutan' => 21,
                'posisi_row' => 12,
                'posisi_col' => 2,
                'lebar_col' => 3,
                'wajib' => false,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'perjalanan_2_sales_rata',
                'label' => 'Sales Rata-rata Per Bulan',
                'tipe_field' => 'number',
                'placeholder' => '0',
                'validasi' => ['numeric', 'min:0'],
                'opsi' => null,
                'urutan' => 22,
                'posisi_row' => 12,
                'posisi_col' => 3,
                'lebar_col' => 3,
                'wajib' => false,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'perjalanan_2_estimasi_sales',
                'label' => 'Estimasi Sales',
                'tipe_field' => 'number',
                'placeholder' => '0',
                'validasi' => ['numeric', 'min:0'],
                'opsi' => null,
                'urutan' => 23,
                'posisi_row' => 12,
                'posisi_col' => 4,
                'lebar_col' => 3,
                'wajib' => false,
                'status' => 'aktif'
            ],
            
            // Informasi tambahan
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'nama_pemohon',
                'label' => 'Nama Pemohon',
                'tipe_field' => 'text',
                'placeholder' => 'Nama lengkap pemohon',
                'validasi' => ['required', 'string', 'max:255'],
                'opsi' => null,
                'urutan' => 24,
                'posisi_row' => 13,
                'posisi_col' => 1,
                'lebar_col' => 6,
                'wajib' => true,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'area_kerja',
                'label' => 'Area Kerja',
                'tipe_field' => 'select',
                'placeholder' => 'Pilih area kerja',
                'validasi' => ['required'],
                'opsi' => ['Jakarta', 'Surabaya', 'Bandung', 'Semarang', 'Medan', 'Makassar'],
                'urutan' => 25,
                'posisi_row' => 13,
                'posisi_col' => 2,
                'lebar_col' => 6,
                'wajib' => true,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'periode_perjalanan',
                'label' => 'Periode Perjalanan',
                'tipe_field' => 'text',
                'placeholder' => 'Contoh: 19-21 Mei 2025',
                'validasi' => ['required', 'string'],
                'opsi' => null,
                'urutan' => 26,
                'posisi_row' => 14,
                'posisi_col' => 1,
                'lebar_col' => 12,
                'wajib' => true,
                'status' => 'aktif'
            ],
            [
                'kategori_pengajuan_id' => $kategoriPerdin->id,
                'nama_field' => 'tujuan_bisnis',
                'label' => 'Tujuan Bisnis',
                'tipe_field' => 'textarea',
                'placeholder' => 'Jelaskan tujuan bisnis dari perjalanan dinas ini...',
                'validasi' => ['required', 'string'],
                'opsi' => null,
                'urutan' => 27,
                'posisi_row' => 15,
                'posisi_col' => 1,
                'lebar_col' => 12,
                'wajib' => true,
                'status' => 'aktif'
            ]
        ];

        // Insert form fields
        foreach ($formFields as $field) {
            FormField::create($field);
        }

        $this->command->info('FormField seeder for Perjalanan Dinas completed successfully!');
    }
}
