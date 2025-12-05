<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_item',
        'nama_material',
        'kategori',
        'sub_kategori',
        'satuan',
        'deskripsi',
        'qc_required',
        'expiry_required',
        'abc_class',
        'default_supplier_id',
        'status',
    ];

    protected $casts = [
        'qc_required' => 'boolean',
        'expiry_required' => 'boolean',
    ];

    public function defaultSupplier()
    {
        return $this->belongsTo(Supplier::class, 'default_supplier_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function incomingGoodsItems()
    {
        return $this->hasMany(IncomingGoodsItem::class);
    }
}