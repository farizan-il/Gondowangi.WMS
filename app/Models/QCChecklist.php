<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QCChecklist extends Model
{
    use HasFactory;

    protected $table = 'qc_checklists';

    protected $fillable = [
        'no_form_checklist',
        'incoming_item_id',
        'incoming_id',
        'po_id',
        'no_surat_jalan',
        'material_id',
        'supplier_id',
        'reference',
        'kategori',
        'no_kendaraan',
        'nama_driver',
        'tanggal_qc',
        'qc_by',
        'status',
    ];

    protected $casts = [
        'tanggal_qc' => 'datetime',
    ];

    public function incomingItem()
    {
        return $this->belongsTo(IncomingGoodsItem::class, 'incoming_item_id');
    }

    public function incomingGood()
    {
        return $this->belongsTo(IncomingGood::class, 'incoming_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function qcBy()
    {
        return $this->belongsTo(User::class, 'qc_by');
    }

    public function qcChecklistDetail()
    {
        return $this->hasOne(QCChecklistDetail::class, 'qc_checklist_id');
    }

    public function photos()
    {
        return $this->hasMany(QCPhoto::class);
    }
}
