<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengajuan extends Model
{
    use HasFactory;

    protected $table = 'DetailPengajuan'; // Ganti jika nama tabel berbeda

    protected $fillable = [
        'pengajuan_id',
        'form_field_id',
        'nilai',
    ];

    public $timestamps = true;

    // Relasi ke model Pengajuan
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    // Relasi ke model FormField
    public function formField()
    {
        return $this->belongsTo(FormField::class);
    }
}
