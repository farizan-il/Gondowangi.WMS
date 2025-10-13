<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QCChecklistDetail extends Model
{
    use HasFactory;

    protected $table = 'qc_checklist_details';

    protected $fillable = [
        'qc_checklist_id',
        'jumlah_box_utuh',
        'qty_box_utuh',
        'jumlah_box_tidak_utuh',
        'qty_box_tidak_utuh',
        'total_incoming',
        'uom',
        'defect_count',
        'catatan_qc',
        'hasil_qc',
        'qc_date',
        'qc_by',
    ];

    protected $casts = [
        'qty_box_utuh' => 'decimal:2',
        'qty_box_tidak_utuh' => 'decimal:2',
        'total_incoming' => 'decimal:2',
        'defect_count' => 'integer',
        'qc_date' => 'datetime',
    ];

    public function qcChecklist()
    {
        return $this->belongsTo(QCChecklist::class);
    }

    public function qcBy()
    {
        return $this->belongsTo(User::class, 'qc_by');
    }
}
