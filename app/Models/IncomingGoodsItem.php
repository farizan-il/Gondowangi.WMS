<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingGoodsItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'incoming_id',
        'material_id',
        'batch_lot',
        'exp_date',
        'qty_wadah',
        'qty_unit',
        'satuan',
        'kondisi_baik',
        'kondisi_tidak_baik',
        'coa_ada',
        'coa_tidak_ada',
        'label_mfg_ada',
        'label_mfg_tidak_ada',
        'label_coa_sesuai',
        'label_coa_tidak_sesuai',
        'pabrik_pembuat',
        'status_qc',
        'is_halal',
        'is_non_halal',
        'bin_target',
        'qr_code',
        'keterangan',
    ];

    protected $casts = [
        'exp_date' => 'date',
        'qty_wadah' => 'decimal:2',
        'qty_unit' => 'decimal:2',
        'kondisi_baik' => 'boolean',
        'kondisi_tidak_baik' => 'boolean',
        'coa_ada' => 'boolean',
        'coa_tidak_ada' => 'boolean',
        'label_mfg_ada' => 'boolean',
        'label_mfg_tidak_ada' => 'boolean',
        'label_coa_sesuai' => 'boolean',
        'label_coa_tidak_sesuai' => 'boolean',
    ];

    public function incomingGood()
    {
        return $this->belongsTo(IncomingGood::class, 'incoming_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function qcChecklist()
    {
        // Relasi ke tabel QCChecklist
        return $this->hasOne(QCChecklist::class, 'incoming_item_id');
    }
}