<?php

namespace App\Http\Controllers\Gondowangi\Brands;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Brand;

class SemuaBrandController extends Controller
{
    public function index()
    {
        $carouselbrand = Banner::where('type', 'brand_produk')
            ->active() // Menggunakan scope active untuk is_active = 1
            ->ordered()
            ->get();
            
        $banners = Banner::where('type', 'semua_brand')
            ->active()
            ->ordered()
            ->get();
        
        $brands = Brand::where('is_active', 1)
            ->orderBy('display_order', 'asc')
            ->get();
            
        // Bagi brands menjadi chunks of 4 untuk setiap slide
        $brandChunks = $brands->chunk(4);
            
        return view('Gondowangi.Brands.index', [
            'title' => 'Semua Brands',
            'banners' => $banners,
            'carouselbrand' => $carouselbrand,
            'brandChunks' => $brandChunks
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
