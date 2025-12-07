<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Golongan extends Model
{
    protected $table = 'Golongan';

    // Kolom yang boleh diisi secara mass-assignment
    protected $fillable = [
        'nama_golongan',
        'biaya_hotel_per_hari',
        'biaya_makan_per_hari',
        'is_active',
    ];

    // Tipe data kolom
    protected $casts = [
        'biaya_hotel_per_hari' => 'decimal:2',
        'biaya_makan_per_hari' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Timestamps (created_at dan updated_at) aktif secara default
    public $timestamps = true;
    
    public static function isGolonganExists($nama_golongan, $excludeId = null)
    {
        $query = self::where('nama_golongan', $nama_golongan);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}