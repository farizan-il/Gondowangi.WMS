<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanSistem extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_sistem';

    protected $fillable = [
        'kunci',
        'nilai',
        'deskripsi',
        'kategori',
        'tipe_data'
    ];

    protected $casts = [
        'tanggal_diperbarui' => 'datetime',
    ];

    const CREATED_AT = null;
    const UPDATED_AT = 'tanggal_diperbarui';

    // Helper methods
    public static function get($key, $default = null)
    {
        $setting = static::where('kunci', $key)->first();
        return $setting ? $setting->nilai : $default;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(
            ['kunci' => $key],
            ['nilai' => $value]
        );
    }

    // Scopes
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}
