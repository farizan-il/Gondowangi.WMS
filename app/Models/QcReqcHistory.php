<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcReqcHistory extends Model
{
    use HasFactory;

    protected $table = 'qc_reqc_history';

    protected $fillable = [
        'qc_checklist_id',
        'inventory_stock_id',
        'incoming_item_id',
        'reqc_number',
        'old_status',
        'old_exp_date',
        'reason',
        'initiated_by',
        'initiated_at',
        'status',
        'new_status',
        'new_exp_date',
        'qty_sample_previous',
        'qty_sample_new',
        'completed_at',
    ];

    protected $casts = [
        'old_exp_date' => 'date',
        'new_exp_date' => 'date',
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime',
        'qty_sample_previous' => 'decimal:4',
        'qty_sample_new' => 'decimal:4',
    ];

    public function qcChecklist()
    {
        return $this->belongsTo(QCChecklist::class, 'qc_checklist_id');
    }

    public function inventoryStock()
    {
        return $this->belongsTo(InventoryStock::class, 'inventory_stock_id');
    }

    public function incomingItem()
    {
        return $this->belongsTo(IncomingGoodsItem::class, 'incoming_item_id');
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }
}
