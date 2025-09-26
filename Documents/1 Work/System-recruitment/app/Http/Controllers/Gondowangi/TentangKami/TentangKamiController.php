<?php

namespace App\Http\Controllers\Gondowangi\TentangKami;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaturPilar;
use App\Models\Event;
use App\Models\Banner;
use App\Models\CompanyTimeline;

class TentangKamiController extends Controller
{
    public function index()
    {
        $banners = Banner::where('type', 'tentang_kami')
            ->ordered()
            ->get();
            
        $caturPilar = CaturPilar::where('status', 'aktif')
            ->orderBy('urutan', 'asc')
            ->get();
    
        $timelines = CompanyTimeline::query()
            ->orderBy('year', 'asc') // urut dari tahun terlama ke terbaru
            ->get();
    
        return view('Gondowangi.TentangKami.index', [
            'title' => 'Tentang Kami',
            'caturPilar' => $caturPilar,
            'banners' => $banners,
            'timelines' => $timelines
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
