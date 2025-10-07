<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingGood extends Model
{
    use HasFactory;

    protected $fillable = [
        'incoming_number',
        'no_surat_jalan',
        'po_id',
        'supplier_id',
        'no_kendaraan',
        'nama_driver',
        'tanggal_terima',
        'kategori',
        'status',
        'received_by',
    ];

    protected $casts = [
        'tanggal_terima' => 'datetime',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items()
    {
        return $this->hasMany(IncomingGoodsItem::class, 'incoming_id');
    }

    public function documents()
    {
        return $this->hasMany(IncomingDocument::class, 'incoming_id');
    }
}