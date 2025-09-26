<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karyawan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'karyawan';

    protected $fillable = [
        'asal_daerah',
        'user_id',
        'cv',
        'foto',
        'posisi_dilamar_id',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat_ktp',
        'alamat_tinggal',
        'no_telepon',
        'email',
        'agama',
        'tinggi_badan',
        'berat_badan',
        'status_pernikahan',
        'nama_pasangan',
        'jumlah_anak',
        'riwayat_penyakit',
        'golongan_darah',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'tiktok',
        'medsos_lain',
        'nama_ayah',
        'pekerjaan_ayah',
        'pendidikan_ayah',
        'tanggal_lahir_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'pendidikan_ibu',
        'tanggal_lahir_ibu',
        'data_saudara',
        'data_anak',
        'pendidikan_formal',
        'pendidikan_non_formal',
        'bahasa_inggris',
        'bahasa_asing_lain',
        'kemampuan_komputer',
        'keterampilan_lain',
        'pengalaman_kerja',
        'aktivitas_sosial',
        'hobi',
        'kegiatan_waktu_luang',
        'prestasi_karya',
        'gaji_terakhir',
        'tunjangan_terakhir',
        'fasilitas_terakhir',
        'fasilitas_lain',
        'sim',
        'bpjs_tk',
        'bpjs_kesehatan',
        'npwp',
        'bidang_pekerjaan_diminati',
        'jabatan_diminati',
        'gaji_diharapkan',
        'tunjangan_diharapkan',
        'fasilitas_diharapkan',
        'jaminan_diharapkan',
        'lain_diharapkan',
        'kesediaan_medical_checkup',
        'kesediaan_psikologi',
        'kesediaan_masa_percobaan',
        'kesediaan_perjalanan_dinas',
        'maksimum_hari_dinas',
        'kesediaan_penempatan',
        'kesediaan_pindah_kota',
        'kapan_mulai_kerja',
        'referensi',
        'kontak_darurat',
        'informasi_tambahan',
        'status' // 'pending', 'approved', 'rejected', 'Save'
    ];

    protected $casts = [
        'data_saudara' => 'array',
        'data_anak' => 'array',
        'pendidikan_formal' => 'array',
        'pendidikan_non_formal' => 'array',
        'pengalaman_kerja' => 'array',
        'aktivitas_sosial' => 'array',
        'referensi' => 'array',
        'kontak_darurat' => 'array',
        'sim' => 'array',
        'kesediaan_penempatan' => 'array'
    ];
    
    public function posisilamaran()
    {
        return $this->belongsTo(CareerPosition::class, 'posisi_dilamar_id');
    }

    public function credentials()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
