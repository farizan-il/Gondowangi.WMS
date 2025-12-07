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
        'jumlah_hari', // bisa null jika tidak perlu hari
        'nilai_awal',
        'is_intervened_by_finance',
        'finance_intervention_date',
        'finance_intervention_by'
    ];
    
    protected $casts = [
        'pengajuan_id' => 'integer',
        'form_field_id' => 'integer',
        'jumlah_hari' => 'integer',
        'is_intervened_by_finance' => 'boolean',
        'finance_intervention_date' => 'datetime',
        'finance_intervention_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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
    
    public function financeInterventionBy()
    {
        return $this->belongsTo(Karyawan::class, 'finance_intervention_by');
    }
    
    // TAMBAHAN: Helper methods untuk perhitungan
    
    /**
     * Hitung nilai total berdasarkan jumlah hari (untuk hotel/makan)
     */
    public function getNilaiTotalAttribute()
    {
        $nilai = (float)$this->nilai;
        $jumlahHari = (int)$this->jumlah_hari;
        
        // Jika ada jumlah hari, kalikan
        if ($jumlahHari > 0) {
            return $nilai * $jumlahHari;
        }
        
        // Jika tidak, return nilai asli
        return $nilai;
    }
    
    /**
     * Check apakah field ini adalah hotel
     */
    public function isHotelField()
    {
        if ($this->formField && strpos($this->formField->nama_field, 'hotel') !== false) {
            return true;
        }
        
        if ($this->catatan && strpos($this->catatan, 'hotel') !== false) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check apakah field ini adalah makan
     */
    public function isMakanField()
    {
        if ($this->formField && strpos($this->formField->nama_field, 'makan') !== false) {
            return true;
        }
        
        if ($this->catatan && strpos($this->catatan, 'makan') !== false) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get label yang friendly untuk field
     */
    public function getFieldLabelAttribute()
    {
        if ($this->formField) {
            return $this->formField->label;
        }
        
        if ($this->catatan) {
            return ucwords(str_replace(['_', 'form_data'], [' ', ''], $this->catatan));
        }
        
        return 'Field tidak diketahui';
    }
}
