<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_po',
        'supplier_id',
        'tanggal_po',
        'tanggal_kirim_diharapkan',
        'total_nilai',
        'status',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_po' => 'date',
        'tanggal_kirim_diharapkan' => 'date',
        'total_nilai' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_id');
    }

    public function incomingGoods()
    {
        return $this->hasMany(IncomingGood::class, 'po_id');
    }
}