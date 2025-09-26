<?php
namespace App\Http\Controllers\Gondowangi\AdminController\Berita;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SemuaBeritaController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $categoryFilter = $request->get('category');
        $statusFilter = $request->get('status');
        $search = $request->get('search');

        // Base query with eager loading
        $query = News::with(['category', 'author'])
            ->select('news.*')
            ->leftJoin('news_categories', 'news.category_id', '=', 'news_categories.id')
            ->leftJoin('users', 'news.author_id', '=', 'users.id');

        // Apply filters
        if ($categoryFilter) {
            $query->where('news_categories.category_name', $categoryFilter);
        }

        if ($statusFilter) {
            $query->where('news.status', $statusFilter);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('news.title', 'LIKE', "%{$search}%")
                  ->orWhere('news.excerpt', 'LIKE', "%{$search}%")
                  ->orWhere('news.content', 'LIKE', "%{$search}%")
                  ->orWhere('users.name', 'LIKE', "%{$search}%");
            });
        }

        // Get paginated results
        $news = $query->orderBy('news.created_at', 'desc')->paginate(10);

        // Get statistics
        $stats = $this->getNewsStatistics();

        // Get categories for filter dropdown
        $categories = NewsCategory::where('is_active', true)->get();

        return view('Gondowangi.Admin.Berita.Berita', [
            'title' => 'Semua Berita',
            'news' => $news,
            'categories' => $categories,
            'stats' => $stats,
            'filters' => [
                'category' => $categoryFilter,
                'status' => $statusFilter,
                'search' => $search
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
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:9048',
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
                'id' => $news->id,
                'title' => $news->title,
                'excerpt' => $news->excerpt,
                'content' => $news->content,
                'status' => $news->status,
                'is_featured' => $news->is_featured,
                'category_id' => $news->category_id,
                'thumbnail_url' => $news->thumbnail_url ?? asset('images/default-news.jpg'),
                'formatted_date' => $news->created_at->format('d M Y'),
                'category' => $news->category ? [
                    'id' => $news->category->id,
                    'category_name' => $news->category->category_name
                ] : null,
                'author' => $news->author ? [
                    'id' => $news->author->id,
                    'name' => $news->author->name
                ] : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Berita tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update news (untuk modal edit)
     */
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

    /**
     * Delete news (untuk modal delete)
     */
    public function destroy($id)
    {
        try {
            $news = News::findOrFail($id);
            
            // Hapus file gambar jika ada
            if ($news->featured_image) {
                $imagePath = public_path('storage/' . $news->featured_image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $news->delete();

            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus berita'
            ], 500);
        }
    }

    private function getNewsStatistics()
    {
        $total = News::count();
        $published = News::where('status', 'published')->count();
        $draft = News::where('status', 'draft')->count();
        $thisMonth = News::whereMonth('created_at', Carbon::now()->month)
                         ->whereYear('created_at', Carbon::now()->year)
                         ->count();

        return [
            'total' => $total,
            'published' => $published,
            'draft' => $draft,
            'this_month' => $thisMonth
        ];
    }
}