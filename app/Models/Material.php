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
        'subkategori',
        'satuan',
        'deskripsi',
        'qc_required',
        'expiry_required',
        'expiry_required',
        'status',
        'kategori', //RAW MATERIAL dan PACKAGING MATERIAL
        'halal_status'
    ];

    protected $casts = [
        'qc_required' => 'boolean',
        'expiry_required' => 'boolean',
    ];



    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function incomingGoodsItems()
    {
        return $this->hasMany(IncomingGoodsItem::class);
    }
}