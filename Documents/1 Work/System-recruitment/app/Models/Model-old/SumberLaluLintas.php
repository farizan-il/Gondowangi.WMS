<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumberLaluLintas extends Model
{
    use HasFactory;

    protected $table = 'sumber_lalu_lintas';

    protected $fillable = [
        'nama',
        'jenis',
        'url',
        'deskripsi',
        'status'
    ];

    public $timestamps = false;

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }
}