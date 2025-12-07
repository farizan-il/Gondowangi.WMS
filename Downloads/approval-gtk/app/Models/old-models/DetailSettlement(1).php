<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSettlement extends Model
{
    use HasFactory;

    protected $table = 'detail_settlement'; // Pastikan ini sesuai dengan nama tabel Anda

    // Menentukan kolom-kolom yang dapat diisi (Mass Assignment)
    protected $fillable = [
        'settlement_id', // kolom foreign key yang mengarah ke settlement
        'keterangan',
        'tanggal_transaksi',
        'nominal',
        'kategori_biaya',
        'file_bukti',
        'catatan',
        'created_at',
    ];

    // Jika Anda ingin mendefinisikan relasi, misalnya dengan Settlement:
    public function settlement()
    {
        return $this->belongsTo(Settlement::class, 'settlement_id');
    }
}
