<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QCPhoto extends Model
{
    use HasFactory;

    protected $table = 'qc_photos';

    protected $fillable = [
        'qc_checklist_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'description',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function qcChecklist()
    {
        return $this->belongsTo(QCChecklist::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}