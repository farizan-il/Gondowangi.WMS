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
        'qty_sample',
        'total_incoming',
        'uom',
        'defect_count',
        'catatan_qc',
        'hasil_qc',
        'qc_date',
        'qc_by',
    ];

    protected $casts = [
        'qty_sample' => 'decimal:4',
        'total_incoming' => 'decimal:4',
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
