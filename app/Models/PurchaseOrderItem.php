<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_id',
        'material_id',
        'qty_order',
        'satuan',
        'harga_satuan',
        'total_harga',
        'keterangan',
    ];

    protected $casts = [
        'qty_order' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}