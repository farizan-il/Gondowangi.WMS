<?php

namespace App\Http\Controllers\Gondowangi\Berita;

use App\Http\Controllers\Controller;
use App\Models\News;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index()
    {
        // Ambil 4 berita terbaru untuk section kiri (featured)
        $featuredNews = News::with(['category', 'author'])
            ->published()
            ->active()
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get();

        // Ambil berita untuk sidebar (skip 4 berita pertama, ambil 5 untuk tampilan awal)
        $sidebarNews = News::with(['category', 'author'])
            ->published()
            ->active()
            ->orderBy('published_at', 'desc')
            ->skip(4)
            ->take(5)
            ->get();

        // Hitung total berita sidebar untuk menentukan apakah perlu tombol "Load More"
        $totalSidebarNews = News::published()
            ->active()
            ->count() - 4; // Kurangi 4 berita yang sudah ditampilkan di kiri

        return view('Gondowangi.Berita.index', [
            'title' => 'Berita',
            'featuredNews' => $featuredNews,
            'sidebarNews' => $sidebarNews,
            'showLoadMore' => $totalSidebarNews > 5
        ]);
    }
    
    public function show($slug)
    {
        try {
            $news = News::with(['category', 'author'])
                ->where('slug', $slug)
                ->published()
                ->active()
                ->firstOrFail();
    
            // Increment views count
            
    
            // Get related news
            $relatedNews = News::with(['category', 'author'])
                ->where('id', '!=', $news->id)
                ->where('category_id', $news->category_id)
                ->published()
                ->active()
                ->take(3)
                ->get();
    
            // If no related news from same category, get recent news
            if ($relatedNews->count() < 3) {
                $additionalNews = News::with(['category', 'author'])
                    ->where('id', '!=', $news->id)
                    ->whereNotIn('id', $relatedNews->pluck('id'))
                    ->published()
                    ->active()
                    ->orderBy('published_at', 'desc')
                    ->take(3 - $relatedNews->count())
                    ->get();
    
                $relatedNews = $relatedNews->merge($additionalNews);
            }
    
            return view('Gondowangi.Berita.show', [
                'title' => $news->title,
                'news' => $news,
                'relatedNews' => $relatedNews
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Error: Berita tidak ditemukan dengan slug: ' . $slug);  // Log error jika berita tidak ditemukan
            return response()->view('errors.404', [], 404); // Tampilkan halaman 404 jika berita tidak ditemukan
        }
    }


    public function loadMore(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = 5;

        $news = News::with(['category', 'author'])
            ->published()
            ->active()
            ->orderBy('published_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        $hasMore = News::published()->active()->count() > ($offset + $limit);

        $newsData = $news->map(function($item) {
            return [
                'id' => $item->id,
                'title' => \Illuminate\Support\Str::limit($item->title, 60),
                'url' => route('berita.show', $item->slug),
                'thumbnail_url' => $item->thumbnail_url,
                'published_at_formatted' => $item->published_at->format('Y-m-d'),
                'published_at_display' => $item->published_at->format('F j, Y'),
                'views_count' => $item->formatted_views
            ];
        });

        return response()->json([
            'success' => true,
            'news' => $newsData,
            'hasMore' => $hasMore
        ]);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('q', '');
        
        if (empty($searchTerm)) {
            return redirect()->route('berita.index');
        }

        $news = News::with(['category', 'author'])
            ->published()
            ->active()
            ->where(function($query) use ($searchTerm) {
                $query->where('title', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('excerpt', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('content', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('tags', 'LIKE', "%{$searchTerm}%");
            })
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        return view('Gondowangi.Berita.search', [
            'title' => 'Hasil Pencarian: ' . $searchTerm,
            'news' => $news,
            'searchTerm' => $searchTerm,
            'totalResults' => $news->total()
        ]);
    }

    public function category($slug)
    {
        $category = NewsCategory::where('category_slug', $slug)
            ->active()
            ->firstOrFail();

        $news = News::with(['category', 'author'])
            ->where('category_id', $category->id)
            ->published()
            ->active()
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        return view('Gondowangi.Berita.category', [
            'title' => 'Kategori: ' . $category->category_name,
            'category' => $category,
            'news' => $news
        ]);
    }

    public function archive(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        $query = News::with(['category', 'author'])
            ->published()
            ->active()
            ->orderBy('published_at', 'desc');

        if ($year) {
            $query->whereYear('published_at', $year);
        }

        if ($month) {
            $query->whereMonth('published_at', $month);
        }

        $news = $query->paginate(9);

        $title = 'Arsip Berita';
        if ($year && $month) {
            $title .= ' ' . \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y');
        } elseif ($year) {
            $title .= ' ' . $year;
        }

        return view('Gondowangi.Berita.archive', [
            'title' => $title,
            'news' => $news,
            'year' => $year,
            'month' => $month
        ]);
    }

    public function sitemap()
    {
        $news = News::published()
            ->active()
            ->orderBy('published_at', 'desc')
            ->get(['slug', 'updated_at']);

        return response()->view('Gondowangi.Berita.sitemap', [
            'news' => $news
        ])->header('Content-Type', 'application/xml');
    }

    public function rss()
    {
        $news = News::with(['category', 'author'])
            ->published()
            ->active()
            ->orderBy('published_at', 'desc')
            ->take(20)
            ->get();

        return response()->view('Gondowangi.Berita.rss', [
            'news' => $news
        ])->header('Content-Type', 'application/rss+xml');
    }
    
    // public function search(Request $request)
    // {
    //     $query = $request->get('q');
    //     $perPage = 10;
    
    //     $results = News::with(['category', 'author'])
    //         ->where(function($q) use ($query) {
    //             $q->where('title', 'LIKE', "%{$query}%")
    //               ->orWhere('excerpt', 'LIKE', "%{$query}%")
    //               ->orWhere('content', 'LIKE', "%{$query}%");
    //         })
    //         ->published()
    //         ->active()
    //         ->orderBy('published_at', 'desc')
    //         ->paginate($perPage);
    
    //     return view('Gondowangi.Berita.search', [
    //         'title' => 'Hasil Pencarian: ' . $query,
    //         'query' => $query,
    //         'results' => $results
    //     ]);
    // }

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
    // public function show(string $id)
    // {
    //     //
    // }

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
