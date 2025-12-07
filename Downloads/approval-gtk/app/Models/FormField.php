<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    use HasFactory;

    protected $table = 'FormField'; // Ganti jika nama tabel berbeda

    protected $fillable = [
        'kategori_pengajuan_id',
        'nama_field',
        'label',
        'tipe_field', //'text','textarea','number','date','select','radio','checkbox','file','currency'
        'placeholder',
        'validasi',
        'opsi',
        'urutan',
        'posisi_row',
        'posisi_col',
        'lebar_col',
        'wajib',
        'status', // 'aktif', 'nonaktif'
    ];

    protected $casts = [
        'required' => 'boolean',
        'opsi' => 'array',
        'validasi' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public $timestamps = true;

    // Relationships
    public function kategoriPengajuan()
    {
        return $this->belongsTo(KategoriPengajuan::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
