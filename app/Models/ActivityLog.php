<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'material_id',
        'supplier_id',
        'warehousebin_id',
        'user_id_target', // untuk log user yang di-create/update/delete
        'batch_lot',
        'exp_date',
        'qty_before',
        'qty_after',
        'bin_from',
        'bin_to',
        'reference_document',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
        'device_info',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'exp_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relasi ke Bin yang dicatat
    public function warehouseBin(): BelongsTo
    {
        // Asumsi 'warehousebin_id' merujuk ke Bin Utama untuk log ini (misalnya Bin Akhir)
        return $this->belongsTo(WarehouseBin::class, 'warehousebin_id'); 
    }

    // --- BARU: Relasi untuk Bin Asal (bin_from) ---
    public function binAsal(): BelongsTo
    {
        // Asumsi 'bin_from' menyimpan ID dari WarehouseBin
        return $this->belongsTo(WarehouseBin::class, 'bin_from');
    }
    
    // --- BARU: Relasi untuk Bin Tujuan (bin_to) ---
    public function binTujuan(): BelongsTo
    {
        // Asumsi 'bin_to' menyimpan ID dari WarehouseBin
        return $this->belongsTo(WarehouseBin::class, 'bin_to');
    }
}