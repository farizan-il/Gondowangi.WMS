<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaLibrary extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'alt_text',
        'title',
        'description',
        'uploaded_by'
    ];

    public $timestamps = false;

    protected $casts = [
        'file_size' => 'integer',
        'uploaded_by' => 'integer',
        'created_at' => 'datetime',
    ];

    // Relasi
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
