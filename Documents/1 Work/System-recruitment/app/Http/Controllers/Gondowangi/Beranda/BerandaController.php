<?php

namespace App\Http\Controllers\Gondowangi\Beranda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Award;
use App\Models\News;
use App\Models\Event;
use App\Models\Banner;

class BerandaController extends Controller
{
    public function index()
    {
        // Mengambil data banner yang aktif dan diurutkan berdasarkan sort_order
        $banners = Banner::where('type', 'beranda')
            ->ordered()
            ->get();
            
        $events = Event::where('is_featured', true)
            ->orderBy('event_date', 'desc')
            ->get();
        
        // Mengambil data brand yang aktif dan diurutkan berdasarkan display_order
        $brands = Brand::where('is_active', true)
            ->orderBy('display_order')
            ->get();
        
        // Mengambil data award yang aktif dan diurutkan berdasarkan display_order
        $awards = News::with(['category', 'author'])
            ->where('status', 'published') // hanya ambil yang published
            ->whereHas('category', function ($query) {
                $query->where('category_name', 'award');
            })
            ->orderBy('published_at', 'desc')
            ->get();

        
        // Mengambil berita featured (untuk tampilan utama)
        $featuredNews = News::with(['category', 'author'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('published_at', 'desc')
            ->first();
        
        // Mengambil berita lainnya (untuk tampilan list kecil)
        $otherNews = News::with(['category', 'author'])
            ->where('status', 'published')
            ->when($featuredNews, function($query) use ($featuredNews) {
                return $query->where('id', '!=', $featuredNews->id);
            })
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();
        
        return view('Gondowangi.Beranda.index', [
            'title' => 'Beranda',
            'banners' => $banners,
            'brands' => $brands,
            'awards' => $awards,
            'featuredNews' => $featuredNews,
            'otherNews' => $otherNews,
            'events' => $events
        ]);
    }


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
