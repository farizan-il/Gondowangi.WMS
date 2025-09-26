<?php

namespace App\Http\Controllers\Gondowangi\Brands\HGForMan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BrandDetail;

class HgForManController extends Controller
{
    public function index()
    {
        // Mengambil data banner brand untuk HG For Man
        $bannerBrand = BrandDetail::where('brand_name', BrandDetail::BRAND_HGFORMAN)
            ->where('type', BrandDetail::TYPE_BANNER)
            ->first();

        // Mengambil data detail brand untuk HG For Man (produk-produk)
        $detailBrands = BrandDetail::where('brand_name', BrandDetail::BRAND_HGFORMAN)
            ->where('type', BrandDetail::TYPE_DETAIL)
            ->get();

        $backgroundBrand = BrandDetail::where('brand_name', BrandDetail::BRAND_HGFORMAN)
            ->where('type', BrandDetail::TYPE_CAROUSEL)
            ->first();
            
            
        return view('Gondowangi.Brands.HGForMan.index', [
            'title' => 'HG For Man',
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
