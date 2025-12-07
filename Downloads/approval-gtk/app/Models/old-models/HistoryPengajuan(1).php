<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryPengajuan extends Model
{
    use HasFactory;

    protected $table = 'HistoryPengajuan';

    protected $fillable = [
        'pengajuan_id',
        'step_ke',
        'approver_id',
        'aksi', //'submit','approve','reject','revise','cancel'
        'status_sebelum',
        'status_sesudah',
        'catatan',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'waktu_aksi' => 'datetime',
    ];

    public $timestamps = true;

    // Relasi
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}