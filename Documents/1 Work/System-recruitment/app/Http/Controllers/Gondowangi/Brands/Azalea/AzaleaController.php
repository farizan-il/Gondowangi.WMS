<?php

namespace App\Http\Controllers\Gondowangi\Brands\Azalea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BrandDetail;

class AzaleaController extends Controller
{
    // public function index()
    // {
    //     return view('Gondowangi.Brands.Azalea.index', [
    //         'title' => 'Azalea',
    //     ]);
    // }
    
    public function index()
    {
        // Ambil banner brand
        $bannerBrand = BrandDetail::where('brand_name', BrandDetail::BRAND_AZALEA)
            ->where('type', BrandDetail::TYPE_BANNER)
            ->first();
    
        // Ambil detail brand
        $detailBrands = BrandDetail::where('brand_name', BrandDetail::BRAND_AZALEA)
            ->where('type', BrandDetail::TYPE_DETAIL)
            ->get();
    
        // Ambil background carousel brand
        $backgroundBrand = BrandDetail::where('brand_name', BrandDetail::BRAND_AZALEA)
            ->where('type', BrandDetail::TYPE_CAROUSEL)
            ->first();
    
        return view('Gondowangi.Brands.Azalea.index', [
            'title' => 'Azalea',
            'bannerBrand' => $bannerBrand,
            'detailBrands' => $detailBrands,
            'backgroundBrand' => $backgroundBrand,
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
