<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSettlement extends Model
{
    use HasFactory;

    protected $table = 'DetailSettlement';

    protected $fillable = [
        'settlement_id',
        'form_field_id',
        'detail_pengajuan_id',
        'keterangan',
        'tanggal_transaksi',
        'nominal',
        'jumlah_hari', // bisa null jika tidak perlu hari
        'kategori_biaya',
        'file_bukti',
        'catatan',
        'is_intervened_by_finance',
        'finance_intervention_date',
        'finance_intervention_by',
        'original_keterangan',
        'original_nominal',
        'original_kategori_biaya'
    ];

    protected $casts = [
        'settlement_id' => 'integer',
        'form_field_id' => 'integer', 
        'detail_pengajuan_id' => 'integer',
        'tanggal_transaksi' => 'datetime',
        'nominal' => 'decimal:2',
        'original_nominal' => 'decimal:2',
        'is_intervened_by_finance' => 'boolean',
        'finance_intervention_date' => 'datetime',
        'finance_intervention_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function settlement()
    {
        return $this->belongsTo(Settlement::class, 'settlement_id');
    }

    public function formField()
    {
        return $this->belongsTo(FormField::class, 'form_field_id');
    }

    public function detailPengajuan()
    {
        return $this->belongsTo(DetailPengajuan::class, 'detail_pengajuan_id');
    }
    
    // Relationship untuk finance yang melakukan intervensi
    public function financeInterventionBy()
    {
        return $this->belongsTo(Karyawan::class, 'finance_intervention_by');
    }

    // Scopes
    public function scopeBySettlement($query, $settlementId)
    {
        return $query->where('settlement_id', $settlementId);
    }

    public function scopeByFormField($query, $formFieldId)
    {
        return $query->where('form_field_id', $formFieldId);
    }

    // Helper methods
    public function getFormattedNominal()
    {
        $settlement = $this->settlement;
        $pengajuan = $settlement ? $settlement->pengajuan : null;
        $mataUang = $pengajuan ? $pengajuan->mata_uang : 'IDR';
        
        return $mataUang . ' ' . number_format($this->nominal, 0, ',', '.');
    }

    public function getSelisihFromOriginal()
    {
        if (!$this->detailPengajuan) {
            return 0;
        }

        $originalValue = (float) $this->detailPengajuan->nilai;
        return $originalValue - $this->nominal;
    }

    public function getFormattedSelisih()
    {
        $selisih = $this->getSelisihFromOriginal();
        $settlement = $this->settlement;
        $pengajuan = $settlement ? $settlement->pengajuan : null;
        $mataUang = $pengajuan ? $pengajuan->mata_uang : 'IDR';
        
        return $mataUang . ' ' . number_format($selisih, 0, ',', '.');
    }

    public function hasFileBukti()
    {
        return !empty($this->file_bukti) && \Storage::disk('public')->exists($this->file_bukti);
    }

    public function getFileBuktiUrl()
    {
        if ($this->hasFileBukti()) {
            return asset('storage/' . $this->file_bukti);
        }
        
        return null;
    }

    public function getFileBuktiName()
    {
        if ($this->hasFileBukti()) {
            return basename($this->file_bukti);
        }
        
        return null;
    }
}