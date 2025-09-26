<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BrandDetail extends Model
{
    use HasFactory;

    protected $table = 'brand_detail';

    protected $fillable = [
        'img',
        'type',
        'title',
        'deksripsi',
        'brand_name',
    ];

    // Jika enum ingin dibatasi secara programatik:
    public const TYPE_CAROUSEL = 'carousel_brand';
    public const TYPE_BANNER = 'banner_brand';
    public const TYPE_DETAIL = 'detail_brand';

    public const BRAND_NATUR = 'Natur';
    public const BRAND_MIZZU = 'Mizzu';
    public const BRAND_AZALEA = 'Azalea';
    public const BRAND_HGFORMAN = 'Hgforman';

    // Optional: Casts jika kamu ingin
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
