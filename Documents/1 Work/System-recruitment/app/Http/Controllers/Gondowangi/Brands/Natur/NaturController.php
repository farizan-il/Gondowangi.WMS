<?php

namespace App\Http\Controllers\Gondowangi\Brands\Natur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BrandDetail;

class NaturController extends Controller
{
    // public function index()
    // {
    //     return view('Gondowangi.Brands.Natur.index', [
    //         'title' => 'Natur',
    //     ]);
    // }

    public function index()
    {
        // Mengambil data banner brand untuk HG For Man
        $bannerBrand = BrandDetail::where('brand_name', BrandDetail::BRAND_NATUR)
            ->where('type', BrandDetail::TYPE_BANNER)
            ->first();

        // Mengambil data detail brand untuk HG For Man (produk-produk)
        $detailBrands = BrandDetail::where('brand_name', BrandDetail::BRAND_NATUR)
            ->where('type', BrandDetail::TYPE_DETAIL)
            ->get();

        $backgroundBrand = BrandDetail::where('brand_name', BrandDetail::BRAND_NATUR)
            ->where('type', BrandDetail::TYPE_CAROUSEL)
            ->first();
            
            
        return view('Gondowangi.Brands.Natur.index', [
            'title' => 'Natur',
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
