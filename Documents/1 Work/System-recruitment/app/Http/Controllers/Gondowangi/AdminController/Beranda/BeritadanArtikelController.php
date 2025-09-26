<?php
namespace App\Http\Controllers\Gondowangi\AdminController\Beranda;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BeritadanArtikelController extends Controller
{
    public function index()
    {
        $news = News::with(['category', 'author'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $categories = NewsCategory::where('is_active', true)->get();
        
        // Statistics
        $totalNews = News::count();
        $publishedNews = News::where('status', 'published')->count();
        $featuredNews = News::where('status', 'published')->where('is_featured', true)->count();
        $draftNews = News::where('status', 'draft')->count();
        
        return view('Gondowangi.Admin.Beranda.BeritaArtikel', [
            'title' => 'Berita dan Artikel',
            'news' => $news,
            'categories' => $categories,
            'stats' => [
                'total' => $totalNews,
                'published' => $publishedNews,
                'featured' => $featuredNews,
                'draft' => $draftNews
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category_id' => 'required|exists:news_categories,id',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8048',
            // 'is_featured' => 'boolean',
            'published_at' => 'nullable|date'
        ]);
        

        $data = $request->all();
        
        // Generate slug from title
        $data['slug'] = \Str::slug($request->title);
        $data['author_id'] = auth()->id();
        
        // Handle file upload
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/news'), $filename);
            $data['featured_image'] = 'uploads/news/' . $filename;
        }

        // Set published_at if status is published
        if ($data['status'] === 'published' && !$data['published_at']) {
            $data['published_at'] = now();
        }

        News::create($data);

        return redirect()->back()->with('success', 'Berita berhasil ditambahkan!');
    }


    public function show($id)
    {
        try {
            $news = News::with(['category', 'author'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $news
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Berita tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    // public function update(Request $request, $id)
    // {
    //     try {
    //         // Validasi input
    //         $validator = Validator::make($request->all(), [
    //             'title' => 'required|string|max:255',
    //             'category_id' => 'required|exists:news_categories,id',
    //             'excerpt' => 'required|string',
    //             'content' => 'required|string',
    //             'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:9048',
    //             'status' => 'required|in:draft,published,archived',
    //         ]);
            
            
    
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Validasi gagal',
    //                 'errors' => $validator->errors()->all()  // Menampilkan pesan error yang lebih rinci
    //             ], 422);
    //         }
    
    //         $news = News::findOrFail($id);
            
    //         // Start transaction
    //         DB::beginTransaction();
    
    //         // Handle featured news (hanya satu berita yang bisa featured)
    
    //         // Handle file upload
    //         $featuredImagePath = $news->featured_image;
    //         if ($request->hasFile('featured_image')) {
    //             // Delete old image if exists
    //             if ($news->featured_image && Storage::disk('public')->exists(str_replace('storage/', '', $news->featured_image))) {
    //                 Storage::disk('public')->delete(str_replace('storage/', '', $news->featured_image));
    //             }
                
    //             $image = $request->file('featured_image');
    //             $imageName = time() . '_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();
    //             $featuredImagePath = $image->storeAs('news/images', $imageName, 'public');
    //             $featuredImagePath = 'storage/' . $featuredImagePath;
    //         }
    
    //         // Update news
    //         $news->update([
    //             'title' => $request->title,
    //             'slug' => Str::slug($request->title),
    //             'category_id' => $request->category_id,
    //             'excerpt' => $request->excerpt,
    //             'content' => $request->content,
    //             'featured_image' => $featuredImagePath,
    //             'status' => $request->status,
    //             'is_featured' => $request->has('is_featured') ? true : false,
                
    //             'published_at' => $request->status === 'published' && !$news->published_at ? now() : $news->published_at,
    //         ]);
    
    //         DB::commit();
    
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Berita berhasil diperbarui!',
    //             'data' => $news
    //         ]);
    
    //     } catch (\Exception $e) {
    //         DB::rollback();
            
    //         // Menampilkan detail error pada update
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan saat memperbarui berita: ' . $e->getMessage(),
    //             'error_details' => $e->getTraceAsString()  // Detail trace error
    //         ], 500);
    //     }
    // }
    
    public function update(Request $request, $id)
    {
        try {
            $news = News::findOrFail($id);
            
            // Validasi data
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'category_id' => 'required|exists:news_categories,id',
                'excerpt' => 'required|string',
                'content' => 'required|string',
                'status' => 'required|in:draft,published,archived',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:9048', // Tambahkan validasi gambar
            ]);
            
            // Generate slug dari title jika title berubah
            if ($news->title !== $validatedData['title']) {
                $validatedData['slug'] = Str::slug($validatedData['title']);
                
                // Pastikan slug unik
                $originalSlug = $validatedData['slug'];
                $counter = 1;
                while (News::where('slug', $validatedData['slug'])->where('id', '!=', $id)->exists()) {
                    $validatedData['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
            
            // Handle file upload - sama seperti di store method
            if ($request->hasFile('featured_image')) {
                // Hapus gambar lama jika ada
                if ($news->featured_image && file_exists(public_path($news->featured_image))) {
                    unlink(public_path($news->featured_image));
                }
                
                // Upload gambar baru
                $file = $request->file('featured_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/news'), $filename);
                $validatedData['featured_image'] = 'uploads/news/' . $filename;
            }
            
            // Set is_featured default value jika tidak ada
            $validatedData['is_featured'] = $request->has('is_featured') ? 1 : 0;
            
            // Update published_at jika status berubah ke published
            if ($validatedData['status'] === 'published' && $news->status !== 'published') {
                $validatedData['published_at'] = now();
            }
            
            // Update data
            $news->update($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil diperbarui',
                'data' => $news
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui berita'
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $news = News::findOrFail($id);
            
            // Delete associated image
            if ($news->featured_image && Storage::disk('public')->exists(str_replace('storage/', '', $news->featured_image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $news->featured_image));
            }
            
            $news->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus berita: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleFeatured($id)
    {
        try {
            $news = News::findOrFail($id);
            
            DB::beginTransaction();
            
            if ($news->is_featured) {
                // Remove from featured
                $news->update(['is_featured' => false]);
                $message = 'Berita berhasil dihapus dari berita utama';
            } else {
                // Make featured (remove others first)
                News::where('is_featured', true)->update(['is_featured' => false]);
                $news->update(['is_featured' => true]);
                $message = 'Berita berhasil dijadikan berita utama';
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_featured' => $news->is_featured
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}