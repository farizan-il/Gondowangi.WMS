<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRequest extends Model
{
    use HasFactory;

    protected $table = 'TransactionRequests';

    protected $fillable = [
        'pengajuan_id', // nullable
        'settlement_id', // nullable
        'tr_group_id',
        'status', //'waiting','paid','rejected'
        'catatan_finance',
        'bukti_transfer', // simpan file di folder public/assets/pengajuan/tr/nama_pengguna/nama_kategori_pengajuan/
        'tanggal_transfer',
        'processed_by', // ini di proses oleh siapa
    ];

    protected $casts = [
        'jumlah_dana' => 'decimal:2',
        'tanggal_transfer' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relasi ke Transaction Request Group
    public function trGroup()
    {
        return $this->belongsTo(TransactionRequestGroup::class, 'tr_group_id');
    }

    // Relasi ke Pengajuan
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }
    
    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }

    // Relasi ke Karyawan yang memproses (finance)
    public function processedBy()
    {
        return $this->belongsTo(Karyawan::class, 'processed_by');
    }
    
    public function getItemAttribute()
    {
        return $this->pengajuan ?? $this->settlement;
    }
    
    // Method untuk mendapatkan data item (pengajuan atau settlement)
    public function getItemData()
    {
        if ($this->settlement_id) {
            // Jika ini settlement, ambil data settlement dengan pengajuan terkait
            return [
                'type' => 'settlement',
                'data' => $this->settlement,
                'pengajuan' => $this->settlement->pengajuan,
                'nominal' => abs($this->settlement->selisih),
                'nomor_transaksi' => $this->settlement->nomor_settlement,
                'keterangan' => 'Settlement Over Budget'
            ];
        } else {
            // Jika ini pengajuan biasa
            return [
                'type' => 'pengajuan',
                'data' => $this->pengajuan,
                'pengajuan' => $this->pengajuan,
                'nominal' => $this->pengajuan->nominal_pengajuan,
                'nomor_transaksi' => $this->pengajuan->nomor_pengajuan,
                'keterangan' => 'Pengajuan'
            ];
        }
    }
    public function getItem()
    {
        if ($this->pengajuan_id) {
            return $this->pengajuan;
        } elseif ($this->settlement_id) {
            return $this->settlement;
        }
        return null;
    }
    
    // Method untuk mendapatkan nomor item
    public function getItemNumber()
    {
        if ($this->pengajuan_id && $this->pengajuan) {
            return $this->pengajuan->nomor_pengajuan;
        } elseif ($this->settlement_id && $this->settlement) {
            return $this->settlement->nomor_settlement;
        }
        return null;
    }
    
    // Method untuk mendapatkan nominal item
    public function getItemNominal()
    {
        if ($this->pengajuan_id && $this->pengajuan) {
            return $this->pengajuan->nominal_pengajuan;
        } elseif ($this->settlement_id && $this->settlement) {
            return abs($this->settlement->selisih);
        }
        return 0;
    }
    
    // Method untuk mendapatkan requester
    public function getRequester()
    {
        if ($this->pengajuan_id && $this->pengajuan) {
            return $this->pengajuan->requester;
        } elseif ($this->settlement_id && $this->settlement) {
            return $this->settlement->pengajuan->requester;
        }
        return null;
    }
    
    // Method untuk mendapatkan kategori
    public function getKategori()
    {
        if ($this->pengajuan_id && $this->pengajuan) {
            return $this->pengajuan->kategoriPengajuan;
        } elseif ($this->settlement_id && $this->settlement) {
            return $this->settlement->pengajuan->kategoriPengajuan;
        }
        return null;
    }
}