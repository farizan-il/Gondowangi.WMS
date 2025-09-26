<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts';

    protected $fillable = [
        'nama_lengkap',
        'alamat_email',
        'subjek',
        'komentar_pesan',
        'untuk', //enum('head_office', 'factory')
        'status',
        'is_read',
        'replied_at',
        'replied_by'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'replied_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relasi dengan User yang membalas
    public function repliedByUser()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
    
    // Accessor untuk format nama yang lebih rapi
    public function getNamaLengkapAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    // Mutator untuk format nama sebelum disimpan
    public function setNamaLengkapAttribute($value)
    {
        $this->attributes['nama_lengkap'] = ucwords(strtolower($value));
    }

    // Mutator untuk format email
    public function setAlamatEmailAttribute($value)
    {
        $this->attributes['alamat_email'] = strtolower($value);
    }

    // Scope untuk filter berdasarkan tujuan
    public function scopeForDestination($query, $destination)
    {
        return $query->where('untuk', $destination);
    }

    // Scope untuk kontak yang belum dibaca
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Scope untuk kontak berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessor untuk status dalam bahasa Indonesia
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu',
            'responded' => 'Direspon',
            'resolved' => 'Selesai'
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    // Accessor untuk format tanggal Indonesia
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    // Accessor untuk format tanggal balasan
    public function getFormattedRepliedAtAttribute()
    {
        return $this->replied_at ? $this->replied_at->format('d/m/Y H:i') : null;
    }

    // Method untuk menandai sebagai sudah dibaca
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    // Method untuk menandai sebagai sudah direspon
    public function markAsReplied($userId = null)
    {
        $this->update([
            'status' => 'diproses',
            'replied_at' => now(),
            'replied_by' => $userId ?? 1, // Default user ID jika tidak ada auth
            'is_read' => true
        ]);
    }
}